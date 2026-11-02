<?php

declare(strict_types=1);

namespace App\Shared\Read\User;

use App\Domain\Role\Value\RoleId;

/**
 * A Role a User can be given (everything except Visiter), for the Users form.
 */
final readonly class RoleOption
{
    public function __construct(
        public RoleId $id,
        public string $name,
    ) {}
}
