<?php

namespace App\Services\Midtrans;

use App\Models\Checkout;
use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct(protected Checkout $checkout)
    {
        parent::__construct();
    }

    public function getSnapToken()
    {
        $this->checkout->loadMissing(['checkoutbook', 'checkoutbook.book', 'user']);
        $params = [
            'transaction_details' => [
                'order_id' => $this->checkout->order_code ?? (string) $this->checkout->getKey(),
                'gross_amount' => $this->checkout->total_price,
            ],
            'item_details' => $this->checkout->checkoutbook->map(fn ($row) => [
                'id' => $row->book->id,
                'price' => $row->book->price,
                'quantity' => $row->total_book,
                'name' => $row->book->title,
            ])->all(),
            'customer_details' => [
                'first_name' => $this->checkout->user->name,
                'email' => $this->checkout->user->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }
}
