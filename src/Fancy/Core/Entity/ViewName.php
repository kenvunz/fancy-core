<?php namespace Fancy\Core\Entity;

class ViewName extends Entity
{
    public $name;
    public $prefix;
    public $namespace;

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
