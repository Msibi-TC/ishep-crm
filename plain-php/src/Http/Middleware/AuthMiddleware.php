<?php
namespace Ishep\Http\Middleware;use Ishep\Bootstrap\Application;use Ishep\Http\{Request,Response};final class AuthMiddleware{public function __invoke(Request $r,callable $next):Response{$app=Application::instance();return $app->session()->get('user_id')&&$app->auth()->user()?$next($r):Response::redirect(url('/login'));}}
