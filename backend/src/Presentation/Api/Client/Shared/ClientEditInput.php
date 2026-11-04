<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Shared;

use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Length;

/**
 * Body of creating or editing a Client.
 */
#[FromBody]
final class ClientEditInput extends ClientInput
{
    #[Length(max: 5000, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $comment = '';
}
