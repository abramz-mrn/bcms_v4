<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Router;
use Illuminate\Http\JsonResponse;

class RouterController extends Controller
{
    public function index()
    {
        return Router::query()->orderBy('id','desc')->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required','string','max:200'],
            'location' => ['nullable','string','max:200'],
            'description' => ['nullable','string'],
            'ip_address' => ['required','ip'],
            'api_port' => ['nullable','integer','min:1','max:65535'],
            'ssh_port' => ['nullable','integer','min:1','max:65535'],
            'api_username' => ['required','string','max:100'],
            'api_password' => ['required','string','max:200'],
            'api_certificate' => ['nullable','string'],
            'tls_enabled' => ['nullable','boolean'],
            'ssh_enabled' => ['nullable','boolean'],
            'status' => ['nullable','string','max:50'],
            'sync_interval' => ['nullable','integer','min:10'],
            'config_backup' => ['nullable','array'],
        ]);

        return Router::create($data);
    }

    public function show(Router $router) { return $router; }

    public function update(Router $router)
    {
        $data = request()->validate([
            'name' => ['sometimes','string','max:200'],
            'location' => ['nullable','string','max:200'],
            'description' => ['nullable','string'],
            'ip_address' => ['sometimes','ip'],
            'api_port' => ['nullable','integer','min:1','max:65535'],
            'ssh_port' => ['nullable','integer','min:1','max:65535'],
            'api_username' => ['sometimes','string','max:100'],
            'api_password' => ['sometimes','string','max:200'],
            'api_certificate' => ['nullable','string'],
            'tls_enabled' => ['nullable','boolean'],
            'ssh_enabled' => ['nullable','boolean'],
            'status' => ['nullable','string','max:50'],
            'sync_interval' => ['nullable','integer','min:10'],
            'config_backup' => ['nullable','array'],
        ]);

        $router->update($data);
        return $router;
    }

    public function destroy(Router $router): JsonResponse
    {
        $router->delete();
        return response()->json(['ok' => true]);
    }
}