<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\ChangePassword;

use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
final class Input extends AbstractInput
{
    #[Required]
    public string $current_password = '';

    #[Required]
    #[Length(min: 8, max: 255)]
    public string $new_password = '';
}
