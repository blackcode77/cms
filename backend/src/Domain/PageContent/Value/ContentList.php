<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Value of a Content list: ordered Items, each an `id` plus string values of declared fields (ADR 0005).
 * Field types come with the change because the backend does not know the frontend schema; they only decide
 * how each field is validated and sanitized. Fields an Item omits are simply absent.
 */
final readonly class ContentList
{
    public const int MAX_ITEMS = 100;
    public const string FIELD_PATTERN = '/^[a-z][a-z0-9_]{0,63}$/';
    public const string ID_PATTERN = '/^[A-Za-z0-9_-]{1,64}$/';

    /**
     * @param array<string, ContentType> $fields
     * @param list<array<string, string>> $items Each has an `id`.
     */
    private function __construct(
        public array $fields,
        public array $items,
    ) {}

    /**
     * Builds a list from request data, checking its shape (not the per-type rules, see {@see self::MAX_ITEMS}).
     *
     * @throws InvalidArgumentException With a message fit for the client.
     */
    public static function fromArray(mixed $fields, mixed $items): self
    {
        Assert::isMap($fields, 'Fields must be an object of field name to type.');
        $types = [];
        foreach ($fields as $name => $type) {
            Assert::regex($name, self::FIELD_PATTERN, 'Field names must be lowercase latin words, digits and underscores.');
            Assert::notEq($name, 'id', 'The field "id" is reserved.');
            $contentType = is_string($type) ? ContentType::tryFrom($type) : null;
            Assert::true($contentType?->isItemFieldType() === true, sprintf('Unknown type of field "%s".', $name));
            /** @var ContentType $contentType */
            $types[$name] = $contentType;
        }

        Assert::isList($items, 'Value of a list must be an array of Items.');
        $ids = [];
        $result = [];
        foreach ($items as $item) {
            Assert::isMap($item, 'Each Item must be an object.');
            Assert::keyExists($item, 'id', 'Each Item needs an id.');
            $clean = [];
            foreach ($item as $name => $value) {
                Assert::true($name === 'id' || isset($types[$name]), sprintf('Field "%s" is not declared.', $name));
                Assert::string($value, sprintf('Field "%s" must be a string.', $name));
                $clean[$name] = $value;
            }
            Assert::regex($clean['id'], self::ID_PATTERN, 'Item id must be 1-64 latin letters, digits, "-" or "_".');
            Assert::keyNotExists($ids, $clean['id'], 'Item ids must be unique.');
            $ids[$clean['id']] = true;
            $result[] = $clean;
        }

        return new self($types, $result);
    }

    /**
     * A copy with every field value replaced by `$map(type, value)`; ids are kept.
     *
     * @param callable(ContentType, string): string $map
     */
    public function mapValues(callable $map): self
    {
        $items = [];
        foreach ($this->items as $item) {
            foreach ($item as $name => $value) {
                if ($name !== 'id') {
                    $item[$name] = $map($this->fields[$name], $value);
                }
            }
            $items[] = $item;
        }

        return new self($this->fields, $items);
    }
}
