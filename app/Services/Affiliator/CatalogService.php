<?php

namespace App\Services\Affiliator;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogService
{
    public function getProducts(array $filters): LengthAwarePaginator
    {
        $query = Product::query()->where('is_visible', true);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_low'       => $query->orderBy('price', 'asc'),
            'price_high'      => $query->orderBy('price', 'desc'),
            default           => $query->latest(),
        };

        return $query->paginate(12)->withQueryString();
    }
}