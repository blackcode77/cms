<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Shared;

use App\Domain\Order\Value\CargoDetails;
use Yiisoft\Validator\Rule\Length;

/**
 * Cargo and message fields shared by every Order body.
 */
trait CargoInput
{
    #[Length(max: CargoDetails::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $from = '';

    #[Length(max: CargoDetails::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $to = '';

    #[Length(max: CargoDetails::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $cargo_type = '';

    #[Length(max: CargoDetails::LENGTH_LIMIT, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $weight = '';

    #[Length(max: 5000, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $message = '';

    public function cargo(): CargoDetails
    {
        return new CargoDetails(trim($this->from), trim($this->to), trim($this->cargo_type), trim($this->weight));
    }
}
