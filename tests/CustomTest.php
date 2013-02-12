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
                        'singular_name' => 'Case Study'
                    ),
                    'public' => true
                )
            ),

            array(
                'post_type' => 'work',
                'args' => array(
                    'labels' => array(
                        'name' => 'Works',
                        'singular_name' => 'Work'
                    ),

                    'public' => false
                )
            ),

            array(
                'post_type' => 'project',
                'args' => array(
                    'labels' => array(
                        'name' => 'Projects',
                        'singular_name' => 'Project'
                    ),
                    'public' => false
                )
            )
        );

        $custom = Core::custom();

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
                        'singular_name' => 'Case Study'
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
                        'singular_name' => 'Work'
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
                        'singular_name' => 'Project'
                    ),
                    'public' => false
                )
            )
        );

        $custom = Core::custom();

        $result = $custom->parseTaxonomyConfig($config['taxonomy']);

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
        }
    }
}
