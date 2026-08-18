<?php
namespace Ishep\Http\Middleware;
use Ishep\Bootstrap\Application;use Ishep\Http\{Request,Response};
final class ApplicationReviewerMiddleware{public function __invoke(Request $r,callable $next):Response{$app=Application::instance();$id=(int)($app->auth()->user()['id']??0);return($app->authorization()->hasRole($id,'administrator')||$app->authorization()->hasRole($id,'super_user'))?$next($r):$app->render('errors/403',[],403);}}
