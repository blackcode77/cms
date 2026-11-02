<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Update;

use App\Presentation\Api\Role\Create\Input as CreateInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;

/**
 * Same body as creating a Role.
 */
#[FromBody]
final class Input extends CreateInput {}
