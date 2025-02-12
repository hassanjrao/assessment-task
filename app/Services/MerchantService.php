<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // TODO: Complete this method

        // create user
        $user = User::create(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['api_key'],
                'type' => User::TYPE_MERCHANT
            ]
        );

        // create merchant
        $merchant = $user->merchant()->create(
            [
                'domain' => $data['domain'],
                'display_name' => $data['name']
            ]
        );

        return $merchant;
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['api_key'],
        ]);

        $user->merchant->update([
            'display_name' => $data['name'],
            'domain' => $data['domain'],
        ]);
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        // TODO: Complete this method

        $user = User::where('email', $email)->first();

        if ($user) {
            return $user->merchant;
        }

        return null;
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        // TODO: Complete this method

        //get un paid orders
        $unPaidOrders= $affiliate->unPaidOrders();

        //dispatch the job
        foreach ($unPaidOrders as $order) {
            PayoutOrderJob::dispatch($order);
        }

    }


    /**
     * Get the order stats for a given merchant between two dates.
     *
     * @param Merchant $merchant
     * @param string $from
     * @param string $to
     * @return array{count: int, revenue: float, commissions_owed: float}
     */

    public function orderStats(Merchant $merchant, string $from, string $to): array
    {
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);

        $orders = Order::whereBetween('created_at', [$from, $to])
            ->where('merchant_id', $merchant->id)
        ->get();

        $data = [
            'count' => $orders->count(),
            'revenue' => $orders->sum('subtotal'),
            'commissions_owed' => $orders->whereNotNull('affiliate_id')->sum('commission_owed')
        ];

        return $data;
    }


}
