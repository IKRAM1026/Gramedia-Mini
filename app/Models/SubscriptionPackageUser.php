<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPackageUser extends Model
{
    protected $fillable = ['subscription_package_id', 'user_id', 'expired_date'];

    protected function casts(): array
    {
        return [
            'expired_date' => 'date',
        ];
    }
    public function subscriptionPackage(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
