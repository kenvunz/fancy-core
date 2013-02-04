<?php namespace Fancy\Core\Entity;

class ViewName
{
    public $name;
    public $prefix;
    public $namespace;

    public function __construct($name, $namespace = null, $prefix = null)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->prefix = $prefix;
    }

    public function __toString()
    {
        $name = $this->name;

        if(!is_null($this->prefix)) {
            $name =  "{$this->prefix}.$name";
        }

        if(!is_null($this->namespace)) {
            $name = "{$this->namespace}::$name";
        }

        return $name;
    }
}
