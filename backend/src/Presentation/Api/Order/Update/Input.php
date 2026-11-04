<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Update;

use App\Presentation\Api\Order\Shared\OrderEditInput;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;

/**
 * The Client of an Order cannot be changed.
 */
#[FromBody]
final class Input extends AbstractInput
{
    use OrderEditInput;
}
