<?php namespace Fancy\Core\Support;

use Fancy\Core\Support\Wordpress;
use Illuminate\View\ViewFinderInterface;

class ViewFile
{
    protected $directory;

    protected $namespace = FANCY_NAME;

    protected $wp;

    protected $finder;

    public function __construct(Wordpress $wp, ViewFinderInterface $finder)
    {
        $this->wp = $wp;

        $this->finder = $finder;
    }

    public function setDirectory($name)
    {
        $this->directory = $name;

        return $this;
    }

    public function intuit()
    {
        return $this->get('default');
    }

    public function get($name)
    {
        $view = "{$this->namespace}::$name";

        if(!is_null($this->directory)) {
            $view = "{$this->namespace}::{$this->directory}.$name";
        }

        return $view;
    }
}
