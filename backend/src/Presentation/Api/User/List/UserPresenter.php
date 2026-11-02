<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\List;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\PresenterInterface;
use App\Shared\Read\User\UserItem;
use DateTimeInterface;

/**
 * @implements PresenterInterface<UserItem>
 */
final readonly class UserPresenter implements PresenterInterface
{
    public function present(mixed $value): array
    {
        return [
            'id' => $value->id->toString(),
            'email' => $value->email,
            'name' => $value->name,
            'role' => ['id' => $value->roleId->toString(), 'name' => $value->roleName],
            'must_change_password' => $value->mustChangePassword,
            'created_at' => $value->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}
