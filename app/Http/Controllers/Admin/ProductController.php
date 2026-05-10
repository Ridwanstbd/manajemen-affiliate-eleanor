<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Admin\ImportService;
use Illuminate\Http\Request;
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
            $query = Product::select(['id','name','price','stock','seller_sku', 'mandatory_video_count']);

            return DataTables::of($query)
                ->addColumn('action', function($row) {
                    return view('pages.admin.product-sample.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
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
        $product = Product::find($id);
        $product->update($request->only(['name', 'price', 'stock', 'mandatory_video_count']));

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
