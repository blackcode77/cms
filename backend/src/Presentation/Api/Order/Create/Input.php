<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Create;

use App\Presentation\Api\Client\Shared\ClientInput;
use App\Presentation\Api\Order\Shared\OrderEditInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;

/**
 * The Client (by phone, details fill only what is unknown) plus the Order fields.
 */
#[FromBody]
final class Input extends ClientInput
{
    use OrderEditInput;
}
