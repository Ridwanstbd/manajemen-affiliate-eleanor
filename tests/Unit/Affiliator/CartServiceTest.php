<?php

namespace Tests\Unit\Affiliator;

use App\Models\Challenge;
use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\User;
use App\Services\Affiliator\CartService;
use App\Services\Affiliator\CatalogService;
use App\Services\Affiliator\ChallengeService as AffiliatorChallengeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CartService();
        Session::flush();
    }

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'id'         => (string) rand(1000000, 9999999),
            'name'       => 'Produk Test',
            'category'   => 'Kecantikan',
            'seller_sku' => 'SKU-' . uniqid(),
            'price'      => 75000,
            'is_visible' => true,
        ], $attrs));
    }

    // ─── getCart ──────────────────────────────────────────────────────────────

    public function test_get_cart_returns_empty_array_when_no_session()
    {
        $this->assertEquals([], $this->service->getCart());
    }

    // ─── addToCart ────────────────────────────────────────────────────────────

    public function test_add_to_cart_adds_product_to_session()
    {
        $product = $this->makeProduct();

        $cart = $this->service->addToCart($product);

        $this->assertArrayHasKey($product->id, $cart);
        $this->assertEquals(1, $cart[$product->id]['quantity']);
    }

    public function test_add_to_cart_stores_correct_product_fields()
    {
        $product = $this->makeProduct(['name' => 'Serum Vitamin C', 'price' => 99000]);

        $cart = $this->service->addToCart($product);
        $item = $cart[$product->id];

        $this->assertEquals('Serum Vitamin C', $item['name']);
        $this->assertEquals(99000, $item['price']);
        $this->assertEquals(1, $item['quantity']);
    }

    public function test_add_to_cart_throws_when_product_already_in_cart()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah ada di dalam keranjang');

        $product = $this->makeProduct();
        $this->service->addToCart($product);
        $this->service->addToCart($product); // duplikat
    }

    public function test_add_to_cart_allows_multiple_different_products()
    {
        $p1 = $this->makeProduct();
        $p2 = $this->makeProduct();

        $this->service->addToCart($p1);
        $cart = $this->service->addToCart($p2);

        $this->assertCount(2, $cart);
    }

    // ─── removeFromCart ───────────────────────────────────────────────────────

    public function test_remove_from_cart_removes_existing_product()
    {
        $product = $this->makeProduct();
        $this->service->addToCart($product);

        $this->service->removeFromCart($product->id);
        $cart = $this->service->getCart();

        $this->assertArrayNotHasKey($product->id, $cart);
    }

    public function test_remove_from_cart_does_nothing_for_nonexistent_product()
    {
        // Tidak boleh throw
        $this->service->removeFromCart('produk-tidak-ada');
        $this->assertEmpty($this->service->getCart());
    }

    // ─── clearCart ────────────────────────────────────────────────────────────

    public function test_clear_cart_empties_session()
    {
        $product = $this->makeProduct();
        $this->service->addToCart($product);

        $this->service->clearCart();

        $this->assertEmpty($this->service->getCart());
    }

    // ─── checkout ─────────────────────────────────────────────────────────────

    public function test_checkout_throws_when_cart_is_empty()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('keranjang Anda kosong');

        $user = User::factory()->create();
        $this->service->checkout($user, 'Jl. Test No. 1');
    }

    public function test_checkout_creates_sample_request_with_pending_status()
    {
        $user    = User::factory()->create(['role' => 'AFFILIATOR']);
        $product = $this->makeProduct();
        $this->service->addToCart($product);

        $result = $this->service->checkout($user, 'Jl. Maju No. 10, Jakarta');

        $this->assertInstanceOf(SampleRequest::class, $result);
        $this->assertDatabaseHas('sample_requests', [
            'user_id' => $user->id,
            'status'  => 'PENDING',
            'address' => 'Jl. Maju No. 10, Jakarta',
        ]);
    }

    public function test_checkout_creates_sample_request_details_for_each_item()
    {
        $user = User::factory()->create(['role' => 'AFFILIATOR']);
        $p1   = $this->makeProduct();
        $p2   = $this->makeProduct();
        $this->service->addToCart($p1);
        $this->service->addToCart($p2);

        $sr = $this->service->checkout($user, 'Jl. Test');

        $this->assertDatabaseHas('sample_request_details', ['product_id' => $p1->id]);
        $this->assertDatabaseHas('sample_request_details', ['product_id' => $p2->id]);
    }

    public function test_checkout_clears_cart_after_success()
    {
        $user    = User::factory()->create(['role' => 'AFFILIATOR']);
        $product = $this->makeProduct();
        $this->service->addToCart($product);

        $this->service->checkout($user, 'Jl. Test');

        $this->assertEmpty($this->service->getCart());
    }
}


// ═══════════════════════════════════════════════════════════════════════════════
// CatalogServiceTest
// ═══════════════════════════════════════════════════════════════════════════════

class CatalogServiceTest extends TestCase
{
    use RefreshDatabase;

