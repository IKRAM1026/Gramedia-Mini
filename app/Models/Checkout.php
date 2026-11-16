<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkout extends Model
{
    protected $fillable = [
        'user_id',
        'payment-method',
        'total_book',
        'total_book_price',
        'total_price',
    ];

    public function checkoutbook(): HasMany
    {
        return $this->hasMany(CheckoutBook::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
