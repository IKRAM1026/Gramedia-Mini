<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;


#[Fillable(['book_id', 'subscription_package_id', 'user_id'])] 


class Cart extends Model
{
 
    public function book()
    {   //BelongsTo : memakai belongsTo karena cart memiliki satu book, dan book bisa dimiliki oleh banyak cart
        return $this->belongsTo(Book::class);
    }
        
    public function subscriptionPackage()
    {   //belongsTo : memakai belongsTo karena cart memiliki satu subscriptionPackage, dan subscriptionPackage bisa dimiliki oleh banyak cart
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function user()
    {   //belongsTo : memakai belongsTo karena cart memiliki satu user, dan user bisa dimiliki oleh banyak cart
        return $this->belongsTo(User::class);
    }
}
