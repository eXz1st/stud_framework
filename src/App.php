<?php
namespace Mindk\Framework;
use Mindk\Framework\Routing\Route;
use Mindk\Framework\Routing\Router;
use Mindk\Framework\Http\Request\Request;
/**
 * Application class
 */
class App
{
    /**
     * @var array   Config cache
     */
    protected $config = [];
    /**
     * App constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    /**
     * Run the app
     */
    public function run(){
        $request = new Request();
        $router = new Router($request, $this->config['routes'] );
        $route = $router->findRoute();
        if($route instanceof Route){
            $controllerReflection = new \ReflectionClass($route->controller);
            if($controllerReflection->hasMethod($route->action)){
                $controller = $controllerReflection->newInstance();
                $methodReflection = $controllerReflection->getMethod($route->action);
                $methodReflection->invokeArgs($controller, $route->params);
            } else {
                // TODO: throw exception
            }
        } else {
            //@TODO: Return 404 Response
        }
    }
}