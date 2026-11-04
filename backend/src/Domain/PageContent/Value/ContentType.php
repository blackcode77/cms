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
    case Link = 'link';
    case Lines = 'lines';
    case Url = 'url';
    case File = 'file';
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
            self::Link => LinkPath::LENGTH_LIMIT,
            self::Lines => 5_000,
            self::Url => ExternalUrl::LENGTH_LIMIT,
            self::File => 255,
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
