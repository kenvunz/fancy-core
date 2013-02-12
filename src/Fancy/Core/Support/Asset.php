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
        $self = $this;

        $configs = array('scripts', 'styles');

        foreach ($configs as $key => $config) {
            if(!empty($this->config[$config])) {
                $assets = $this->config[$config];

                $method = 'parse'.$config.'Config';

                $assets = $this->$method($assets);

                $wp = $this->wp;

                $this->wp->on("wp_enqueue_scripts", function() use ($wp, $assets, $config) {
                    foreach ($assets as $key => $asset) {
                        call_user_func_array(array($wp, "wp_enqueue_$config"), $asset->toArray());
                    }
                });
            }
        }
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

                if(is_string($value)) {
                    $attributes = array('name' => $key, 'src' => $value);
                }
            }

            if(!empty($attributes)) {
                $argument = new $class($attributes);

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

        return $this->wp->get_stylesheet_directory() . "/$src";
    }
}
