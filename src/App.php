<?php
namespace Mindk\Framework;
use Mindk\Framework\Exceptions\NotFoundException;
use Mindk\Framework\Routing\Route;
use Mindk\Framework\Routing\Router;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Http\Response\Response;
use Mindk\Framework\Http\Response\JsonResponse;
use Mindk\Framework\Config\Config;
use Mindk\Framework\DI\Injector;
/**
 * Application class
 */
class App
{
    /**
     * @var array   Config cache
     */
    protected $config = null;
    /**
     * App constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
        Injector::setConfig($this->config);
    }
    /**
     * Run the app
     */
    public function run(){
        try {
            $router = Injector::make('router', ['mapping' => $this->config->get('routes', []) ] );
            $route = $router->findRoute();
            if ($route instanceof Route) {
                $controllerReflection = new \ReflectionClass($route->controller);
                if ($controllerReflection->hasMethod($route->action)) {
                    $controller = Injector::make($route->controller);
                    $methodReflection = $controllerReflection->getMethod($route->action);

                    // Get response from responsible controller:
                    $paramset = Injector::resolveParams($methodReflection->getParameters(), $route->params);
                    $response = $methodReflection->invokeArgs($controller, $paramset);

                    // Ensure it's Response subclass or wrap with JsonResponse:
                    if (!($response instanceof Response)) {
                        $response = new JsonResponse($response);
                    }
                } else {
                    throw new \Exception('Bad controller action');
                }
            } else {
                throw new NotFoundException('Route not found');
            }
        }
        catch (NotFoundException $e) {
            $response = $e->toResponse();
        }
        catch (\Exception $e) {
            $response = new JsonResponse(['error' => $e->getMessage()], 500);
        }
        // Send final response:
        $response->send();
    }
}