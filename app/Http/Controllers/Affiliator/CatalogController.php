<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliator\CatalogIndexRequest;
use App\Models\Product;
use App\Services\Affiliator\CatalogService;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogService $catalogService
    ) {}

    public function index(CatalogIndexRequest $request)
    {
        $products = $this->catalogService->getProducts($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('pages.affiliator.catalog.partials.product_cards', compact('products'))->render();
            
            return response()->json([
                'html' => $html,
                'next_page_url' => $products->nextPageUrl()
            ]);
        }

        return view('pages.affiliator.catalog.index', compact('products'));
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.affiliator.catalog.detail', compact('product'));
    }
}