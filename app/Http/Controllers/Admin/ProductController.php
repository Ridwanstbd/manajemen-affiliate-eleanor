<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportProductRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Services\Admin\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }
    
    public function index()
    {
        return view('pages.admin.product-sample.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::select([
                        'id', 
                        'name', 
                        'price', 
                        'seller_sku', 
                        'category', 
                        'product_detail', 
                        'is_visible', 
                        'image_path'
                    ]);
                    
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    if($row->image_path) {
                        $imageUrl = $row->image_path ? (Str::startsWith($row->image_path, ['http://', 'https://']) ? $row->image_path : asset('storage/' . $row->image_path)) : '';
                        return '<img src="'.$imageUrl.'" onclick="openLightbox(\''.$imageUrl.'\')" style="width:48px; height:48px; object-fit:cover; border-radius:4px; cursor:pointer; border:1px solid #e2e8f0;">';
                    }
                    return '<span style="font-size:12px; color:#94a3b8;">No Image</span>';
                })
                ->addColumn('price_formated', function($row) {
                    return 'Rp ' . number_format($row['price'], 0, ',', '.');
                })
                ->addColumn('action', function($row) {
                    return view('pages.admin.product-sample.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['image','action'])
                ->make(true);
        }
    }

    public function importData(ImportProductRequest $request)
    {
        try {
            $this->importService->executeProductUpdateImport($request->file('files'));
            
            return redirect()->back()->with('success', 'File Excel berhasil diunggah dan masuk antrean. Notifikasi akan dikirim setelah proses selesai (maks. 1 menit).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    
    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $data = $request->only(['seller_sku', 'name', 'category', 'product_detail']);
        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }
}