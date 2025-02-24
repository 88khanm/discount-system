<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\DiscountUsage;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class DiscountService
{
    public function applyDiscount($cart)
    {
       
        $cartTotal = $cart['cart_total'];
        $cartItems = collect($cart['cart_items']);
        $discountCodes = $cart['discount_codes'] ?? [];

        $discounts = Discount::whereIn('code', $discountCodes)
        ->where(function ($query) {
            $query->whereNull('active_from')
                  ->orWhere('active_from', '<=', Carbon::now());
        })
        ->where(function ($query) {
            $query->whereNull('active_to')
                  ->orWhere('active_to', '>=', Carbon::now());
        })
        ->get();

        $finalDiscount = 0;
        $appliedDiscounts = [];
        $stackingAllowed = true;


        foreach ($discounts as $discount) {
            // Validate minimum cart total
            if ($discount->min_cart_total && $cartTotal < $discount->min_cart_total) {
                continue;
            }

            // if (!$discount->is_stackable && count($appliedDiscounts) > 0) {
            //     return response()->json(['error' => "Discount code '{$discount->code}' cannot be stacked"], 400);
            // }

            // // Validate minimum cart total
            // if ($discount->min_cart_total && $cartTotal < $discount->min_cart_total) {
            //     return response()->json(['error' => "Discount '{$discount->code}' requires a minimum cart total of {$discount->min_cart_total}"], 400);
            // }

            // Prevent further stacking if a non-stackable discount is applied
            if (!$discount->stackable) {
                $stackingAllowed = false;
            }

            // Check if the discount applies to specific products/categories
            if ($discount->applicable_products || $discount->applicable_categories) {
                $valid = $cartItems->filter(function ($item) use ($discount) {
                    return (
                        (!$discount->applicable_products || in_array($item['product_id'], $discount->applicable_products)) ||
                        (!$discount->applicable_categories || in_array($item['category_id'], $discount->applicable_categories))
                    );
                })->isNotEmpty();

                if (!$valid) continue;
            }

            // Apply discount
            $discountAmount = ($discount->type === 'percentage')
                ? ($cartTotal * ($discount->value / 100))
                : $discount->value;

            $cartTotal -= $discountAmount;
            $finalDiscount += $discountAmount;
            $appliedDiscounts[] = $discount->code;

            // If a non-stackable discount is applied, stop processing further discounts
            if (!$stackingAllowed) {
                break;
            }
        }

        return [
            // 'message' => count($appliedDiscounts) ? 'Discounts applied successfully' : 'No valid discounts applied',
            'discount_applied' => $finalDiscount,
            'final_total' => max($cartTotal, 0),
            'applied_discounts' => $appliedDiscounts,
        ];
    }
}

?>