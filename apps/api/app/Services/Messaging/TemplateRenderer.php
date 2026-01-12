<?php

namespace App\Services\Messaging;

class TemplateRenderer
{
    public static function render(?string $text, array $vars): ?string
    {
        if ($text === null) return null;

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($vars) {
            $k = $m[1];
            return array_key_exists($k, $vars) ? (string) $vars[$k] : $m[0];
        }, $text);
    }
}