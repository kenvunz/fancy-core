<?php namespace Fancy\Core\Entity;

class RegisterPostTypeArgument extends Entity
{
    public $post_type;
    public $args = array (
        'labels' => array(
            'add_new' => 'Add New',
            'add_new_item' => 'Add New #Singular#',
            'edit_item' => 'Edit #Singular#',
            'new_item' => 'New #Singular#',
            'view_item' => 'View #Singular#',
            'search_items' => 'Search #plural#',
            'not_found' => 'No #plural# found',
            'not_found_in_trash' => 'No #plural# found in Trash',
            'parent_item_colon' => 'Parent #singular#:',
            'all_items' => 'All #Plural#'
        ),
        'public' => true
    );
}
