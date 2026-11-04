<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Shared;

use App\Domain\Client\Value\ClientPhone;
use Yiisoft\Validator\Result;

/**
 * For inputs with a required `phone` validated by `#[Callback(method: 'validatePhone')]`.
 */
trait PhoneValidation
{
    public function validatePhone(mixed $value): Result
    {
        $result = new Result();
        if (!is_string($value) || trim($value) === '') {
            return $result->addError('Укажите номер телефона.');
        }
        if (ClientPhone::tryFrom($value) === null) {
            return $result->addError('Укажите номер телефона с кодом страны, например +7 999 123-45-67.');
        }

        return $result;
    }

    public function phone(): ClientPhone
    {
        return new ClientPhone($this->phone);
    }
}
