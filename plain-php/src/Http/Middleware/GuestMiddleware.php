<?php
namespace Ishep\Http\Middleware;
use Ishep\Bootstrap\Application;use Ishep\Http\{Request,Response};

final class GuestMiddleware
{
    public function __invoke(Request $request, callable $next): Response
    {
        $app=Application::instance();
        if($app->session()->get('user_id') && $app->auth()->user()) return Response::redirect(url('/dashboard'));
        return $next($request);
    }
}
