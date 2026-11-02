<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Create;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleName;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
class Input extends AbstractInput
{
    #[Required]
    #[Length(max: RoleName::LENGTH_LIMIT)]
    public string $name = '';

    /** @var list<string> */
    #[Each(new In(self::PERMISSIONS))]
    public array $permissions = [];

    private const array PERMISSIONS = [
        'view_dashboard',
        'edit_page',
        'view_users',
        'manage_users',
        'view_roles',
        'manage_roles',
    ];

    /**
     * @return list<Permission>
     */
    public function permissionList(): array
    {
        return array_map(static fn(string $p): Permission => Permission::from($p), $this->permissions);
    }
}
