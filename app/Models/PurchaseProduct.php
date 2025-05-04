<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_price',
        'sale_price',
        'stock',
        'remaining_stock',
        'status',
        'product_id',
        'user_id',
        'description',
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
