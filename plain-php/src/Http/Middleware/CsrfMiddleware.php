<?php
namespace Ishep\Http\Middleware;use Ishep\Bootstrap\Application;use Ishep\Http\{Request,Response};final class CsrfMiddleware{public function __invoke(Request $r,callable $next):Response{return Application::instance()->csrf()->valid($r->input['_token']??null)?$next($r):Application::instance()->render('errors/419',[],419);}}
