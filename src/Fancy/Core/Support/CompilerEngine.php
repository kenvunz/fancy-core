<?php namespace Fancy\Core\Support;

class CompilerEngine extends \Illuminate\View\Engines\CompilerEngine
{
    public function get($path, array $data = array())
    {
        if(\Config::get('fancy::debug')) {
            var_dump($path);
        }
        return parent::get($path, $data);
    }
}
