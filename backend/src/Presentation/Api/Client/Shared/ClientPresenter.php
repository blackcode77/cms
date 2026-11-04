<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Shared;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\PresenterInterface;
use App\Shared\Read\Client\ClientItem;
use DateTimeInterface;

/**
 * @implements PresenterInterface<ClientItem>
 */
final readonly class ClientPresenter implements PresenterInterface
{
    public function present(mixed $value): array
    {
        return [
            'id' => $value->id->toString(),
            'phone' => $value->phone,
            'first_name' => $value->firstName,
            'last_name' => $value->lastName,
            'middle_name' => $value->middleName,
            'email' => $value->email,
            'comment' => $value->comment,
            'orders_count' => $value->ordersCount,
            'created_at' => $value->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}
