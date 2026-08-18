<?php

use Ishep\Bootstrap\Application;

function env(string $key, ?string $default = null): ?string { $v = $_ENV[$key] ?? getenv($key); return ($v === false || $v === null) ? $default : (string) $v; }
function config(string $key, mixed $default = null): mixed { return Application::instance()->config($key, $default); }
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/'); }
function csrf_field(): string { return '<input type="hidden" name="_token" value="'.e(Application::instance()->csrf()->token()).'">'; }
function old(string $key, string $default = ''): string { return (string) (Application::instance()->session()->old()[$key] ?? $default); }
function errors(): array { return Application::instance()->session()->errors(); }
function field_errors(string $field): array { return (array) (errors()[$field] ?? []); }
function field_invalid(string $field): string { return field_errors($field) ? 'true' : 'false'; }
function user(): ?array { $app=Application::instance(); return $app->session()->get('user_id') ? $app->auth()->user() : null; }
