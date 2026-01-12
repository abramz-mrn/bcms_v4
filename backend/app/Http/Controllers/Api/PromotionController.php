<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index()
    {
        return Promotion::query()->with('product')->orderBy('id','desc')->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'product_id' => ['required','exists:products,id'],
            'name' => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date'],
            'discount' => ['required','integer','min:0'],
        ]);

        return Promotion::create($data);
    }

    public function show(Promotion $promotion) { return $promotion->load('product'); }

    public function update(Promotion $promotion)
    {
        $data = request()->validate([
            'product_id' => ['sometimes','exists:products,id'],
            'name' => ['sometimes','string','max:200'],
            'description' => ['nullable','string'],
            'start_date' => ['sometimes','date'],
            'end_date' => ['sometimes','date'],
            'discount' => ['sometimes','integer','min:0'],
        ]);

        $promotion->update($data);
        return $promotion->load('product');
    }

    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();
        return response()->json(['ok' => true]);
    }
}