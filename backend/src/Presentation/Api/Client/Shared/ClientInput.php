<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Shared;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientEmail;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

/**
 * Body of creating or editing a Client in the admin area (also the Client part of a manually created Order).
 */
abstract class ClientInput extends AbstractInput
{
    use PhoneValidation;

    #[Callback(method: 'validatePhone')]
    public string $phone = '';

    #[Length(max: ClientDetails::NAME_LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $first_name = '';

    #[Length(max: ClientDetails::NAME_LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $last_name = '';

    #[Length(max: ClientDetails::NAME_LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $middle_name = '';

    #[Email(message: 'Укажите корректный email.', skipOnEmpty: true)]
    #[Length(max: ClientEmail::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $email = '';

    public function details(): ClientDetails
    {
        return new ClientDetails(
            trim($this->first_name),
            trim($this->last_name),
            trim($this->middle_name),
            ClientEmail::fromOptional($this->email),
        );
    }
}
