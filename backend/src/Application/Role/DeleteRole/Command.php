<?php

declare(strict_types=1);

namespace App\Application\Role\DeleteRole;

use App\Domain\Role\Value\RoleId;

final readonly class Command
{
    public function __construct(
        public RoleId $id,
    ) {}
}
