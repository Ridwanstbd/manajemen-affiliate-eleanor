<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Product;
use App\Services\Affiliator\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cartItems = $this->cartService->getCart();
        $agreements = Agreement::where('is_active', true)->get();
        
        return view('pages.affiliator.cart.index', compact('cartItems', 'agreements'));
    }

    public function store(Request $request, Product $product)
    {
        try {
            $this->cartService->addToCart($product);
            return redirect()->route('affiliator.catalog.index')
                ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->cartService->removeFromCart($id);
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'address' => 'required|string|min:10|max:255',
        ], [
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'address.min' => 'Alamat pengiriman terlalu pendek. Mohon isi dengan lengkap.',
        ]);

        try {
            $this->cartService->checkout(auth()->user(), $request->address);
            
            return redirect()->route('affiliator.catalog.index')
                ->with('success', 'Pengajuan sampel berhasil dibuat! Menunggu persetujuan admin.');
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}