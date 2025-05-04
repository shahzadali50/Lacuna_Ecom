<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProductLog extends Model
{
    protected $fillable = [
        'note',
        'product_name',
        'purchase_id',
        'user_id',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
