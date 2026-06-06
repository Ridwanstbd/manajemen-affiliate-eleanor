<?php

namespace Tests\Feature\Affiliator;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $affiliator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->affiliator = User::factory()->create(['role' => 'AFFILIATOR']);
    }

    private function makeProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'id'         => (string) rand(1000000, 9999999),
            'name'       => 'Produk Test',
            'category'   => 'Kecantikan',
            'seller_sku' => 'SKU-' . uniqid(),
            'price'      => 75000,
            'is_visible' => true,
        ], $attributes));
    }

    public function test_affiliator_can_add_product_to_cart()
    {
        $product = $this->makeProduct(['is_visible' => true]);

        $response = $this->actingAs($this->affiliator)
                         ->post(route('affiliator.cart.store', $product->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Produk berhasil ditambahkan ke keranjang.');
        $response->assertSessionHas('affiliate_cart');
    }

    public function test_affiliator_can_checkout_cart()
    {
        $product = $this->makeProduct();

        $cart = [
            $product->id => [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => 1,
            ]
        ];

        $response = $this->actingAs($this->affiliator)
                         ->withSession(['affiliate_cart' => $cart])
                         ->post(route('affiliator.cart.checkout'), [
                             'address' => 'Jl. Affiliator Sukses No. 123, Madiun',
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $response->assertSessionMissing('affiliate_cart');

        $this->assertDatabaseHas('sample_requests', [
            'user_id' => $this->affiliator->id,
            'status'  => 'PENDING',
            'address' => 'Jl. Affiliator Sukses No. 123, Madiun',
        ]);

        $this->assertDatabaseHas('sample_request_details', [
            'product_id' => $product->id,
        ]);
    }
}