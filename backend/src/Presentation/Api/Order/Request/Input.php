<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Request;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientEmail;
use App\Presentation\Api\Client\Shared\PhoneValidation;
use App\Presentation\Api\Order\Shared\CargoInput;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

/**
 * The public request form: only the phone is required.
 */
#[FromBody]
final class Input extends AbstractInput
{
    use PhoneValidation;
    use CargoInput;

    #[Callback(method: 'validatePhone')]
    public string $phone = '';

    #[Length(max: ClientDetails::NAME_LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $name = '';

    #[Email(message: 'Укажите корректный email.', skipOnEmpty: true)]
    #[Length(max: ClientEmail::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $email = '';

    /**
     * The form has a single name field; it becomes the Client's first name.
     */
    public function clientDetails(): ClientDetails
    {
        return new ClientDetails(trim($this->name), email: ClientEmail::fromOptional($this->email));
    }
}
