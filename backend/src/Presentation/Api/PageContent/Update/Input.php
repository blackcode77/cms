<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\Update;

use App\Domain\PageContent\Value\ContentList;
use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ContentType;
use InvalidArgumentException;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Result;

/**
 * `{path, type?, value?, enabled?, fields?}`: either a new value (with its type), a new `enabled` flag, or both.
 * For `type: list` the value is an array of Items and `fields` maps each Item field to its type (ADR 0005).
 */
#[FromBody]
final class Input extends AbstractInput
{
    #[Required]
    #[Length(max: ContentPath::LENGTH_LIMIT)]
    #[Regex(ContentPath::PATTERN, message: 'Path must be dotted lowercase latin words, digits and underscores.')]
    public string $path = '';

    #[Callback(method: 'validateType')]
    public ?string $type = null;

    #[Callback(method: 'validateValue')]
    public string|array|null $value = null;

    public ?array $fields = null;

    public ?bool $enabled = null;

    public function validateType(mixed $type): Result
    {
        $result = new Result();
        if ($type !== null && (!is_string($type) || ContentType::tryFrom($type) === null)) {
            $result->addError('Unknown type.');
        } elseif ($type === null && $this->value !== null) {
            $result->addError('A value needs its type.');
        }

        return $result;
    }

    public function validateValue(mixed $value): Result
    {
        $result = new Result();
        if ($value === null && $this->enabled === null) {
            return $result->addError('Pass a value, "enabled", or both.');
        }
        if ($this->type === ContentType::List->value) {
            try {
                ContentList::fromArray($this->fields, $value);
            } catch (InvalidArgumentException $e) {
                $result->addError($e->getMessage());
            }
        } elseif ($this->fields !== null) {
            $result->addError('Only a list has fields.');
        } elseif ($value !== null && !is_string($value)) {
            $result->addError('Value must be a string.');
        }

        return $result;
    }

    public function contentType(): ?ContentType
    {
        return $this->type === null ? null : ContentType::from($this->type);
    }

    public function contentValue(): string|ContentList|null
    {
        if ($this->value === null) {
            return null;
        }
        if ($this->type === ContentType::List->value) {
            return ContentList::fromArray($this->fields, $this->value);
        }

        /** @var string */
        return $this->value;
    }
}
