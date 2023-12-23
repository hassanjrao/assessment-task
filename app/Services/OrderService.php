<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        // check if order already exists
        $orderExists = Order::where('external_order_id', $data['order_id'])->first();

        if ($orderExists) {
            return;
        }

        // get merchant
        $merchant = Merchant::where('domain', $data['merchant_domain'])
        ->with(['affiliate'])
        ->first();


        // create affiliate if not exists
        $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], $merchant->default_commission_rate);


        // calculate commission
        $commission = $data['subtotal_price'] * $merchant->affiliate->commission_rate;

        
        // log commission
        Log::info('OrderService@processOrder:commission', [
            'merchant_id' => $merchant->id,
            'affiliate_id' => $merchant->affiliate->id,
            'commission' => $commission
        ]);

        // create order
        Order::create(
            [
                'merchant_id' => $merchant->id,
                'affiliate_id' => $merchant->affiliate->id,
                'subtotal' => $data['subtotal_price'],
                'commission_owed' => $commission,
                'discount_code' => $data['discount_code'],
                'external_order_id' => $data['order_id'],
            ]
        );
    }
}
