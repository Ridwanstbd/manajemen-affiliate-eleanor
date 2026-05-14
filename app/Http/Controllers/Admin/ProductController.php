<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                        'stock', 
                        'seller_sku', 
                        'category', 
                        'mandatory_video_count', 
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

    public function importData(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|extensions:xlsx,xls,csv'
        ]);

        try {
            $this->importService->executeProductUpdateImport($request->file('files'));
            
            return redirect()->back()->with('success', 'File Excel berhasil diimport dan data produk telah diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'seller_sku' => 'required|string|max:255',
            'name'       => 'required|string|max:255',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ], [
            'seller_sku.required' => 'SKU wajib diisi.',
            'name.required'       => 'Nama produk wajib diisi.',
            'image.image'         => 'File harus berupa gambar.',
            'image.mimes'         => 'Format gambar harus jpeg, png, atau jpg.',
            'image.max'           => 'Ukuran gambar maksimal 2MB.',
        ]);

        $product = Product::findOrFail($id);
        
        $data = $request->only(['seller_sku', 'name', 'category', 'mandatory_video_count', 'stock', 'product_detail']);
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

    public function massUpdate(Request $request)
    {
        $request->validate([
            'stock' => 'nullable|integer',
            'mandatory_video_count' => 'nullable|integer',
        ]);

        $updateData = array_filter($request->only(['stock', 'mandatory_video_count']), function($value) {
            return $value !== null;
        });

        if (!empty($updateData)) {
            $count = Product::count(); 
            
            Product::query()->update($updateData); 
            
            return redirect()->back()->with('success', 'Seluruh (' . $count . ') produk berhasil diperbarui secara massal.');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang diubah.');
    }
}
