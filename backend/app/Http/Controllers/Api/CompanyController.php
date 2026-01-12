<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function index()
    {
        return Company::query()->orderBy('id','desc')->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required','string'],
            'initial' => ['nullable','string','max:20'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string','max:100'],
            'state' => ['nullable','string','max:100'],
            'pos' => ['nullable','string','max:20'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:200'],
            'logo' => ['nullable','string'],
            'bank_account' => ['nullable','array'],
            'npwp' => ['nullable','string','max:50'],
        ]);

        return Company::create($data);
    }

    public function show(Company $company) { return $company; }

    public function update(Company $company)
    {
        $data = request()->validate([
            'name' => ['sometimes','string'],
            'initial' => ['nullable','string','max:20'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string','max:100'],
            'state' => ['nullable','string','max:100'],
            'pos' => ['nullable','string','max:20'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:200'],
            'logo' => ['nullable','string'],
            'bank_account' => ['nullable','array'],
            'npwp' => ['nullable','string','max:50'],
        ]);

        $company->update($data);
        return $company;
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return response()->json(['ok' => true]);
    }
}