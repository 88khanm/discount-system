<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Discount;
use Carbon\Carbon;

class DiscountApiTest extends TestCase
{

    use RefreshDatabase;

    public function test_is_discount_model_exist(){

        $this->withoutExceptionHandling(); // this method gives you more information about errors

        // here we create some dummy discount for testing using factory method
        $discount = Discount::factory()->create();
        
        $this->assertModelExists($discount);
    }

    public function test_can_create_a_discount()
    {
        $this->withoutExceptionHandling(); // this method gives you more information about errors in readable form
        
        $discountData = Discount::factory()->make()->toArray(); // Create fake data but don't save

        $response = $this->postJson('/api/discounts/store', $discountData);

        $response->assertStatus(201)
                 ->assertJson([  
                    'code' => $discountData['code'],
                 ]);

        $this->assertDatabaseHas('discounts', [
            'code' => $discountData['code'],
        ]);
    }

    public function test_apply_discount_to_cart() {
        
        $this->withoutExceptionHandling();
        
        $discount = Discount::create([
            'code' => 'HOLIDAY20',
            'type' => 'fixed',
            'value' => 20,
            'min_cart_total' => 50,
            'active_from' => Carbon::now()->subDay(),
            'active_to' => Carbon::now()->addDay(),
            'stackable' => true
        ]);

        $response = $this->postJson('/api/discounts/apply', [
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['HOLIDAY20']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'discount_applied' => 20,
                     'final_total' => 80,
                     'applied_discounts' => ['HOLIDAY20']
                 ]);
    }

    public function test_discount_does_not_apply_if_invalid_code() {
        
        $this->withoutExceptionHandling(); // this method gives you more information about errors

        $response = $this->postJson('/api/discounts/apply', [
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ],
            'discount_codes' => ['INVALIDCODE']
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'discount_applied' => 0,
                     'final_total' => 100,
                     'applied_discounts' => []
                 ]);
    }

    public function test_discount_does_not_apply_if_cart_total_below_minimum() {

        $this->withoutExceptionHandling(); // this method gives you more information about errors

        Discount::create([
            'code' => 'SUMMER15',
            'type' => 'percentage',
            'value' => 15,
            'min_cart_total' => 200,
            'active_from' => Carbon::now()->subDay(),
            'active_to' => Carbon::now()->addDay(),
            'stackable' => true
        ]);

        $response = $this->postJson('/api/discounts/apply', [
            'discount_codes' => ['SUMMER15'],
            'cart_total' => 100,
            'cart_items' => [
                ['product_id' => 1, 'category_id' => 5, 'price' => 50],
                ['product_id' => 2, 'category_id' => 5, 'price' => 50]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'discount_applied' => 0,
                     'final_total' => 100,
                     'applied_discounts' => []
                 ]);
    }

    public function test_get_discounts_list_success() {

        $this->withoutExceptionHandling(); // this method gives you more information about errors

        Discount::factory()->count(9)->create(); // Create fake data but don't save

         // Create one specific discount manually for testing
        $discount = Discount::factory()->create([
            'type' => 'fixed',
            'value' => 20,
            'code' => 'WELCOME20'
        ]);

        $this->withoutExceptionHandling();
        $response = $this->getJson('/api/discounts');

        $response->assertStatus(201);

        $this->assertDatabaseCount('discounts', 10); // Ensure 10 discounts exist
        $this->assertDatabaseHas('discounts', ['code' => 'WELCOME20']); // Ensure the custom discount exists
    }

    public function test_non_stackable_discount_blocks_others()
    {
        $this->withoutExceptionHandling(); // this method gives you more information about errors

        $discount1 = Discount::factory()->create([
            'code' => 'FLAT20',
            'type' => 'fixed',
            'value' => 20,
            'stackable' => false,
        ]);

        $discount2 = Discount::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'stackable' => true,
        ]);

        $cartData = [
            'discount_codes' => ['FLAT20', 'SAVE10'],
            'cart_total' => 100,
            'cart_items' => [['product_id' => 1, 'category_id' => 2, 'price' => 50, 'quantity' => 2]]
        ];

        $response = $this->postJson('/api/discounts/apply', $cartData);

        $response->assertStatus(200)
                 ->assertJson([
                    'discount_applied' => 20,
                    'final_total' => 80,
                    'applied_discounts' => ['FLAT20']
                 ]);
    }

    public function test_stackable_discount_apply()
    {
        $this->withoutExceptionHandling(); // this method gives you more information about errors

        $discount1 = Discount::factory()->create([
            'code' => 'FLAT20',
            'type' => 'fixed',
            'value' => 20,
            'min_cart_total' => 50,
            'stackable' => true,
        ]);

        $discount2 = Discount::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'min_cart_total' => 50,
            'stackable' => true,
        ]);

        $cartData = [
            'discount_codes' => ['FLAT20', 'SAVE10'],
            'cart_total' => 100,
            'cart_items' => [['product_id' => 1, 'category_id' => 2, 'price' => 50, 'quantity' => 2]]
        ];

        $response = $this->postJson('/api/discounts/apply', $cartData);

        // Expected calculations:
        // 1. FLAT20 discount applied first: $100 - $20 = $80
        // 2. SAVE10 (10% of $80): $80 - $8 = $72

        $response->assertStatus(200)
                 ->assertJson([
                    'discount_applied' => 28,
                    'final_total' => 72,
                    'applied_discounts' => ['FLAT20', 'SAVE10']
                 ]);
    }
}
