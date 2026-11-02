<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

enum ContentType: string
{
    case String = 'string';
    case Text = 'text';
    case Html = 'html';
    case Image = 'image';

    /**
     * @return positive-int
     */
    public function maxLength(): int
    {
        return match ($this) {
            self::String => 255,
            self::Text => 5_000,
            self::Html => 50_000,
            self::Image => 255,
        };
    }
}
