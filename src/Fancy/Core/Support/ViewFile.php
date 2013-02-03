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
        $name = $this->getPossibleViewFile($name);

        $nameWithNameSpace = "{$this->namespace}::$name";

        if($this->exists($name)) {
            return $name;
        } else if($this->exists($nameWithNameSpace)) {
            return $nameWithNameSpace;
        } else {
            throw new \InvalidArgumentException("View [$name] not found.");
        }
    }

    public function exists($name)
    {
        try {
            return is_string($this->finder->find($name));
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    protected function getPossibleViewFile($name)
    {
        $view = $name;

        if(!is_null($this->directory)) {
            $view = "{$this->directory}.$name";
        }

        return $view;
    }
}
