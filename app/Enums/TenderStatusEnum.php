<?php

namespace App\Enums;

enum TenderStatusEnum: string
{
    case Open = 'open';
    case Closed = 'closed';
    case InProgress = 'in_progress';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}
