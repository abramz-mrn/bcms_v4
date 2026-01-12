<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index()
    {
        $q = request('q');

        return Product::query()
            ->when($q, fn($qq) => $qq->where('name','ilike',"%$q%")->orWhere('code','ilike',"%$q%"))
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'code' => ['required','string','max:50','unique:products,code'],
            'name' => ['required','string','max:200'],
            'type' => ['required','string','max:50'],
            'description' => ['nullable','string'],
            'market_segment' => ['nullable','string','max:50'],
            'billing_cycle' => ['required','string','max:50'],
            'price' => ['required','integer','min:0'],
            'tax_rate' => ['nullable','numeric','min:0'],
            'tax_included' => ['nullable','boolean'],
        ]);

        return Product::create($data);
    }

    public function show(Product $product) { return $product; }

    public function update(Product $product)
    {
        $data = request()->validate([
            'code' => ['sometimes','string','max:50',"unique:products,code,{$product->id}"],
            'name' => ['sometimes','string','max:200'],
            'type' => ['sometimes','string','max:50'],
            'description' => ['nullable','string'],
            'market_segment' => ['nullable','string','max:50'],
            'billing_cycle' => ['sometimes','string','max:50'],
            'price' => ['sometimes','integer','min:0'],
            'tax_rate' => ['nullable','numeric','min:0'],
            'tax_included' => ['nullable','boolean'],
        ]);

        $product->update($data);
        return $product;
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['ok' => true]);
    }
}