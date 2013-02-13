<?php namespace Fancy\Core\Entity;

abstract class Entity
{
    protected static $signatures = array();

    public static function signature()
    {
        $className = get_called_class();

        if(empty(self::$signatures[$className])) {
            $instance = new static();
            self::$signatures[$className] = $instance->toArray();
        }

        return self::$signatures[$className];
    }

    public function __construct(array $attributes = array())
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray()
    {
        // get keys out of type casting the object
        $object_vars = array_keys((array) $this);

        $class_vars = array_keys(get_class_vars(get_class($this)));

        $var_keys = array_intersect($object_vars, $class_vars);

        $vars = array();

        foreach ($var_keys as $key) {
            $vars[$key] = $this->$key;
        }

        return $vars;
    }
}
