<?php

namespace App\Services\Affiliator;

use App\Models\Product;
use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class CartService
{
    protected string $sessionKey = 'affiliate_cart';

    protected string $screenshotDir = 'affiliate-screenshots';

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

        $cart[$product->id] = [
            'id'         => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'image_path' => $product->image_path,
            'category'   => $product->category,
            'quantity'   => 1,
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

    public function checkout(User $user, string $address, UploadedFile $screenshot): SampleRequest
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            throw new Exception('Gagal checkout, keranjang Anda kosong.');
        }

        $screenshotPath = $this->processAndStoreScreenshot($screenshot, $user->id);

        DB::beginTransaction();
        try {
            $sampleRequest = SampleRequest::create([
                'user_id'                      => $user->id,
                'status'                       => 'PENDING',
                'address'                      => $address,
                'affiliate_center_screenshot'  => $screenshotPath,
            ]);

            foreach ($cart as $item) {
                SampleRequestDetail::create([
                    'sample_request_id' => $sampleRequest->id,
                    'product_id'        => $item['id'],
                    'quantity'          => $item['quantity'] ?? 1,
                    'status'            => 'PENDING',
                ]);
            }

            $this->clearCart();

            DB::commit();

            return $sampleRequest;

        } catch (Exception $e) {
            DB::rollBack();

            if ($screenshotPath && Storage::disk('public')->exists($screenshotPath)) {
                Storage::disk('public')->delete($screenshotPath);
            }

            throw new Exception('Terjadi kesalahan saat memproses pengajuan: ' . $e->getMessage());
        }
    }

    protected function processAndStoreScreenshot(UploadedFile $file, int $userId): string
    {
        if (! extension_loaded('gd')) {
            throw new Exception('Ekstensi GD PHP tidak tersedia. Hubungi administrator server.');
        }

        $tmpPath = $file->getRealPath();
        $mime    = $file->getMimeType();

        $sourceImage = match (true) {
            str_contains($mime, 'jpeg') => imagecreatefromjpeg($tmpPath),
            str_contains($mime, 'png')  => imagecreatefrompng($tmpPath),
            str_contains($mime, 'gif')  => imagecreatefromgif($tmpPath),
            str_contains($mime, 'webp') => imagecreatefromwebp($tmpPath),
            default => throw new Exception('Format gambar tidak didukung: ' . $mime),
        };

        if (! $sourceImage) {
            throw new Exception('Gagal memuat file gambar. Pastikan file tidak rusak.');
        }

        $origWidth  = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);
        $maxWidth   = 1280;

        if ($origWidth > $maxWidth) {
            $ratio     = $maxWidth / $origWidth;
            $newWidth  = $maxWidth;
            $newHeight = (int) round($origHeight * $ratio);
        } else {
            $newWidth  = $origWidth;
            $newHeight = $origHeight;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $resized, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        imagedestroy($sourceImage);

        $filename  = 'aff-center_u' . $userId . '_' . Str::random(12) . '_' . time() . '.webp';
        $directory = $this->screenshotDir . '/' . date('Y/m');
        $fullPath  = $directory . '/' . $filename;

        Storage::disk('public')->makeDirectory($directory);

        ob_start();
        imagewebp($resized, null, 75); // kualitas 75
        $webpContent = ob_get_clean();

        imagedestroy($resized);

        if ($webpContent === false || empty($webpContent)) {
            throw new Exception('Gagal mengkonversi gambar ke format WebP.');
        }

        Storage::disk('public')->put($fullPath, $webpContent);

        return $fullPath;
    }
}