<?php

declare(strict_types=1);

namespace App\Presentation\Api\Page\Create;

use App\Domain\Page\PageSlug;
use App\Domain\Page\PageTitle;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
final class Input extends AbstractInput
{
    #[Required]
    #[Length(max: PageTitle::LENGTH_LIMIT)]
    public string $title = '';

    #[Required]
    #[Length(max: PageSlug::LENGTH_LIMIT)]
    #[Regex(PageSlug::PATTERN, message: 'Slug must contain lowercase latin letters, digits and single dashes.')]
    public string $slug = '';

    public string $content = '';
}
