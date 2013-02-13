<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\Support\Custom;
use Fancy\Core\Support\Wordpress;


class CustomTest extends \TestCase
{
    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::custom() instanceof Custom);
    }

    public function testParsePostTypeConfig()
    {
        $config = array(
            'post_type' => array(
                'case-study' => 'Case Study',

                'work' => array(
                    'label' => 'Work',
                    'public' => false
                ),

                'project' => array(
                    'labels' => array(
                        'name' => 'Project'
                    ),
                    'public' => false
                )
            )
        );

        $expected = array(
            array(
                'post_type' => 'case-study',
                'args' => array(
                    'labels' => array(
                        'name' => 'Case Studies',
                        'singular_name' => 'Case Study',
                        'add_new' => 'Add New',
                        'add_new_item' => 'Add New Case Study',
                        'edit_item' => 'Edit Case Study',
                        'new_item' => 'New Case Study',
                        'view_item' => 'View Case Study',
                        'search_items' => 'Search case studies',
                        'not_found' => 'No case studies found',
                        'not_found_in_trash' => 'No case studies found in Trash',
                        'parent_item_colon' => 'Parent case study:',
                        'all_items' => 'All Case Studies'
                    ),
                    'public' => true
                )
            ),

            array(
                'post_type' => 'work',
                'args' => array(
                    'labels' => array(
                        'name' => 'Works',
                        'singular_name' => 'Work',
                        'add_new' => 'Add New',
                        'add_new_item' => 'Add New Work',
                        'edit_item' => 'Edit Work',
                        'new_item' => 'New Work',
                        'view_item' => 'View Work',
                        'search_items' => 'Search works',
                        'not_found' => 'No works found',
                        'not_found_in_trash' => 'No works found in Trash',
                        'parent_item_colon' => 'Parent work:',
                        'all_items' => 'All Works'
                    ),

                    'public' => false
                )
            ),

            array(
                'post_type' => 'project',
                'args' => array(
                    'labels' => array(
                        'name' => 'Projects',
                        'singular_name' => 'Project',
                        'add_new' => 'Add New',
                        'add_new_item' => 'Add New Project',
                        'edit_item' => 'Edit Project',
                        'new_item' => 'New Project',
                        'view_item' => 'View Project',
                        'search_items' => 'Search projects',
                        'not_found' => 'No projects found',
                        'not_found_in_trash' => 'No projects found in Trash',
                        'parent_item_colon' => 'Parent project:',
                        'all_items' => 'All Projects'
                    ),
                    'public' => false
                )
            )
        );

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('_x'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->any())
            ->method('_x')
            ->will($this->returnCallBack(function($value, $context) {
                return $value;
            }));

        $custom = new Custom($wordpress, array());

        $result = $custom->parsePostTypeConfig($config['post_type']);

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
        }
    }

    public function testParseTaxonomyConfigArgument()
    {
        $config = array(
            'taxonomy' => array(
                'case-study' => 'Case Study',

                'work' => array(
                    'label' => 'Work',
                    'object_type' => array('page'),
                    'public' => false
                ),

                'project' => array(
                    'labels' => array(
                        'name' => 'Project'
                    ),
                    'object_type' => 'page',
                    'public' => false
                )
            )
        );

        $expected = array(
            array(
                'taxonomy' => 'case-study',
                'object_type' => 'post',
                'args' => array(
                    'labels' => array(
                        'name' => 'Case Studies',
                        'singular_name' => 'Case Study',
                        'search_items' => 'Search Case Studies',
                        'popular_items' => 'Popular Case Studies',
                        'all_items' => 'All Case Studies',
                        'parent_item' => 'Parent Case Study',
                        'parent_item_colon' => 'Parent Case Study:',
                        'edit_item' => 'Edit Case Study',
                        'view_item' => 'View Case Study',
                        'update_item' => 'Update Case Study',
                        'add_new_item' => 'Add New Case Study',
                        'new_item_name' => 'New Case Study Name',
                        'separate_items_with_commas' => 'Separate case studies with commas',
                        'add_or_remove_items' => 'Add or remove case studies',
                        'choose_from_most_used' => 'Choose from the most used case studies',
                    ),
                    'public' => true
                )
            ),

            array(
                'taxonomy' => 'work',
                'object_type' => array('page'),
                'args' => array(
                    'labels' => array(
                        'name' => 'Works',
                        'singular_name' => 'Work',
                        'search_items' => 'Search Works',
                        'popular_items' => 'Popular Works',
                        'all_items' => 'All Works',
                        'parent_item' => 'Parent Work',
                        'parent_item_colon' => 'Parent Work:',
                        'edit_item' => 'Edit Work',
                        'view_item' => 'View Work',
                        'update_item' => 'Update Work',
                        'add_new_item' => 'Add New Work',
                        'new_item_name' => 'New Work Name',
                        'separate_items_with_commas' => 'Separate works with commas',
                        'add_or_remove_items' => 'Add or remove works',
                        'choose_from_most_used' => 'Choose from the most used works',
                    ),

                    'public' => false
                )
            ),

            array(
                'taxonomy' => 'project',
                'object_type' => 'page',
                'args' => array(
                    'labels' => array(
                        'name' => 'Projects',
                        'singular_name' => 'Project',
                        'search_items' => 'Search Projects',
                        'popular_items' => 'Popular Projects',
                        'all_items' => 'All Projects',
                        'parent_item' => 'Parent Project',
                        'parent_item_colon' => 'Parent Project:',
                        'edit_item' => 'Edit Project',
                        'view_item' => 'View Project',
                        'update_item' => 'Update Project',
                        'add_new_item' => 'Add New Project',
                        'new_item_name' => 'New Project Name',
                        'separate_items_with_commas' => 'Separate projects with commas',
                        'add_or_remove_items' => 'Add or remove projects',
                        'choose_from_most_used' => 'Choose from the most used projects',
                    ),
                    'public' => false
                )
            )
        );

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('_x'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->any())
            ->method('_x')
            ->will($this->returnCallBack(function($value, $context) {
                return $value;
            }));

        $custom = new Custom($wordpress, array());

        $result = $custom->parseTaxonomyConfig($config['taxonomy']);

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
        }
    }
}
