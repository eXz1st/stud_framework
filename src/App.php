<?php
namespace Mindk\Framework;
use Mindk\Framework\Routing\Route;
use Mindk\Framework\Routing\Router;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Http\Response\Response;
use Mindk\Framework\Http\Response\JsonResponse;
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
        $request = Request::getInstance();
        $router = new Router($request, $this->config['routes'] );
        $route = $router->findRoute();
        if($route instanceof Route){
            $controllerReflection = new \ReflectionClass($route->controller);
            if($controllerReflection->hasMethod($route->action)){
                $controller = $controllerReflection->newInstance();
                $methodReflection = $controllerReflection->getMethod($route->action);
                // Get response from responsible controller:
                $response = $methodReflection->invokeArgs($controller, $route->params);
                // Ensure it's Response subclass or wrap with JsonResponse:
                if(!($response instanceof Response)){
                    $response = new JsonResponse($response);
                }
            } else {
                $response = new JsonResponse(['error' => 'Bad Controller Action'], 500);
            }
        } else {
            $response = new JsonResponse(['error' => 'Bad Request'], 400);
        }
        // Send final response:
        $response->send();
    }
}