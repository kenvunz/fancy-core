<?php namespace Fancy\Core\Entity;

class RegisterTaxonomyArgument extends Entity
{
    public $taxonomy;
    public $object_type = 'post';
    public $args = array (
        'labels' => array(
            'search_items' => 'Search #Plural#',
            'popular_items' => 'Popular #Plural#',
            'all_items' => 'All #Plural#',
            'parent_item' => 'Parent #Singular#',
            'parent_item_colon' => 'Parent #Singular#:',
            'edit_item' => 'Edit #Singular#',
            'view_item' => 'View #Singular#',
            'update_item' => 'Update #Singular#',
            'add_new_item' => 'Add New #Singular#',
            'new_item_name' => 'New #Singular# Name',
            'separate_items_with_commas' => 'Separate #plural# with commas',
            'add_or_remove_items' => 'Add or remove #plural#',
            'choose_from_most_used' => 'Choose from the most used #plural#',
        ),
        'public' => true
    );
}
