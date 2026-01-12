<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        return Company::query()->latest()->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string'],
            'alias' => ['nullable','string'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string'],
            'state' => ['nullable','string'],
            'pos' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'logo' => ['nullable','string'],
            'bank_account' => ['nullable','array'],
            'npwp' => ['nullable','string'],
        ]);

        return Company::create($data);
    }

    public function show(Company $company)
    {
        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['sometimes','required','string'],
            'alias' => ['nullable','string'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string'],
            'state' => ['nullable','string'],
            'pos' => ['nullable','string'],
            'phone' => ['nullable','string'],
            'email' => ['nullable','email'],
            'logo' => ['nullable','string'],
            'bank_account' => ['nullable','array'],
            'npwp' => ['nullable','string'],
        ]);

        $company->update($data);
        return $company;
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->noContent();
    }
}