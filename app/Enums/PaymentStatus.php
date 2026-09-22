<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Due = 'due';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case Refunded = 'refunded';

}
