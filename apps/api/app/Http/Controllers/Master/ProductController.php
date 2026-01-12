<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() { return Product::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'code' => ['required','string','max:100','unique:products,code'],
            'name' => ['required','string'],
            'type' => ['required','string'],
            'description' => ['nullable','string'],
            'market_segment' => ['required','string'],
            'billing_cycle' => ['required','string'],
            'price' => ['required','numeric'],
            'tax_rate' => ['required','numeric'],
            'tax_included' => ['required','boolean'],
        ]);
        return Product::create($data);
    }
    public function show(Product $product) { return $product; }
    public function update(Request $request, Product $product) {
        $data = $request->validate([
            'code' => ['sometimes','required','string','max:100','unique:products,code,'.$product->id],
            'name' => ['sometimes','required','string'],
            'type' => ['sometimes','required','string'],
            'description' => ['nullable','string'],
            'market_segment' => ['sometimes','required','string'],
            'billing_cycle' => ['sometimes','required','string'],
            'price' => ['sometimes','required','numeric'],
            'tax_rate' => ['sometimes','required','numeric'],
            'tax_included' => ['sometimes','required','boolean'],
        ]);
        $product->update($data); return $product;
    }
    public function destroy(Product $product) { $product->delete(); return response()->noContent(); }
}