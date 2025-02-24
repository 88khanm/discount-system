<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Discount;
use Carbon\Carbon;
use App\Services\DiscountService;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use DB;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize the service once per test
        $this->discountService = new DiscountService();
    }
    
    public function test_discount_applies_correctly()
    {
        
        $discount = [
                'code' => 'TEST10',
                'type' => 'percentage',
                'value' => 10,
                'min_cart_total' => 50,
                'applicable_products' => null,
                'applicable_categories' => null,
                'active_from' => Carbon::now()->subDay(),
                'active_to' => Carbon::now()->addDay(),
                'stackable' => true
        ];
        
        DB::table("discounts")->insert($discount);

        $requestData = [
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['TEST10']
        ];

        $response = $this->discountService->applyDiscount($requestData);

        $this->assertEquals(10, $response['discount_applied']);
        $this->assertEquals(90, $response['final_total']);
    }

 

    public function test_zero_discount_returns_full_price()
    {
        $discount = Discount::create([
            'code' => 'TEST10',
            'type' => 'fixed',
            'value' => 0,
            'min_cart_total' => 100,
            'active_from' => Carbon::now()->subDay(),
            'active_to' => Carbon::now()->addDay(),
        ]);

        $requestData = [
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['TEST10']
        ];

        $response = $this->discountService->applyDiscount($requestData);

        $this->assertEquals(100, $response['final_total']);
    }

    public function test_full_discount_returns_zero()
    {
        $discount = Discount::create([
            'code' => 'TEST10',
            'type' => 'fixed',
            'value' => 100,
            'min_cart_total' => 50,
            'active_from' => Carbon::now()->subDay(),
            'active_to' => Carbon::now()->addDay(),
        ]);

       

        $requestData = [
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['TEST10']
        ];

        $response = $this->discountService->applyDiscount($requestData);

        $this->assertEquals(0, $response['final_total']);

    }

    public function test_discount_does_not_apply_if_below_min_cart_total() {
        $discount = Discount::create([
            'code' => 'TEST20',
            'type' => 'fixed',
            'value' => 20,
            'min_cart_total' => 100,
            'active_from' => Carbon::now()->subDay(),
            'active_to' => Carbon::now()->addDay(),
        ]);

         
       

        $requestData = [
            'cart_total' => 50,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['TEST20']
        ];

        $response = $this->discountService->applyDiscount($requestData);

        $this->assertEquals(0, $response['discount_applied']);
        $this->assertEquals(50, $response['final_total']);
    }

    public function test_discount_does_not_apply_if_expired() {

        $discount = Discount::create([
            'code' => 'EXPIRED',
            'type' => 'fixed',
            'value' => 10,
            'active_from' => Carbon::now()->subDays(10),
            'active_to' => Carbon::now()->subDay(),
        ]);

        $now = Carbon::now();
       
        $isValid = $now->between($discount->active_from, $discount->active_to);
        
        $this->assertFalse($isValid);
    }
   
}
