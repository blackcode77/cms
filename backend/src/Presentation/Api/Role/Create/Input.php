<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Create;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleName;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
class Input extends AbstractInput
{
    #[Required]
    #[Length(max: RoleName::LENGTH_LIMIT)]
    public string $name = '';

    /** @var list<string> */
    #[Callback(method: 'validatePermissions')]
    public array $permissions = [];

    /**
     * Strict whitelist built from the Permission catalog (non-strings such as `true` must not reach Permission::from).
     */
    public function validatePermissions(mixed $value): Result
    {
        $result = new Result();
        if (!is_array($value)) {
            return $result->addError('Permissions must be a list.');
        }
        foreach ($value as $permission) {
            if (!is_string($permission) || Permission::tryFrom($permission) === null) {
                $result->addError('Unknown permission.');
                break;
            }
        }

        return $result;
    }

    /**
     * @return list<Permission>
     */
    public function permissionList(): array
    {
        return array_map(static fn(string $p): Permission => Permission::from($p), $this->permissions);
    }
}
