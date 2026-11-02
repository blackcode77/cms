<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\Create;

use App\Domain\User\Value\UserEmail;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Uuid;

#[FromBody]
final class Input extends AbstractInput
{
    #[Required]
    #[Email]
    #[Length(max: UserEmail::LENGTH_LIMIT)]
    public string $email = '';

    #[Required]
    #[Length(max: 255)]
    public string $name = '';

    #[Required]
    #[Uuid]
    public string $role_id = '';
}
