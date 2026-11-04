<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Shared;

use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;
use Yiisoft\Validator\Rule\InEnum;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Uuid;

/**
 * Fields a User sets on an Order in the admin area.
 */
trait OrderEditInput
{
    use CargoInput;

    #[InEnum(OrderStatus::class, message: 'Неизвестный статус заказа.')]
    public string $status = 'request';

    #[Uuid(skipOnEmpty: true)]
    public ?string $manager_id = null;

    #[Length(max: 5000, greaterThanMaxMessage: 'Не длиннее {max} символов.')]
    public string $comment = '';

    public function orderStatus(): OrderStatus
    {
        return OrderStatus::from($this->status);
    }

    public function managerId(): ?UserId
    {
        return $this->manager_id === null || $this->manager_id === '' ? null : UserId::fromString($this->manager_id);
    }
}
