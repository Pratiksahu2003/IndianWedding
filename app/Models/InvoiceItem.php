<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'unit_amount' => 'integer',
            'quantity' => 'integer',
            'gst_rate' => 'integer',
            'taxable_amount' => 'integer',
            'cgst_amount' => 'integer',
            'sgst_amount' => 'integer',
            'igst_amount' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
