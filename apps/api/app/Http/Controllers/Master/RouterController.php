<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Router;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index() { return Router::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required','string'],
            'location' => ['nullable','string'],
            'description' => ['nullable','string'],
            'ip_address' => ['required','ip'],
            'api_port' => ['required','integer'],
            'ssh_port' => ['required','integer'],
            'api_username' => ['required','string'],
            'api_password' => ['required','string'],
            'api_certificate' => ['nullable','string'],
            'tls_enabled' => ['required','boolean'],
            'ssh_enabled' => ['required','boolean'],
            'status' => ['required','string'],
            'sync_interval' => ['nullable','integer'],
            'last_sync_at' => ['nullable','date'],
            'config_backup' => ['nullable','array'],
        ]);
        return Router::create($data);
    }
    public function show(Router $router) { return $router; }
    public function update(Request $request, Router $router) {
        $data = $request->validate([
            'name' => ['sometimes','required','string'],
            'location' => ['nullable','string'],
            'description' => ['nullable','string'],
            'ip_address' => ['sometimes','required','ip'],
            'api_port' => ['sometimes','required','integer'],
            'ssh_port' => ['sometimes','required','integer'],
            'api_username' => ['sometimes','required','string'],
            'api_password' => ['sometimes','required','string'],
            'api_certificate' => ['nullable','string'],
            'tls_enabled' => ['sometimes','required','boolean'],
            'ssh_enabled' => ['sometimes','required','boolean'],
            'status' => ['sometimes','required','string'],
            'sync_interval' => ['nullable','integer'],
            'last_sync_at' => ['nullable','date'],
            'config_backup' => ['nullable','array'],
        ]);
        $router->update($data); return $router;
    }
    public function destroy(Router $router) { $router->delete(); return response()->noContent(); }
}