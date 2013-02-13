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

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
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

        foreach ($result as $key => $value) {
            $this->assertEquals($expected[$key], $value->toArray());
        }
    }
}
