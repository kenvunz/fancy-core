<?php namespace Fancy\Core\Support;

use Doctrine\Common\Inflector\Inflector;

class Custom
{
    protected $wp;
    protected $config;
    protected $inflector;

    public function __construct(Inflector $inflector, Wordpress $wp, array $config)
    {
        $this->wp = $wp;
        $this->config = $config;
        $this->inflector = $inflector;
    }

    public function initialize()
    {
        $configs = array('post_type', 'taxonomy');

        foreach ($configs as $key => $config) {
            if(!empty($this->config[$config])) {
                $custom_config = $this->config[$config];

                $method = 'parse'.camel_case($config).'Config';

                $custom = $this->$method($custom_config);

                $wp = $this->wp;

                foreach ($custom as $key => $value) {
                    call_user_func_array(array($wp, "register_$config"), $value->toArray());
                }
            }
        }
    }

    /**
     * Parse an array of post_type config elements into Fancy\Core\Entity\RegisterPostTypeArgument
     * @param  array $config    Array of config to be parsed
     * @return array            Array of Fancy\Core\Entity\RegisterPostTypeArgument objects
     */
    public function parsePostTypeConfig(array $config)
    {
        $class = 'Fancy\Core\Entity\RegisterPostTypeArgument';

        $default = $class::signature();

        $result = array();

        foreach ($config as $key => $value) {
            $attributes = array_merge($default, array('post_type' => $key));
            $args = &$attributes['args'];

            if(is_string($value)) {
                $args['label'] = $value;
            } else if(is_array($value)) {
                $args = array_merge($args, $value);
            }

            $this->parseLabels($args, $key);

            $argument = new $class($attributes);

            $result[] = $argument;
        }

        return $result;
    }

    public function parseTaxonomyConfig(array $config)
    {
        $class = 'Fancy\Core\Entity\RegisterTaxonomyArgument';

        $default = $class::signature();

        $result = array();

        foreach ($config as $key => $value) {
            $attributes = array_merge($default, array('taxonomy' => $key));
            $args = &$attributes['args'];

            if(is_string($value)) {
                $args['label'] = $value;
            } else if(is_array($value)) {
                if(isset($value['object_type'])) {
                    $attributes['object_type'] = $value['object_type'];

                    unset($value['object_type']);
                }

                $args = array_merge($args, $value);
            }

            $this->parseLabels($args, $key);

            $argument = new $class($attributes);

            $result[] = $argument;
        }
        return $result;
    }

    protected function parseLabels(array &$args, $context)
    {
        if(isset($args['name'])) {
            $args['label'] = $args['name'];

            unset($args['name']);
        }

        if(isset($args['label']) && (!isset($args['labels']['name']) || !isset($args['labels']['singular_name']))) {
            $args['labels']['name'] = $this->inflector->pluralize($args['label']);
            $args['labels']['singular_name'] = $this->inflector->singularize($args['label']);

             unset($args['label']);
        } else if(isset($args['labels']['name']) && !isset($args['labels']['singular_name'])) {
            $name = $args['labels']['name'];
            $args['labels']['name'] = $this->inflector->pluralize($name);
            $args['labels']['singular_name'] = $this->inflector->singularize($name);

        } else if(isset($args['labels']['singular_name']) && !isset($args['labels']['name'])) {
            $args['labels']['name'] = $this->inflector->pluralize($args['labels']['singular_name']);
        }

        $labels = &$args['labels'];

        $Singular = $labels['singular_name'];
        $Plural = $labels['name'];

        $singular = strtolower($Singular);
        $plural = strtolower($Plural);

        foreach ($labels as $key => $value) {
            if($key === 'name' || $key === 'singular_name') {
                continue;
            }

            $labels[$key] = _x(str_replace(
                array('#plural#', '#singular#', '#Plural#', '#Singular#'),
                array($plural, $singular, $Plural, $Singular), $value),
                $context);
        }

        return $args;
    }
}
