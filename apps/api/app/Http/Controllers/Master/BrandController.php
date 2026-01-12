<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index() { return Brand::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'companies_id' => ['required','integer','exists:companies,id'],
            'name' => ['required','string'],
            'description' => ['nullable','string'],
        ]);
        return Brand::create($data);
    }
    public function show(Brand $brand) { return $brand; }
    public function update(Request $request, Brand $brand) {
        $data = $request->validate([
            'companies_id' => ['sometimes','required','integer','exists:companies,id'],
            'name' => ['sometimes','required','string'],
            'description' => ['nullable','string'],
        ]);
        $brand->update($data); return $brand;
    }
    public function destroy(Brand $brand) { $brand->delete(); return response()->noContent(); }
}