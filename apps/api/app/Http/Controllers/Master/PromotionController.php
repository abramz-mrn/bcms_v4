<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index() { return Promotion::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'products_id' => ['required','integer','exists:products,id'],
            'name' => ['required','string'],
            'description' => ['nullable','string'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date'],
            'discount' => ['required','numeric'],
        ]);
        return Promotion::create($data);
    }
    public function show(Promotion $promotion) { return $promotion; }
    public function update(Request $request, Promotion $promotion) {
        $data = $request->validate([
            'products_id' => ['sometimes','required','integer','exists:products,id'],
            'name' => ['sometimes','required','string'],
            'description' => ['nullable','string'],
            'start_date' => ['sometimes','required','date'],
            'end_date' => ['sometimes','required','date'],
            'discount' => ['sometimes','required','numeric'],
        ]);
        $promotion->update($data); return $promotion;
    }
    public function destroy(Promotion $promotion) { $promotion->delete(); return response()->noContent(); }
}