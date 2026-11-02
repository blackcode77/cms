<?php

declare(strict_types=1);

namespace App\Application\Role\CreateRole;

use App\Domain\Role\Value\RoleId;

final readonly class Result
{
    public function __construct(
        public RoleId $id,
    ) {}
}
