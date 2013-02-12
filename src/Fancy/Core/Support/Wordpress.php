<?php namespace Fancy\Core\Support;

/**
 * Simple wrapper class for calling all wordpress functions, making it easy for unit testing
 * also adding a few helper functions to normalize the inconsistency in wordpress funtions naming and such
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

    /**
     * Return the current global post object
     * @return object Current global post object
     */
    public function post()
    {
        global $post;
        return $post;
    }

    /**
     * Works out the current taxonomy regardless it's category
     * or tag or custom taxonomies
     * @return object Taxonomy object
     */
    public function term()
    {
        $term = $this->get_queried_object();

        return $term;
    }

    /**
     * A unified way to add action/filter to wordpress system
     * @param  string  $tag           The name of the action to which the $function_to_add is hooked.
     * @param  Closure $function      The name of the function you wish to be called.
     * @param  int $priority          Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param  int $accepted_args     The number of arguments the function accept (default 99 aka 'all')
     */
    public function on($tag, \Closure $function, $priority = 10, $accepted_args = 99)
    {
        $pieces = preg_split('/\s+/', $tag);

        foreach ($pieces as $key => $tag) {
            static::add_action($tag, $function, $priority, $accepted_args);
        }
    }

    public function wp_enqueue_scripts(/* arguments */)
    {
        $arguments = func_get_args();
        return call_user_func_array(array($this, 'wp_enqueue_script'), $arguments);
    }

    public function wp_enqueue_styles(/* arguments */)
    {
        $arguments = func_get_args();
        return call_user_func_array(array($this, 'wp_enqueue_style'), $arguments);
    }
}
