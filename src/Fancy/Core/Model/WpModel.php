<?php namespace Fancy\Core\Model;

use Illuminate\Database\Eloquent\Model;
use Fancy\Core\Support\Wordpress;

class WpModel extends Model
{
    protected static $wp;

    public static function setWordpress(Wordpress $wp)
    {
        self::$wp = $wp;
    }
}
