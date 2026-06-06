<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'ADMINISTRATOR']);
    }

    // FIX: ProductFactory memakai 'stock' & 'mandatory_video_count' yang tidak ada
    // di $fillable Product model, sehingga factory()->create() gagal insert ke DB.
    // Solusi: buat product pakai create() manual dengan hanya field yang ada di $fillable.
    private function makeProduct(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'id'           => (string) rand(1000000, 9999999),
            'name'         => 'Produk Test',
            'category'     => 'Makanan',
            'seller_sku'   => 'SKU-' . uniqid(),
            'price'        => 50000,
            'is_visible'   => true,
        ], $attributes));
    }

    public function test_admin_can_view_product_page()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin-dashboard.product-index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_product()
    {
        Storage::fake('public');
        $product = $this->makeProduct(['name' => 'Produk Lama', 'seller_sku' => 'SKU-001']);
        $file    = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
                         ->put(route('admin-dashboard.product-update', $product->id), [
                             'name'       => 'Produk Baru',
                             'seller_sku' => 'SKU-002',
                             'image'      => $file,
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id'         => $product->id,
            'name'       => 'Produk Baru',
            'seller_sku' => 'SKU-002',
        ]);
    }

    public function test_admin_can_queue_product_import_via_excel()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('product_update.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.import-product-update'), [
                             'files' => [$file],
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function test_affiliator_cannot_access_admin_product_routes()
    {
        $affiliator = User::factory()->create(['role' => 'AFFILIATOR']);

        $response = $this->actingAs($affiliator)
                         ->get(route('admin-dashboard.product-index'));

        $response->assertStatus(403);
    }
}