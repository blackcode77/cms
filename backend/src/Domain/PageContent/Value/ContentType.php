<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

enum ContentType: string
{
    case String = 'string';
    case Text = 'text';
    case Html = 'html';
    case Image = 'image';
    case Icon = 'icon';
    case List = 'list';

    /**
     * Max characters of a value; for `list`, of the whole JSON-encoded list (each Item field also obeys its own type's limit).
     *
     * @return positive-int
     */
    public function maxLength(): int
    {
        return match ($this) {
            self::String => 255,
            self::Text => 5_000,
            self::Html => 50_000,
            self::Image => 255,
            self::Icon => IconName::LENGTH_LIMIT,
            self::List => 200_000,
        };
    }

    /**
     * Whether an Item field of a Content list may have this type.
     */
    public function isItemFieldType(): bool
    {
        return $this !== self::List;
    }
}
