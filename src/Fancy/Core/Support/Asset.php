<?php namespace Fancy\Core\Support;

class Asset
{

    protected $wp;
    protected $config;

    public function __construct(Wordpress $wp, array $config)
    {
        $this->wp = $wp;
        $this->config = $config;
    }

    /**
     * Listen to wp_enqueue_scripts event and attach assets to the Wordpress template
     */
    public function initialize()
    {
        $sets = $this->collect();

        foreach ($sets as $key => $set) {
            $method = 'parse'.$config.'Config';

            $set = $this->$method($set);

            $wp = $this->wp;

            $this->wp->on("wp_enqueue_scripts", function() use ($wp, $set, $config) {
                foreach ($set as $key => $value) {
                    call_user_func_array(array($wp, "wp_enqueue_$config"), $value->toArray());
                }
            });
        }
    }

    public function collect()
    {
        $configs = array('scripts', 'styles');

        $sets = array();

        foreach ($configs as $key => $config) {
            $set = array();

            if(!empty($this->config[$config])) {
                $set[] = $this->config[$config];
            }

            $collected = \Event::fire(FANCY_NAME."::$config.initialize");

            if(!empty($collected)) {
                foreach ($collected as $key => $value) {
                    if(is_null($value)) {
                        continue;
                    }

                    $set[] = $value;
                }
            }

            if(!empty($set)) {
                $sets[$config] = $set;
            }
        }

        return $sets;
    }

    /**
     * Parse an array of script config elements into Fancy\Core\Entity\EnqueueScriptArgument
     * @param  array  $config   Array of config to be parsed
     * @return array            Array of Fancy\Core\Entity\EnqueueScriptArgument objects
     */
    public function parseScriptsConfig(array $config = array())
    {
        return $this->parseConfig($config, 'Fancy\Core\Entity\EnqueueScriptArgument');
    }

    /**
     * Parse an array of style config elements into Fancy\Core\Entity\EnqueueStyleArgument
     * @param  array  $config   Array of config to be parsed
     * @return array            Array of Fancy\Core\Entity\EnqueueScriptArgument objects
     */
    public function parseStylesConfig(array $config = array())
    {
        return $this->parseConfig($config, 'Fancy\Core\Entity\EnqueueStyleArgument');
    }

    /**
     * Parse an array of config elements into Fancy\Core\Entity\EnqueueScriptArgument
     * @param  array  $config   Array of config to be parsed
     * @param  string $class    Name of class that will be used to create argument objects
     */
    protected function parseConfig(array $config = array(), $class)
    {
        $result = array();

        foreach ($config as $key => $value) {
            $attributes = array();

            if(is_int($key)) {
                $attributes = array('name' => $value);
            } else {
                if(is_array($value)) {
                    $attributes = array_merge(array('name' => $key), $value);
                }

                if(is_string($value) || is_callable($value)) {
                    $attributes = array('name' => $key, 'src' => $value);
                }
            }

            if(!empty($attributes)) {
                $argument = new $class($attributes);

                if(is_callable($argument->src)) {
                    $func = $argument->src;
                    $argument->src = $func($attributes);
                }

                if($argument->src !== false) {
                    $argument->src = $this->resolveSource($argument->src);
                }

                $result[] = $argument;
            }
        }

        return $result;
    }

    protected function resolveSource($src)
    {
        if(strpos($src, 'http') === 0) {
            return $src;
        }

        return $this->wp->get_stylesheet_directory_uri() . "/$src";
    }
}
