<?php namespace Fancy\Core\Support;

class ViewFile
{
    protected $context;

    protected $namespace = FANCY_NAME;

    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    public function intuit()
    {
        return $this->get('default');
    }

    public function get($name)
    {
        $view = "{$this->namespace}::$name";

        if(!is_null($this->context)) {
            $view = "{$this->namespace}::{$this->context}.$name";
        }

        return $view;
    }
}
