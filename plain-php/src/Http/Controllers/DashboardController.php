<?php
namespace Ishep\Http\Controllers;use Ishep\Bootstrap\Application as App;use Ishep\Http\Response;final class DashboardController{public function show(string $view,string $title):Response{return App::instance()->render('dashboards/'.$view,['title'=>$title]);}}
