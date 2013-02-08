<?php namespace Fancy\Core\Entity;

class EnqueueStyleArgument extends Entity
{
    public $name;
    public $src = false;
    public $deps = array();
    public $ver = false;
    public $media = 'all';
    public $in_admin = false;
}
