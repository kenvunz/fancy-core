<?php namespace Fancy\Core\Entity;

class RegisterTaxonomyArgument extends Entity
{
    public $taxonomy;
    public $object_type = 'post';
    public $args = array (
        'labels' => array(),
        'public' => true
    );
}
