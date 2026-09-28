<?php

namespace App\Support;

use App\Models\Customer;

class ClientPortal
{
    public static function customer(): ?Customer
    {
        return Customer::query()->where('user_id', auth()->id())->first();
    }
}
