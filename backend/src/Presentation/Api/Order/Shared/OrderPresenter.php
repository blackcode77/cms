<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Shared;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\PresenterInterface;
use App\Shared\Read\Order\OrderItem;
use DateTimeInterface;

/**
 * @implements PresenterInterface<OrderItem>
 */
final readonly class OrderPresenter implements PresenterInterface
{
    public function present(mixed $value): array
    {
        return [
            'id' => $value->id->toString(),
            'number' => $value->number,
            'status' => $value->status->value,
            'client' => [
                'id' => $value->clientId->toString(),
                'phone' => $value->clientPhone,
                'first_name' => $value->clientFirstName,
                'last_name' => $value->clientLastName,
                'middle_name' => $value->clientMiddleName,
            ],
            'manager' => $value->managerId === null
                ? null
                : ['id' => $value->managerId->toString(), 'name' => $value->managerName ?? ''],
            'from' => $value->cargo->from,
            'to' => $value->cargo->to,
            'cargo_type' => $value->cargo->cargoType,
            'weight' => $value->cargo->weight,
            'message' => $value->message,
            'comment' => $value->comment,
            'created_at' => $value->createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $value->updatedAt->format(DateTimeInterface::ATOM),
        ];
    }
}
