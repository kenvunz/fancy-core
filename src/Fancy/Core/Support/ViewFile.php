<?php namespace Fancy\Core\Support;

use Fancy\Core\Support\Wordpress;
use Illuminate\View\ViewFinderInterface;
use Fancy\Core\Entity\ViewName;

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

    /**
     * Works out a view name base on general Wordpress context
     * @return ViewName|null
     */
    public function intuitByContext()
    {
        $view = null;

        $contexts = array(
            'is_front_page' => 'front',
            'is_home' => 'home',
            'is_single' => 'single',
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

    /**
     * Works out a view name if the current post has a meta key name 'page'
     * @return ViewName|null
     */
    public function intuitByMeta()
    {
        $view = null;

        $post = $this->wp->post();
        $metaValue = $this->wp->get_post_meta($post->ID, 'page', true);

        $view = $this->find("meta-page-$metaValue");

        return $view;
    }

    /**
     * Trigger the internal find method to find a view given by name
     * @param  string $name A name to look for view
     * @return ViewName
     */
    public function get($name)
    {
        $view = $this->find($name);

        if(is_null($view)) {
            throw new \InvalidArgumentException("View [$name] not found.");
        }

        return $view;
    }

    /**
     * Check if a view name is exists using the ViewFileFinder instance
     * @param  string|ViewName $name
     * @return boolean
     */
    public function exists($name)
    {
        try {
            return is_string($this->finder->find($name));
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Get a view name with directory prefix
     * @param  string   $name           A name
     * @param  boolean  $addNamespace   Should add namespace or not
     * @return ViewName
     */
    protected function getViewName($name, $addNamespace = false)
    {
        return new ViewName($name, $addNamespace? FANCY_NAME : null, $this->directory);
    }

    /**
     * Find a possible view name out of current app view directory
     * and the package directory
     * @param  string $name A name to look for view
     * @return string|null  Found view name or null if not found
     */
    protected function find($name)
    {
        $viewName = $this->getViewName($name);

        $viewNameWithNamespace = $this->getViewName($name, true);

        if($this->exists($viewName)) {
            return $viewName;
        } else if($this->exists($viewNameWithNamespace)) {
            return $viewNameWithNamespace;
        }

        return null;
    }
}
