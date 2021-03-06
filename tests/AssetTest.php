<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\Support\Asset;
use Fancy\Core\Support\Wordpress;

class AssetTest extends \TestCase
{
    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::asset() instanceof Asset);
    }

    public function testParseScriptsConfig()
    {
        $config = array(
            'scripts' => array(
                'jquery',

                'script' => 'js/script.js',

                'admin-script' => array(
                    'src' => 'http://foo/baz.js',
                    'in_admin' => true
                )
            )
        );

        $expected = array(
            array(
                'name' => 'jquery',
                'src' => false,
                'deps' => array(),
                'ver' => false,
                'in_footer' => true,
                'in_admin' => false
            ),

            array(
                'name' => 'script',
                'src' => 'http://foo/baz/js/script.js',
                'deps' => array(),
                'ver' => false,
                'in_footer' => true,
                'in_admin' => false
            ),

            array(
                'name' => 'admin-script',
                'src' => 'http://foo/baz.js',
                'deps' => array(),
                'ver' => false,
                'in_footer' => true,
                'in_admin' => true
            )
        );

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('get_stylesheet_directory_uri'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->any())
            ->method('get_stylesheet_directory_uri')
            ->will($this->returnValue('http://foo/baz'));

        $asset = new Asset($wordpress, $config);

        $result = $asset->parseScriptsConfig($config['scripts']);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $result[$key]->toArray());
        }
    }

    public function testParseStylesConfig()
    {
        $config = array(
            'styles' => array(
                'site',

                'style' => 'css/style.css',

                'admin-style' => array(
                    'src' => 'http://foo/baz.css',
                    'in_admin' => true
                )
            )
        );

        $expected = array(
            array(
                'name' => 'site',
                'src' => false,
                'deps' => array(),
                'ver' => false,
                'media' => 'all',
                'in_admin' => false
            ),

            array(
                'name' => 'style',
                'src' => 'http://foo/baz/css/style.css',
                'deps' => array(),
                'ver' => false,
                'media' => 'all',
                'in_admin' => false
            ),

            array(
                'name' => 'admin-style',
                'src' => 'http://foo/baz.css',
                'deps' => array(),
                'ver' => false,
                'media' => 'all',
                'in_admin' => true
            )
        );

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('get_stylesheet_directory_uri'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->any())
            ->method('get_stylesheet_directory_uri')
            ->will($this->returnValue('http://foo/baz'));

        $asset = new Asset($wordpress, $config);

        $result = $asset->parseStylesConfig($config['styles']);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $result[$key]->toArray());
        }
    }

    public function testParseStyleAsClosure()
    {
        $config = array(
            'styles' => array(
                'site' => function() {
                    return "foo1";
                }
            )
        );

        $expected = array(
            array(
                'name' => 'site',
                'src' => 'http://foo/baz/foo1',
                'deps' => array(),
                'ver' => false,
                'media' => 'all',
                'in_admin' => false
            )
        );

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('get_stylesheet_directory_uri'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->any())
            ->method('get_stylesheet_directory_uri')
            ->will($this->returnValue('http://foo/baz'));

        $asset = new Asset($wordpress, $config);

        $result = $asset->parseStylesConfig($config['styles']);

        $this->assertEquals(1, count($result));

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
        }
    }

    public function testCollect()
    {
        $config = array(
            'styles' => array(
                'site' => function() {
                    return "foo1";
                }
            )
        );

        $wordpress = $this->app['fancy.wordpress'];

        $asset = new Asset($wordpress, $config);

        $result = $asset->collect();

        $expected = array('styles' => array($config['styles']));

        $this->assertEquals($expected, $result);
    }

    public function testCollectWithEvent()
    {
        $config = array(
            'styles' => array(
                'site' => function() {
                    return "foo1";
                }
            )
        );

        \Event::listen('fancy::styles.initialize', function() {
            return array('event' => 'css/event.css');
        });

        \Event::listen('fancy::styles.initialize', function() {
            return array('event1' => 'css/event1.css');
        });

        $wordpress = $this->app['fancy.wordpress'];

        $asset = new Asset($wordpress, $config);

        $result = $asset->collect();

        $expected = array('styles' => array(
            array('site' => function() {
                    return "foo1";
                }),

            array('event' => 'css/event.css'),

            array('event1' => 'css/event1.css')
        ));

        $this->assertEquals($expected, $result);
    }
}
