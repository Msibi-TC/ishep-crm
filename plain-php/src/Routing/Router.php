<?php
namespace Ishep\Routing;
use Ishep\Http\Request;use Ishep\Http\Response;
final class Router
{
    private array $routes=[];
    public function get(string $path,callable $handler,array $middleware=[]):void{$this->add('GET',$path,$handler,$middleware);} public function post(string $path,callable $handler,array $middleware=[]):void{$this->add('POST',$path,$handler,$middleware);}
    private function add(string $method,string $path,callable $handler,array $middleware):void{$pattern=preg_replace('#\{([A-Za-z_][A-Za-z0-9_]*)\}#','(?P<$1>[^/]+)',$path);$this->routes[]=compact('method','handler','middleware')+['pattern'=>'#^'.$pattern.'$#'];}
    public function dispatch(Request $request):Response{$pathMatch=false;foreach($this->routes as $route){if(!preg_match($route['pattern'],$request->path,$matches))continue;$pathMatch=true;if($route['method']!==$request->method)continue;foreach($matches as $key=>$value)if(is_string($key))$request->set($key,urldecode($value));$next=fn(Request $r)=>($route['handler'])($r);foreach(array_reverse($route['middleware']) as $mw){$following=$next;$next=fn(Request $r)=>$mw($r,$following);}return $next($request);}return new Response($pathMatch?'Method Not Allowed':'Not Found',$pathMatch?405:404);}
}
