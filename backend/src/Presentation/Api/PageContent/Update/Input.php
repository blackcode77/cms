<?php

declare(strict_types=1);

namespace App\Presentation\Api\PageContent\Update;

use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ContentType;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Result;

/**
 * `{path, type?, value?, enabled?}`: either a new value (with its type), a new `enabled` flag, or both.
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
    public ?string $value = null;

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
            $result->addError('Pass a value, "enabled", or both.');
        }

        return $result;
    }

    public function contentType(): ?ContentType
    {
        return $this->type === null ? null : ContentType::from($this->type);
    }
}
