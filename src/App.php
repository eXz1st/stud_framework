<?php
namespace Mindk\Framework;
use Mindk\Framework\Exceptions\NotFoundException;
use Mindk\Framework\Routing\Route;
use Mindk\Framework\Routing\Router;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Http\Response\Response;
use Mindk\Framework\Http\Response\JsonResponse;
use Mindk\Framework\DI\Service;
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

        $db = new \PDO(sprintf('mysql:host=%s;dbname=%s;', $this->config['db_host'], $this->config['db_name'] ),
            $this->config['db_user'],
            $this->config['db_pass']
        );

        Service::set('db', $db);
    }
    /**
     * Run the app
     */
    public function run(){
        try {
            $request = Request::getInstance();
            $router = new Router($request, $this->config['routes']);
            $route = $router->findRoute();
            if ($route instanceof Route) {
                $controllerReflection = new \ReflectionClass($route->controller);
                if ($controllerReflection->hasMethod($route->action)) {
                    $controller = $controllerReflection->newInstance();
                    $methodReflection = $controllerReflection->getMethod($route->action);

                    // Get response from responsible controller:
                    $response = $methodReflection->invokeArgs($controller, $route->params);

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