<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliator\CheckoutRequest;
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
        $user      = auth()->user();

        $allAgreements = Agreement::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')
                            ->where('is_kol', $user->is_kol);
                      });
                if ($user->is_kol) {
                    $query->orWhere(function ($q) {
                        $q->whereNull('user_id')->where('is_kol', false);
                    });
                }
            })
            ->orderByRaw('user_id DESC')
            ->get();

        $personalAgreements = $allAgreements->whereNotNull('user_id')->where('user_id', $user->id)->values();
        $generalAgreements  = $allAgreements->whereNull('user_id')->values();

        return view('pages.affiliator.cart.index', compact('cartItems', 'personalAgreements', 'generalAgreements'));
    }

    public function store(Request $request, Product $product)
    {
        try {
            $this->cartService->addToCart($product);
            $redirectTo = $request->input('redirect_back');
            return $redirectTo
                ? redirect($redirectTo)->with('success', 'Produk berhasil ditambahkan ke keranjang.')
                : redirect()->route('affiliator.catalog.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->cartService->removeFromCart($id);
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(CheckoutRequest $request)
    {
        try {
            $this->cartService->checkout(auth()->user(), $request->address);
            
            return redirect()->route('affiliator.catalog.index')
                ->with('success', 'Pengajuan sampel berhasil dibuat! Menunggu persetujuan admin.');
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}