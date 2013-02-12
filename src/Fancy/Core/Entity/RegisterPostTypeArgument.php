<?php namespace Fancy\Core\Entity;

class RegisterPostTypeArgument extends Entity
{
    public $post_type;
    public $args = array (
        'labels' => array(),
        'public' => true
    );
}
