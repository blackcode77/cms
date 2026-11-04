<?php

declare(strict_types=1);

namespace App\Domain\Order\Value;

/**
 * Processing stage of an Order. Any status may follow any other.
 */
enum OrderStatus: string
{
    /** Just received, not yet taken. */
    case Request = 'request';
    case InProgress = 'in_progress';
    case Done = 'done';
}
