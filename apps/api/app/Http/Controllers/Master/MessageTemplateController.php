<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Services\Messaging\TemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $q = MessageTemplate::query()->latest();

        if ($request->filled('channel')) {
            $q->where('channel', (string) $request->string('channel'));
        }
        if ($request->filled('event')) {
            $q->where('event', (string) $request->string('event'));
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->string('search'));
            $q->where(function ($qq) use ($s) {
                $qq->where('key', 'ilike', "%{$s}%")
                   ->orWhere('name', 'ilike', "%{$s}%")
                   ->orWhere('event', 'ilike', "%{$s}%");
            });
        }

        $perPage = (int) ($request->integer('per_page') ?: 50);
        $perPage = max(5, min(100, $perPage));

        return $q->paginate($perPage);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required','string','max:80', Rule::unique('message_templates','key')->whereNull('deleted_at')],
            'name' => ['required','string','max:120'],
            'channel' => ['required','string', Rule::in(['email','whatsapp','sms'])],
            'event' => ['required','string','max:80'],
            'subject' => ['nullable','string','max:200'],
            'body' => ['required','string'],
            'active' => ['boolean'],
            'meta' => ['nullable','array'],
        ]);

        // Unique per channel+event (enforced by index too)
        $exists = MessageTemplate::query()
            ->where('channel', $data['channel'])
            ->where('event', $data['event'])
            ->whereNull('deleted_at')
            ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Template for this channel+event already exists.',
                'code' => 'TEMPLATE_EXISTS',
            ], 409);
        }

        return MessageTemplate::create($data);
    }

    public function show(MessageTemplate $messageTemplate)
    {
        return $messageTemplate;
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $data = $request->validate([
            'key' => ['sometimes','required','string','max:80',
                Rule::unique('message_templates','key')->ignore($messageTemplate->id)->whereNull('deleted_at')
            ],
            'name' => ['sometimes','required','string','max:120'],
            'channel' => ['sometimes','required','string', Rule::in(['email','whatsapp','sms'])],
            'event' => ['sometimes','required','string','max:80'],
            'subject' => ['nullable','string','max:200'],
            'body' => ['sometimes','required','string'],
            'active' => ['boolean'],
            'meta' => ['nullable','array'],
        ]);

        // If channel/event updated, ensure no duplicate
        $channel = $data['channel'] ?? $messageTemplate->channel;
        $event = $data['event'] ?? $messageTemplate->event;

        $exists = MessageTemplate::query()
            ->where('channel', $channel)
            ->where('event', $event)
            ->where('id', '!=', $messageTemplate->id)
            ->whereNull('deleted_at')
            ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Template for this channel+event already exists.',
                'code' => 'TEMPLATE_EXISTS',
            ], 409);
        }

        $messageTemplate->update($data);
        return $messageTemplate;
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();
        return response()->noContent();
    }

    // POST /message-templates/preview
    public function preview(Request $request)
    {
        $data = $request->validate([
            'subject' => ['nullable','string'],
            'body' => ['required','string'],
            'vars' => ['nullable','array'],
        ]);

        $vars = $data['vars'] ?? [];
        return response()->json([
            'subject' => TemplateRenderer::render($data['subject'] ?? null, $vars),
            'body' => TemplateRenderer::render($data['body'], $vars),
        ]);
    }
}