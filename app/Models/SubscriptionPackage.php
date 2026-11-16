<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPackage extends Model
{
    protected $fillable = ['nama_package', 'description', 'price', 'color'];

    public function subscriptionsPackageUsers(): HasMany
    {
        return $this->hasMany(SubscriptionPackageUser::class);
    }

    public function subscriptionsPackageBook(): HasMany
    {
        return $this->hasMany(SubscriptionPackageBook::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'subscription_package_books');
    }
}
