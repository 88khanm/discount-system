<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    protected $discountService;
    
    /**
     * __construct
     *
     * @param  mixed $discountService
     * @return void
     */
    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    // return discounts list
    public function index()
    {
        $discounts = Discount::all();
        
        return response()->json(["data" => $discounts], 201);
    }

    // Create a new discount
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:discounts',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:1',
            'min_cart_total' => 'nullable|numeric|min:0',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'first_time_only' => 'boolean',
            'active_from' => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_to',
            'stackable' => 'boolean'
        ]);

        $discount = Discount::create($data);

        return response()->json($discount, 201);
    }

    // Apply discount to cart
    public function applyDiscount(Request $request)
    {
        $validated = $request->validate([
            'cart_total' => 'required|numeric|min:0',
            'cart_items' => 'required|array',
            'discount_codes' => 'nullable|array',
        ]);

        $discountData = $this->discountService->applyDiscount($validated);
        
        // if (isset($discountData['error'])) {
        //     return response()->json(['message' => $discountData['error']], 400);
        // }
        // if (!$discountData || !$discountData['message']) {
        //     return response()->json(['success' => false, 'message' => 'Invalid discount'], 400);
        // }

        return response()->json([
            'discount_applied' => $discountData['discount_applied'],
            'final_total' => $discountData['final_total'],
            'applied_discounts' => $discountData["applied_discounts"],
        ], 200);
    }
}
