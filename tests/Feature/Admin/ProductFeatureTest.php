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

    public function test_admin_can_view_product_page()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin-dashboard.product-index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_product()
    {
        Storage::fake('public');
        $product = Product::factory()->create(['name' => 'Produk Lama', 'seller_sku' => 'SKU-001']);
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
                         ->put(route('admin-dashboard.product-update', $product->id), [
                             'name'       => 'Produk Baru',
                             'seller_sku' => 'SKU-002',
                             'stock'      => 50,
                             'image'      => $file,
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Baru',
            'seller_sku' => 'SKU-002',
            'stock' => 50,
        ]);
    }

    public function test_admin_can_mass_update_products()
    {
        Product::factory()->count(3)->create(['mandatory_video_count' => 1]);

        $response = $this->actingAs($this->admin)
                         ->post(route('admin-dashboard.product-mass-update'), [
                             'mandatory_video_count' => 3,
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('products', [
            'mandatory_video_count' => 3,
        ]);
    }

    public function test_affiliator_cannot_access_admin_product_routes()
    {
        $affiliator = User::factory()->create(['role' => 'AFFILIATOR']);
        
        $response = $this->actingAs($affiliator)
                         ->get(route('admin-dashboard.product-index'));

        $response->assertStatus(403);
    }
}