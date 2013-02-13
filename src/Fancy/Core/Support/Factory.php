<?php namespace Fancy\Core\Support;

class Factory
{
    protected $app;

    protected $namespace = FANCY_NAME;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function __call($method, array $arguments)
    {
        $name = "{$this->namespace}.$method";

        if(!isset($this->app[$name])) {
            throw new \RuntimeException("Factory cannot find '$name'");
        }

        $instance = $this->app[$name];

        if(method_exists($instance, 'factory')) {
            return call_user_func_array(array($instance, 'factory'), $arguments);
        }

        return $this->app[$name];
    }

}
