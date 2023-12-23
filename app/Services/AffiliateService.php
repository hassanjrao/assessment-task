<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method

        // check if a user with the email already exists, if so throw exception
        $userExists= User::where('email', $email)->first();
        if ($userExists) {
            throw new AffiliateCreateException('Email already exists');
        }

        //create user
        $user = User::create(
            [
                'name' => $name,
                'email' => $email,
                'type' => User::TYPE_AFFILIATE
            ]
        );

        // create affiliate
        $affiliate = $user->affiliate()->create(
            [
                'merchant_id' => $merchant->id,
                'user_id' => $user->id,
                'commission_rate' => $commissionRate,
                'discount_code' => $this->apiService->createDiscountCode($merchant)['code']
            ]
        );

        // send email
        Mail::to($email)->send(new AffiliateCreated($affiliate));

        return $affiliate;
    }
}
