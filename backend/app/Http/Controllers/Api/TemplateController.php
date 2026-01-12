<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\JsonResponse;

class TemplateController extends Controller
{
    public function index()
    {
        return Template::query()->orderBy('id','desc')->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'name' => ['required','string','max:200'],
            'type' => ['required','string','max:20'], // email|sms|whatsapp
            'subject' => ['nullable','string','max:200'],
            'content' => ['required','string'],
            'variables' => ['nullable','array'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['created_by'] = request()->user()->id;

        return Template::create($data);
    }

    public function show(Template $template) { return $template; }

    public function update(Template $template)
    {
        $data = request()->validate([
            'name' => ['sometimes','string','max:200'],
            'type' => ['sometimes','string','max:20'],
            'subject' => ['nullable','string','max:200'],
            'content' => ['sometimes','string'],
            'variables' => ['nullable','array'],
            'is_active' => ['nullable','boolean'],
        ]);

        $template->update($data);
        return $template;
    }

    public function destroy(Template $template): JsonResponse
    {
        $template->delete();
        return response()->json(['ok' => true]);
    }
}