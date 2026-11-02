<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Webmozart\Assert\Assert;

/**
 * A partial change of one Content element: a new value (with its type), a new `enabled` flag, or both.
 */
final readonly class ElementPatch
{
    public function __construct(
        public ?ContentType $type,
        public ?string $value,
        public ?bool $enabled,
    ) {
        Assert::true($value === null || $type !== null, 'A value needs its type.');
        Assert::true($value !== null || $enabled !== null, 'A patch must change something.');
    }

    /**
     * @return array{type?: string, value?: string, enabled?: bool}
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->value !== null && $this->type !== null) {
            $data['type'] = $this->type->value;
            $data['value'] = $this->value;
        }
        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }

        return $data;
    }
}
