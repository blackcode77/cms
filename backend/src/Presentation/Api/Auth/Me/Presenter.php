<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Me;

use App\Application\Auth\CurrentUser;

final readonly class Presenter
{
    /**
     * @return array<string, mixed>
     */
    public function present(CurrentUser $current): array
    {
        return [
            'user' => [
                'id' => $current->user->id->toString(),
                'email' => $current->user->email->toString(),
                'name' => $current->user->name,
            ],
            'role' => [
                'id' => $current->role->id->toString(),
                'name' => $current->role->name->toString(),
            ],
            'permissions' => array_map(
                static fn($permission): string => $permission->value,
                $current->role->permissions,
            ),
            'must_change_password' => $current->user->mustChangePassword,
        ];
    }
}
