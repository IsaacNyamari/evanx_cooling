<?php

namespace App\Support;

/** Length rules for search-result snippets (Google truncates around 60 / 160 characters). */
class SeoAnalyzer
{
    public const TITLE_MIN = 30;

    public const TITLE_MAX = 60;

    public const DESC_MIN = 70;

    public const DESC_MAX = 160;

    /** @return array{status:string,message:string,length:int} status is good | warn | bad */
    public static function title(?string $title): array
    {
        return self::rate(trim((string) $title), self::TITLE_MIN, self::TITLE_MAX, 'title');
    }

    /** @return array{status:string,message:string,length:int} */
    public static function description(?string $description): array
    {
        return self::rate(trim((string) $description), self::DESC_MIN, self::DESC_MAX, 'description');
    }

    private static function rate(string $text, int $min, int $max, string $what): array
    {
        $length = mb_strlen($text);

        return match (true) {
            $length === 0 => ['status' => 'bad', 'length' => 0, 'message' => "Missing {$what}"],
            $length > $max + 10 => ['status' => 'bad', 'length' => $length, 'message' => "Too long: Google will cut it off after about {$max} characters"],
            $length > $max => ['status' => 'warn', 'length' => $length, 'message' => "A little long: the end may be cut off (aim for {$max} or fewer)"],
            $length < $min => ['status' => 'warn', 'length' => $length, 'message' => "Short: use up to {$max} characters to describe the page better"],
            default => ['status' => 'good', 'length' => $length, 'message' => 'Good length'],
        };
    }

    /** Worst of several ratings. */
    public static function worst(array ...$ratings): string
    {
        $order = ['good' => 0, 'warn' => 1, 'bad' => 2];

        return collect($ratings)->map(fn ($r) => $r['status'])->sortByDesc(fn ($s) => $order[$s])->first() ?? 'good';
    }
}
