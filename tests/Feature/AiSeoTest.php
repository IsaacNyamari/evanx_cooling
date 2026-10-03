<?php

namespace Tests\Feature;

use App\Livewire\Admin\ProductForm;
use App\Livewire\Admin\ProductIndex;
use App\Livewire\Admin\Seo\Products;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AiSeoTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'test-gemini-key-123456';

    private const URL = 'generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => self::KEY, 'services.gemini.models' => ['gemini-flash-lite-latest', 'gemini-flash-latest']]);
        $this->actingAs(User::factory()->create());
    }

    private function gemini(string $title, string $description): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => json_encode(['title' => $title, 'description' => $description])]]], 'finishReason' => 'STOP']]];
    }

    private function good(): array
    {
        return $this->gemini(
            'Copper Flare Nuts Kenya | Evanx Cooling Systems',
            'Brass flare nuts for leak-proof copper pipe joints in air conditioning and cold room lines. Available in Nairobi, Kenya. Order on WhatsApp.'
        );
    }

    private function form(): \Livewire\Features\SupportTesting\Testable
    {
        $category = Category::firstOrCreate(['slug' => 'hvac-tools'], ['name' => 'HVAC Tools']);

        return Livewire::test(ProductForm::class)
            ->set('name', 'Flare Nuts')
            ->set('sku', 'FN-1')
            ->set('short_description', '<p>Flare nuts join <strong>copper pipes</strong> in air conditioners.</p>')
            ->set('categoryIds', [(string) $category->id]);
    }

    public function test_generate_writes_title_and_description_into_the_form(): void
    {
        Http::fake([self::URL => Http::response($this->good())]);

        $this->form()->call('generateSeo')
            ->assertSet('meta_title', 'Copper Flare Nuts Kenya | Evanx Cooling Systems')
            ->assertSet('meta_description', 'Brass flare nuts for leak-proof copper pipe joints in air conditioning and cold room lines. Available in Nairobi, Kenya. Order on WhatsApp.')
            ->assertSet('aiError', null)
            ->assertSee('Written by AI');

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            $prompt = $request['contents'][0]['parts'][0]['text'];

            return str_contains($request->url(), '/models/gemini-flash-lite-latest:generateContent')
                && $request->hasHeader('x-goog-api-key', self::KEY)
                && ! str_contains($request->url(), self::KEY)               // key goes in a header, never the URL
                && str_contains($prompt, 'Name: Flare Nuts')
                && str_contains($prompt, 'Category: HVAC Tools')
                && str_contains($prompt, 'SKU: FN-1')
                && str_contains($prompt, 'Flare nuts join copper pipes')    // HTML stripped
                && ! str_contains($prompt, '<strong>')
                && str_contains($prompt, 'at most 60 characters')
                && str_contains($prompt, 'Kenya')
                && $request['generationConfig']['responseMimeType'] === 'application/json';
        });
    }

    public function test_text_from_the_model_is_cleaned(): void
    {
        Http::fake([self::URL => Http::response($this->gemini(
            "  \"Flare Nuts <b>Kenya</b> | Evanx Cooling\"  ",
            "Brass flare nuts for\nAC lines in Nairobi, Kenya, built to seal tight and resist vibration on every install. Order on WhatsApp today."
        ))]);

        $this->form()->call('generateSeo')
            ->assertSet('meta_title', 'Flare Nuts Kenya | Evanx Cooling')
            ->assertSet('meta_description', 'Brass flare nuts for AC lines in Nairobi, Kenya, built to seal tight and resist vibration on every install. Order on WhatsApp today.');
    }

    public function test_json_wrapped_in_a_markdown_fence_is_accepted(): void
    {
        $inner = json_encode(['title' => 'Flare Nuts Kenya | Evanx Cooling', 'description' => 'Brass flare nuts for leak-proof copper pipe joints in air conditioning and cold room lines. Available in Nairobi, Kenya. Order on WhatsApp.']);
        Http::fake([self::URL => Http::response(['candidates' => [['content' => ['parts' => [['text' => "```json\n{$inner}\n```"]]]]]])]);

        $this->form()->call('generateSeo')->assertSet('meta_title', 'Flare Nuts Kenya | Evanx Cooling');
    }

    public function test_too_long_answer_is_retried_once_with_feedback(): void
    {
        Http::fake([self::URL => Http::sequence()
            ->push($this->gemini(str_repeat('Very long title ', 6), str_repeat('Too long description. ', 12)))
            ->push($this->good())]);

        $this->form()->call('generateSeo')->assertSet('meta_title', 'Copper Flare Nuts Kenya | Evanx Cooling Systems');

        Http::assertSentCount(2);
        $requests = Http::recorded();
        $second = $requests[1][0]['contents'][0]['parts'][0]['text'];
        $this->assertStringContainsString('previous answer was rejected', $second);
        $this->assertStringContainsString('title was 95 characters', $second);
    }

    public function test_if_it_is_still_too_long_it_is_trimmed_to_fit(): void
    {
        $long = $this->gemini(str_repeat('Flare nuts for AC ', 7), str_repeat('Brass flare nuts sealing copper pipes. ', 8));
        Http::fake([self::URL => Http::sequence()->push($long)->push($long)]);

        $component = $this->form()->call('generateSeo');

        $this->assertLessThanOrEqual(60, mb_strlen($component->get('meta_title')));
        $this->assertLessThanOrEqual(160, mb_strlen($component->get('meta_description')));
        $this->assertNotEmpty($component->get('meta_title'));
    }

    public function test_failures_show_a_friendly_message_and_leave_the_fields_alone(): void
    {
        $cases = [
            'rate limit' => [Http::response(['error' => ['message' => 'quota']], 429), 'free quota is used up'],
            'bad key' => [Http::response(['error' => ['message' => 'API key not valid. Please pass a valid API key.']], 400), 'rejected the API key'],
            'forbidden' => [Http::response(['error' => ['message' => 'denied']], 403), 'rejected the API key'],
            'unknown model' => [Http::response(['error' => ['message' => 'models/x is not found']], 404), 'GEMINI_MODEL'],
            'server error' => [Http::response('oops', 503), 'having problems'],
            'blocked' => [Http::response(['promptFeedback' => ['blockReason' => 'SAFETY']]), 'declined to write'],
            'empty' => [Http::response(['candidates' => []]), 'returned nothing'],
            'not json' => [Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Sorry, I cannot do that']]]]]]), 'unexpected format'],
            'missing keys' => [Http::response(['candidates' => [['content' => ['parts' => [['text' => '{"title":"Only a title"}']]]]]]), 'unexpected format'],
            'offline' => [fn () => throw new ConnectionException('cURL error 6'), 'Could not reach Gemini'],
        ];

        foreach ($cases as $name => [$response, $expected]) {
            Http::swap(new \Illuminate\Http\Client\Factory); // Http::fake() keeps the first stub, so start fresh each time
            Http::fake([self::URL => $response]);

            $component = $this->form()->set('meta_title', 'My own title')->set('meta_description', 'My own description')
                ->call('generateSeo')
                ->assertSet('meta_title', 'My own title')
                ->assertSet('meta_description', 'My own description');
            $this->assertStringContainsString($expected, html_entity_decode($component->html(), ENT_QUOTES), $name);
            \Illuminate\Support\Facades\RateLimiter::clear('seo-ai:'.auth()->id());
        }
    }

    public function test_the_api_key_never_appears_in_pages_or_error_messages(): void
    {
        Http::fake([self::URL => Http::response(['error' => ['message' => 'bad '.self::KEY]], 500)]);

        $html = $this->form()->call('generateSeo')->html();
        $this->assertStringNotContainsString(self::KEY, $html);

        $this->assertStringNotContainsString(self::KEY, $this->get('/admin/products/create')->getContent());
        $this->assertStringNotContainsString(self::KEY, $this->get('/admin/seo/products')->getContent());
    }

    public function test_next_model_is_used_when_the_first_is_out_of_quota(): void
    {
        Http::fake([self::URL => Http::sequence()
            ->push(['error' => ['message' => 'quota exceeded']], 429)
            ->push($this->good())]);

        $this->form()->call('generateSeo')
            ->assertSet('meta_title', 'Copper Flare Nuts Kenya | Evanx Cooling Systems')
            ->assertSet('aiError', null);

        $urls = collect(Http::recorded())->map(fn ($pair) => $pair[0]->url())->all();
        $this->assertCount(2, $urls);
        $this->assertStringContainsString('/models/gemini-flash-lite-latest:', $urls[0]);
        $this->assertStringContainsString('/models/gemini-flash-latest:', $urls[1]);
    }

    public function test_a_rejected_api_key_does_not_try_the_other_models(): void
    {
        Http::fake([self::URL => Http::response(['error' => ['message' => 'API key not valid']], 400)]);

        $this->form()->call('generateSeo')->assertSee('rejected the API key');

        Http::assertSentCount(1);
    }

    public function test_when_every_model_fails_the_last_problem_is_reported(): void
    {
        Http::fake([self::URL => Http::response(['error' => ['message' => 'models/x is not found']], 404)]);

        $this->form()->call('generateSeo')->assertSee('gemini-flash-latest')->assertSee('GEMINI_MODELS');

        Http::assertSentCount(2);
    }

    public function test_the_default_model_list_is_the_free_flash_family(): void
    {
        $this->assertSame(['gemini-flash-lite-latest', 'gemini-flash-latest'], array_slice(config('services.gemini.models'), 0, 2) ?: ['gemini-flash-lite-latest', 'gemini-flash-latest']);
    }

    public function test_a_product_name_is_required_before_asking_ai(): void
    {
        Http::fake();

        Livewire::test(ProductForm::class)->call('generateSeo')->assertSee('Enter the product name first');

        Http::assertNothingSent();
    }

    public function test_without_an_api_key_the_button_is_disabled_and_nothing_is_sent(): void
    {
        config(['services.gemini.key' => null]);
        Http::fake();

        $this->get('/admin/products/create')->assertOk()->assertSee('GEMINI_API_KEY');

        $this->form()->assertSee('Not set up')->call('generateSeo')->assertSee('not set up yet');
        Http::assertNothingSent();
    }

    public function test_button_is_present_when_the_key_is_set_on_new_and_existing_products(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::first();

        $this->get('/admin/products/create')->assertSee('Generate with AI')->assertDontSee('Not set up');
        $this->get("/admin/products/{$product->id}/edit")->assertSee('Generate with AI');
    }

    public function test_seo_screen_generates_from_the_saved_product(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::firstWhere('slug', 'flare-nuts');
        Http::fake([self::URL => Http::response($this->good())]);

        Livewire::test(Products::class)
            ->call('edit', $product->id)
            ->call('generateSeo')
            ->assertSet('meta_title', 'Copper Flare Nuts Kenya | Evanx Cooling Systems')
            ->assertSee('Written by AI');

        Http::assertSent(fn (Request $r) => str_contains($r['contents'][0]['parts'][0]['text'], 'Name: Flare Nuts')
            && str_contains($r['contents'][0]['parts'][0]['text'], 'Category: HVAC Tools'));

        // Nothing is saved until the admin presses Save.
        $this->assertNull($product->fresh()->meta_title);
    }

    public function test_generated_text_is_only_saved_when_the_form_is_saved(): void
    {
        Http::fake([self::URL => Http::response($this->good())]);

        $this->form()->call('generateSeo')->call('save')->assertHasNoErrors();

        $product = Product::firstWhere('slug', 'flare-nuts');
        $this->assertSame('Copper Flare Nuts Kenya | Evanx Cooling Systems', $product->meta_title);
        $this->assertStringContainsString('Order on WhatsApp', $product->meta_description);

        // ...and it is what Google gets.
        $this->assertStringContainsString('<title>Copper Flare Nuts Kenya | Evanx Cooling Systems</title>', $this->get(route('shop.show', 'flare-nuts'))->getContent());
    }

    public function test_one_click_on_a_list_row_generates_and_saves(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::firstWhere('slug', 'flare-nuts');
        Http::fake([self::URL => Http::response($this->good())]);

        Livewire::test(ProductIndex::class)
            ->set('q', 'Flare Nuts')
            ->call('quickSeo', $product->id)
            ->assertSet("seoErrors.{$product->id}", null)
            ->assertSee('Done')
            ->assertSee('Redo');

        $product->refresh();
        $this->assertSame('Copper Flare Nuts Kenya | Evanx Cooling Systems', $product->meta_title);
        $this->assertStringContainsString('Order on WhatsApp', $product->meta_description);

        // The prompt used the saved product's own details, and it is live for Google straight away.
        Http::assertSent(fn (Request $r) => str_contains($r['contents'][0]['parts'][0]['text'], 'Name: Flare Nuts')
            && str_contains($r['contents'][0]['parts'][0]['text'], 'Category: HVAC Tools'));
        $this->assertStringContainsString('<title>Copper Flare Nuts Kenya | Evanx Cooling Systems</title>', $this->get(route('shop.show', 'flare-nuts'))->getContent());
    }

    public function test_row_shows_the_error_and_changes_nothing_when_ai_fails(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::firstWhere('slug', 'flare-nuts');
        $other = Product::where('id', '!=', $product->id)->first();
        Http::fake([self::URL => Http::response(['error' => ['message' => 'quota']], 429)]);

        Livewire::test(ProductIndex::class)->set('q', 'Flare Nuts')
            ->call('quickSeo', $product->id)
            ->assertSet("seoDone.{$product->id}", null)
            ->assertSee('free quota is used up');

        $this->assertNull($product->fresh()->meta_title);
        $this->assertNull($other->fresh()->meta_title);
    }

    public function test_list_buttons_are_disabled_without_a_key_and_nothing_is_sent(): void
    {
        $this->seed(ShopSeeder::class);
        config(['services.gemini.key' => null]);
        Http::fake();

        $this->get('/admin/products')->assertOk()->assertSee('Add GEMINI_API_KEY to the .env file to enable AI SEO');

        $product = Product::orderBy('name')->first(); // first row of the alphabetical list
        Livewire::test(ProductIndex::class)->call('quickSeo', $product->id)->assertSee('not set up yet');
        $this->assertNull($product->fresh()->meta_title);
        Http::assertNothingSent();
    }

    public function test_rows_ask_before_replacing_existing_seo_text_and_label_the_state(): void
    {
        $this->seed(ShopSeeder::class);
        Product::firstWhere('slug', 'flare-nuts')->update(['meta_title' => 'My title', 'meta_description' => 'My description']);

        $html = $this->get('/admin/products?q=Flare+Nuts')->assertOk()->getContent();
        $this->assertStringContainsString('Replace the current SEO title and description', $html);
        $this->assertStringContainsString('Custom', $html);

        $fresh = $this->get('/admin/products?q=Armaflex')->getContent();
        $this->assertStringContainsString('AI SEO', $fresh);
        $this->assertStringNotContainsString('Replace the current SEO title', $fresh);
        $this->assertStringContainsString('Auto', $fresh);
    }

    public function test_one_click_also_works_from_the_seo_products_table_and_syncs_the_editor(): void
    {
        $this->seed(ShopSeeder::class);
        $product = Product::firstWhere('slug', 'flare-nuts');
        Http::fake([self::URL => Http::response($this->good())]);

        Livewire::test(Products::class)
            ->call('edit', $product->id)
            ->call('quickSeo', $product->id)
            ->assertSet('meta_title', 'Copper Flare Nuts Kenya | Evanx Cooling Systems')
            ->assertSet('meta_description', fn ($v) => str_contains($v, 'Order on WhatsApp'));

        $this->assertSame('Copper Flare Nuts Kenya | Evanx Cooling Systems', $product->fresh()->meta_title);
    }

    public function test_one_click_counts_towards_the_rate_limit(): void
    {
        $this->seed(ShopSeeder::class);
        $ids = Product::limit(13)->pluck('id');
        Http::fake([self::URL => Http::response($this->good())]);

        $component = Livewire::test(ProductIndex::class);
        foreach ($ids as $id) {
            $component->call('quickSeo', $id);
        }

        $component->assertSee('a lot of requests');
        Http::assertSentCount(12);
        $this->assertSame(12, Product::whereNotNull('meta_title')->count());
    }

    public function test_requests_are_rate_limited_per_admin(): void
    {
        Http::fake([self::URL => Http::response($this->good())]);
        $component = $this->form();

        foreach (range(1, 12) as $i) {
            $component->call('generateSeo')->assertSet('aiError', null);
        }
        $component->call('generateSeo')->assertSee('a lot of requests');

        Http::assertSentCount(12);
    }
}
