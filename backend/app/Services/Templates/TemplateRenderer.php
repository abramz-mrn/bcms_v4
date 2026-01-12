<?php

namespace App\Services\Templates;

class TemplateRenderer
{
    /**
     * Replace {{var}} with provided values. Unknown vars remain unchanged.
     */
    public function render(string $content, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($vars) {
            $key = $m[1];
            return array_key_exists($key, $vars) ? (string) $vars[$key] : $m[0];
        }, $content) ?? $content;
    }
}