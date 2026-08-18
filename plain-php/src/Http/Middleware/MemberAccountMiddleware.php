<?php
namespace Ishep\Http\Middleware;
use Ishep\Bootstrap\Application;
use Ishep\Http\{Request,Response};
final class MemberAccountMiddleware{public function __invoke(Request $r,callable $next):Response{$user=Application::instance()->auth()->user();if(!$user||$user['membership_type_id']===null)return Response::redirect(url('/dashboard'));return$next($r);}}