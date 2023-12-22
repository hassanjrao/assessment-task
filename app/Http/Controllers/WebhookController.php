<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    /**
     * Pass the necessary data to the process order method
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Complete this method

        $request->validate([
            'order_id' => 'required',
            'subtotal_price' => 'required|numeric',
            'merchant_domain' => 'required',
            'discount_code' => 'required'
        ]);



        $data=[
            'order_id' => $request->order_id,
            'subtotal_price' => $request->subtotal_price,
            'merchant_domain' => $request->merchant_domain,
            'discount_code' => $request->discount_code,
        ];

        $this->orderService->processOrder($data);

        return response()->json(['message' => 'success']);
    }
}
