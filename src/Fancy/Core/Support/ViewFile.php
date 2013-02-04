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
        $name = 'default';

        $extensions = array(
            'meta',
            'context'
        );

        foreach ($extensions as $key => $extension) {
            $method = "intuitBy" . ucfirst($extension);
            $found = $this->$method();

            if(!is_null($found)) {
                return $found;
            }
        }

        return $name;
    }

    public function intuitByContext()
    {
        $view = null;

        $contexts = array(
            'is_front_page' => 'front',
            'is_home' => 'home',
            'is_page' => 'page',
            'is_single' => 'single',
            'is_category' => 'category',
            'is_tag' => 'tag',
            'is_taxonomy' => 'taxonomy',
            'is_archive' => 'archive',
            'is_404' => '404'
        );

        foreach ($contexts as $key => $context) {
            if($this->wp->$key()) {
                $view = $this->find($context);
                if(!is_null($view)) {
                    return $view;
                }
            }
        }

        return $view;
    }

    public function intuitByMeta()
    {
        $view = null;

        $post = $this->wp->post();
        $metaValue = $this->wp->get_post_meta($post->ID, 'page', true);

        $view = $this->find("meta-page-$metaValue");

        return $view;
    }

    public function get($name)
    {
        $view = $this->find($name);

        if(is_null($view)) {
            throw new \InvalidArgumentException("View [$name] not found.");
        }

        return $view;
    }

    public function exists($name)
    {
        try {
            return is_string($this->finder->find($name));
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    protected function getViewFile($name)
    {
        $view = $name;

        if(!is_null($this->directory)) {
            $view = "{$this->directory}.$name";
        }

        return $view;
    }

    protected function find($name)
    {
        $name = $this->getViewFile($name);

        $nameWithNameSpace = "{$this->namespace}::$name";

        if($this->exists($name)) {
            return $name;
        } else if($this->exists($nameWithNameSpace)) {
            return $nameWithNameSpace;
        }

        return null;
    }
}
