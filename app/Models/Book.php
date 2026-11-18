<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['cover', 'title', 'price', 'description', 'language',
    'publisher', 'writer', 'release_date', 'page_of_book', 'book_category_id'])]
class Book extends Model
{
    // nama tanggal karena book_categories berperan sebagai one dalam model
    public function bookCategory(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class);
    }

    public function checkoutbook(): HasMany
    {
        return $this->hasMany(CheckoutBook::class);
    }

    public function subscriptionsPackageBook(): HasMany
    {
        return $this->hasMany(SubscriptionPackageBook::class);
    }

    public function subscriptionPackageBooks(): HasMany
    {
        return $this->hasMany(SubscriptionPackageBook::class);
    }

    public function subscriptionPackages(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPackage::class, 'subscription_package_books');
    }
}
