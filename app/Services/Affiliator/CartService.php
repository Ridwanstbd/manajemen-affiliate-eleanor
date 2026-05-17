<?php

namespace App\Services\Affiliator;

use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Exception;

class CartService
{
    protected string $sessionKey = 'affiliate_cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }
    public function addToCart(Product $product): array
    {
        $cart = $this->getCart();

        if (array_key_exists($product->id, $cart)) {
            throw new Exception('Produk ini sudah ada di dalam keranjang Anda.');
        }

        if ($product->stock <= 0) {
            throw new Exception('Gagal menambahkan, stok produk habis.');
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image_path' => $product->image_path,
            'category' => $product->category,
            'quantity' => 1,
            'mandatory_video_count' => $product->mandatory_video_count 
        ];

        Session::put($this->sessionKey, $cart);

        return $cart;
    }

    public function removeFromCart(string $productId): void
    {
        $cart = $this->getCart();

        if (array_key_exists($productId, $cart)) {
            unset($cart[$productId]);
            Session::put($this->sessionKey, $cart);
        }
    }

    public function clearCart(): void
    {
        Session::forget($this->sessionKey);
    }

    public function checkout(User $user, string $address): SampleRequest
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            throw new Exception('Gagal checkout, keranjang Anda kosong.');
        }

        DB::beginTransaction();
        try {
            $sampleRequest = SampleRequest::create([
                'user_id' => $user->id,
                'status'  => 'PENDING',
                'address' => $address,
            ]);

            foreach ($cart as $item) {
                SampleRequestDetail::create([
                    'sample_request_id' => $sampleRequest->id,
                    'product_id'        => $item['id'],
                    'quantity'          => $item['quantity'] ?? 1,
                ]);
            }

            $this->clearCart();

            DB::commit();

            return $sampleRequest;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Terjadi kesalahan saat memproses pengajuan: ' . $e->getMessage());
        }
    }
}