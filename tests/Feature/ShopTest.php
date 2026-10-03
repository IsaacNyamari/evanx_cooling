<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\Admin\CategoryForm;
use App\Livewire\Admin\CategoryIndex;
use App\Livewire\Admin\ProductForm;
use App\Livewire\Admin\ProductIndex;
use App\Livewire\Shop\ProductList;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_imports_and_rebrands_catalogue(): void
    {
        $this->seed(ShopSeeder::class);

        $this->assertSame(24, Category::count());
        $this->assertSame(195, Product::count());
        $this->assertSame(0, Product::where('description', 'like', '%coolmas%')->count());
        // URL-safe slugs and file names (no % escapes or unicode).
        Product::all()->each(function ($p) {
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $p->slug);
            $this->assertMatchesRegularExpression('/^products\/[A-Za-z0-9._-]+$/', $p->image);
        });
        // Fresh server: seeding links to the committed public/uploads copies, no remote URLs.
        $this->assertSame(0, Product::where('image', 'like', 'http%')->count());
        Product::all()->each(fn ($p) => $this->assertFileExists(public_path('uploads/'.$p->image)));
        $this->assertGreaterThan(195, \DB::table('category_product')->count());
    }

    public function test_public_shop_pages_render(): void
    {
        $this->seed(ShopSeeder::class);

        $this->get('/shop')->assertOk()->assertSee('Shop');
        $this->get('/shop?category=compressors')->assertOk();
        $this->get('/shop/flare-nuts')->assertOk()->assertSee('Flare Nuts');
        $this->get('/shop/does-not-exist')->assertNotFound();
    }

    public function test_admin_routes_require_login(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/admin/categories')->assertRedirect('/login');
    }

    public function test_shop_list_filters_and_searches(): void
    {
        $this->seed(ShopSeeder::class);

        Livewire::test(ProductList::class)
            ->assertViewHas('products', fn ($p) => $p->total() === 195)
            ->set('q', 'flare')
            ->assertViewHas('products', fn ($p) => $p->total() > 0 && $p->total() < 195)
            ->call('clearFilters')
            ->call('selectCategory', 'compressors')
            ->assertViewHas('current', fn ($c) => $c->slug === 'compressors');
    }

    public function test_category_and_product_crud(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CategoryForm::class)
            ->set('name', 'Test Cat')
            ->assertSet('slug', 'test-cat')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.categories.index'));
        $category = Category::firstWhere('slug', 'test-cat');
        $this->assertNotNull($category);

        Livewire::test(ProductForm::class)
            ->set('name', 'Test Part')->set('currency', 'kes')->set('price', 1500)
            ->set('categoryIds', [(string) $category->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.products.index'));
        $product = Product::firstWhere('slug', 'test-part');
        $this->assertSame('KES', $product->currency);
        $this->assertTrue($product->categories->contains($category));

        $this->get('/admin/products')->assertOk();
        $this->get("/admin/products/{$product->id}/edit")->assertOk()->assertSee('Test Part');
        $this->get('/admin/categories/create')->assertOk();

        Livewire::test(ProductForm::class, ['product' => $product])
            ->set('name', 'Renamed')->set('categoryIds', [])
            ->call('save')->assertHasNoErrors();
        $this->assertSame('Renamed', $product->fresh()->name);
        $this->assertCount(0, $product->fresh()->categories);

        Livewire::test(ProductForm::class)->set('name', '')->call('save')->assertHasErrors(['name' => 'required']);

        Livewire::test(ProductIndex::class)->set('q', 'Renamed')->assertSee('Renamed')
            ->call('toggleActive', $product->id)->call('delete', $product->id);
        Livewire::test(CategoryIndex::class)->call('delete', $category->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_uploaded_images_are_saved_to_public_uploads(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ProductForm::class)
            ->set('name', 'Upload Test Product')
            ->set('image_file', UploadedFile::fake()->image('part.jpg', 600, 400))
            ->call('save')->assertHasNoErrors();
        $product = Product::firstWhere('slug', 'upload-test-product');
        $this->assertStringStartsWith('products/', $product->image);
        $this->assertFileExists(public_path('uploads/'.$product->image));
        $this->assertStringContainsString('/uploads/products/', $product->imageUrl());

        Livewire::test(CategoryForm::class)
            ->set('name', 'Upload Test Category')
            ->set('image_file', UploadedFile::fake()->image('cat.png'))
            ->call('save')->assertHasNoErrors();
        $category = Category::firstWhere('slug', 'upload-test-category');
        $this->assertFileExists(public_path('uploads/'.$category->image));

        // Replacing the image removes the old file; deleting the record removes the file.
        $old = public_path('uploads/'.$product->image);
        Livewire::test(ProductForm::class, ['product' => $product])
            ->set('image_file', UploadedFile::fake()->image('new.jpg'))
            ->call('save')->assertHasNoErrors();
        $this->assertFileDoesNotExist($old);
        $new = public_path('uploads/'.$product->fresh()->image);
        $this->assertFileExists($new);

        $catFile = public_path('uploads/'.$category->image);
        Livewire::test(ProductIndex::class)->call('delete', $product->id);
        Livewire::test(CategoryIndex::class)->call('delete', $category->id);
        $this->assertFileDoesNotExist($new);
        $this->assertFileDoesNotExist($catFile);
    }

    public function test_non_image_upload_is_rejected_and_nothing_is_saved(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ProductForm::class)
            ->set('name', 'Bad Upload')
            ->set('image_file', UploadedFile::fake()->create('evil.php', 10, 'text/x-php'))
            ->assertHasErrors('image_file')->assertSet('image_file', null);
        $this->assertDatabaseMissing('products', ['name' => 'Bad Upload']);
    }
}
