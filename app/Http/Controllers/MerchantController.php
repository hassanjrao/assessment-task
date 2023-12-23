<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    public function __construct(
       protected MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method

        //validate request
        $request->validate([
            "from" => "required|date",
            "to" => "required|date"
        ]);

        //get merchant
        $user = $request->user();

        $merchant= $user->merchant;

        if (!$merchant) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }

        //get order stats
        $orderStats = $this->merchantService->orderStats($merchant, $request->from, $request->to);


        return response()->json($orderStats);

    }
}
