<?php
namespace Ishep\Http\Middleware;use Ishep\Bootstrap\Application;use Ishep\Http\{Request,Response};final class ActiveAccountMiddleware{public function __invoke(Request $r,callable $next):Response{$u=Application::instance()->auth()->user();if(!$u||$u['account_status']!=='active'){Application::instance()->auth()->logout();return Response::redirect(url('/login'));}return$next($r);}}
