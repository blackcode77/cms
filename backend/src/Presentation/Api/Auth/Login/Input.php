<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Login;

use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
final class Input extends AbstractInput
{
    #[Required]
    public string $email = '';

    #[Required]
    public string $password = '';
}