    private CatalogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CatalogService();
    }

    private function makeProduct(string $name, float $price = 50000, bool $visible = true): Product
    {
        return Product::create([
            'id'         => (string) rand(1000000, 9999999),
            'name'       => $name,
            'category'   => 'Umum',
            'seller_sku' => 'SKU-' . uniqid(),
            'price'      => $price,
            'is_visible' => $visible,
        ]);
    }

    public function test_get_products_returns_only_visible_products()
    {
        $this->makeProduct('Produk Terlihat', 50000, true);
        $this->makeProduct('Produk Tersembunyi', 50000, false);

        $result = $this->service->getProducts([]);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Produk Terlihat', $result->items()[0]->name);
    }

    public function test_get_products_filters_by_search_keyword()
    {
        $this->makeProduct('Serum Vitamin C');
        $this->makeProduct('Pelembap Wajah');

        $result = $this->service->getProducts(['search' => 'Serum']);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('Serum Vitamin C', $result->items()[0]->name);
    }

    public function test_get_products_search_is_case_insensitive()
    {
        $this->makeProduct('Serum Vitamin C');

        $result = $this->service->getProducts(['search' => 'serum']);

        $this->assertEquals(1, $result->total());
    }

    public function test_get_products_sorts_by_price_ascending()
    {
        $this->makeProduct('Mahal', 200000);
        $this->makeProduct('Murah', 50000);
        $this->makeProduct('Sedang', 100000);

        $result = $this->service->getProducts(['sort' => 'price_low']);
        $prices = array_column($result->items(), 'price');

        $this->assertEquals([50000, 100000, 200000], $prices);
    }

    public function test_get_products_sorts_by_price_descending()
    {
        $this->makeProduct('Murah', 50000);
        $this->makeProduct('Mahal', 200000);

        $result = $this->service->getProducts(['sort' => 'price_high']);

        $this->assertEquals(200000, $result->items()[0]->price);
    }

    public function test_get_products_paginates_12_per_page()
    {
        for ($i = 0; $i < 15; $i++) {
            $this->makeProduct("Produk $i");
        }

        $result = $this->service->getProducts([]);

        $this->assertEquals(12, $result->perPage());
        $this->assertEquals(12, count($result->items()));
    }

    public function test_get_products_returns_empty_when_no_visible_products()
    {
        $this->makeProduct('Disembunyikan', 50000, false);

        $result = $this->service->getProducts([]);

        $this->assertEquals(0, $result->total());
    }
}


// ═══════════════════════════════════════════════════════════════════════════════
// AffiliatorChallengeServiceTest
// ═══════════════════════════════════════════════════════════════════════════════

class AffiliatorChallengeServiceTest extends TestCase
{
    use RefreshDatabase;

    private AffiliatorChallengeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AffiliatorChallengeService();
    }

    private function makeChallenge(bool $active, string $start, string $end): Challenge
    {
        return Challenge::create([
            'title'            => 'Challenge Test',
            'rules'            => 'Aturan test',
            'start_date'       => $start,
            'end_date'         => $end,
            'commission_bonus' => 5.0,
            'is_active'        => $active,
        ]);
    }

    // ─── getActiveChallenges ──────────────────────────────────────────────────

    public function test_get_active_challenges_returns_only_active_and_ongoing()
    {
        $this->makeChallenge(true,  now()->subDays(1)->toDateString(), now()->addDays(10)->toDateString()); // aktif
        $this->makeChallenge(false, now()->subDays(1)->toDateString(), now()->addDays(10)->toDateString()); // non-aktif
        $this->makeChallenge(true,  now()->addDays(5)->toDateString(), now()->addDays(15)->toDateString()); // belum mulai
        $this->makeChallenge(true,  now()->subDays(20)->toDateString(), now()->subDays(1)->toDateString());  // sudah berakhir

        $result = $this->service->getActiveChallenges();

        $this->assertCount(1, $result);
    }

    public function test_get_active_challenges_returns_empty_when_none_active()
    {
        $result = $this->service->getActiveChallenges();

        $this->assertCount(0, $result);
    }

    public function test_get_active_challenges_ordered_by_end_date_ascending()
    {
        $this->makeChallenge(true, now()->subDays(1)->toDateString(), now()->addDays(20)->toDateString());
        $this->makeChallenge(true, now()->subDays(1)->toDateString(), now()->addDays(5)->toDateString());

        $result = $this->service->getActiveChallenges();

        $this->assertTrue($result->first()->end_date <= $result->last()->end_date);
    }

    // ─── getPastChallenges ────────────────────────────────────────────────────

    public function test_get_past_challenges_returns_only_ended_active_challenges()
    {
        $this->makeChallenge(true, now()->subDays(30)->toDateString(), now()->subDays(1)->toDateString());  // lalu
        $this->makeChallenge(true, now()->subDays(1)->toDateString(), now()->addDays(10)->toDateString()); // berjalan
        $this->makeChallenge(false, now()->subDays(30)->toDateString(), now()->subDays(1)->toDateString()); // non-aktif

        $result = $this->service->getPastChallenges();

        $this->assertCount(1, $result);
    }

    // ─── getChallengeDetails ──────────────────────────────────────────────────

    public function test_get_challenge_details_returns_challenge_with_relations()
    {
        $challenge = $this->makeChallenge(
            true,
            now()->subDays(5)->toDateString(),
            now()->addDays(5)->toDateString()
        );

        $result = $this->service->getChallengeDetails($challenge->id);

        $this->assertInstanceOf(Challenge::class, $result);
        $this->assertTrue($result->relationLoaded('rewards'));
        $this->assertTrue($result->relationLoaded('winners'));
    }

    public function test_get_challenge_details_throws_for_nonexistent_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->getChallengeDetails(99999);
    }
}