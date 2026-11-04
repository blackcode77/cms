<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Webmozart\Assert\Assert;

/**
 * A partial change of one Content element: a new value (with its type), a new `enabled` flag, or both.
 * A `list` element's value is a {@see ContentList}, every other type's value is a string.
 */
final readonly class ElementPatch
{
    public function __construct(
        public ?ContentType $type,
        public string|ContentList|null $value,
        public ?bool $enabled,
    ) {
        Assert::true($value === null || $type !== null, 'A value needs its type.');
        Assert::true($value !== null || $enabled !== null, 'A patch must change something.');
        Assert::true(
            $value === null || ($type === ContentType::List) === ($value instanceof ContentList),
            'Only a list element has a list value.',
        );
    }

    public function withValue(string|ContentList $value): self
    {
        return new self($this->type, $value, $this->enabled);
    }

    /**
     * @return array{type?: string, value?: string|list<array<string, string>>, enabled?: bool}
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->value !== null && $this->type !== null) {
            $data['type'] = $this->type->value;
            $data['value'] = $this->value instanceof ContentList ? $this->value->items : $this->value;
        }
        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }

        return $data;
    }
}
