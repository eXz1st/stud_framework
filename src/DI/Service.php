<?php
namespace Mindk\Framework\DI;
/**
 * Class Service
 *
 * @package Mindk\Framework\DI
 */
class Service
{
    /**
     * @var array Service cache
     */
    protected static $services = [];
    /**
     * Service setter
     *
     * @param   string  name
     * @param   mixed   object
     */
    public static function set($name, $object) {
        self::$services[$name] = $object;
    }
    /**
     * Service getter
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get($name) {
        return self::$services[$name] ?? null; //@TODO: Resolve dependency
    }
}