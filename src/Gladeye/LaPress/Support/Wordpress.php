<?php namespace Gladeye\LaPress\Support;

/**
 * Simple wrapper class for calling all wordpress functions, making it easy for unit testing
 */
class Wordpress
{
    public function __call($method, array $arguments)
    {
        if(function_exists($method)) {
            switch (count($arguments))
            {
                case 0:
                    return $method();

                case 1:
                    return $method($arguments[0]);

                case 2:
                    return $method($arguments[0], $arguments[1]);

                case 3:
                    return $method($arguments[0], $arguments[1], $arguments[2]);

                case 4:
                    return $method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);

                default:
                    return call_user_func_array($method, $arguments);
            }
        } else {
            throw new \RuntimeException("Wordpress cannot find function name '$method'");
        }
    }
}
