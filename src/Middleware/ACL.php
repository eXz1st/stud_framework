<?php
namespace Mindk\Framework\Middleware;
use Optimus\Onion\LayerInterface;
/**
 * Class ACL Route Middleware
 * @package Mindk\Framework\Middleware
 */
class ACL implements LayerInterface
{
    /**
     * Handler
     *
     * @param $object
     * @param Closure $next
     */
    public function peel($object, \Closure $next){
        // Assuming $object is route object:
        return $next($object);
    }
}