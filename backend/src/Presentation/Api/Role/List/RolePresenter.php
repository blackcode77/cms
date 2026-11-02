<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\List;

use App\Domain\Role\Value\Permission;
use App\Presentation\Api\Shared\ResponseFactory\Presenter\PresenterInterface;
use App\Shared\Read\Role\RoleItem;

/**
 * @implements PresenterInterface<RoleItem>
 */
final readonly class RolePresenter implements PresenterInterface
{
    public function present(mixed $value): array
    {
        return [
            'id' => $value->id->toString(),
            'name' => $value->name->toString(),
            'permissions' => array_map(static fn(Permission $p): string => $p->value, $value->permissions),
            'users_count' => $value->usersCount,
            'read_only' => $value->isReadOnly(),
            'deletable' => $value->isDeletable(),
        ];
    }
}
