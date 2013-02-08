<?php namespace Fancy\Core\Entity;

class EnqueueScriptArgument extends Entity
{
    public $name;
    public $src = false;
    public $deps = array();
    public $ver = false;
    public $in_footer = true;
    public $in_admin = false;
}
