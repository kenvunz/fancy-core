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
}
