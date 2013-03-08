<?php

use Fancy\Core\Model\WpPost;
use Fancy\Core\Facade\Core;

class WpPostTest extends \TestCase
{
    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::wpPost() instanceof WpPost);
    }

    public function testInstantiationViaFacadeWithArguments()
    {
        $mock = $this->getMock('Fancy\Core\Model\WpPost', array('newQuery'));
        $class = get_class($mock);

        $this->assertTrue(Core::wpPost(null, get_class($mock)) instanceof $class);
    }

    public function testCast()
    {
        $mock = $this->getMock('Fancy\Core\Model\WpPost', array('newQuery'));
        $class = get_class($mock);

        $instance = WpPost::cast(array('post_type'=>'foo'), $class);

        $this->assertTrue($instance instanceof $class);
        $this->assertEquals($instance->getPostType(), 'foo');
    }

    public function testGetSetPostType()
    {
        $instance = new WpPost();

        $instance->setPostType('foo');

        $this->assertEquals($instance->getPostType(), 'foo');
    }

    public function testGetThe()
    {
        $GLOBALS['post'] = (object) array('ID' => 1);

        $instance = new WpPost();

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('the_title', 'setup_postdata'));

        $wordpress->expects($this->once())
            ->method('the_title')
            ->will($this->returnCallBack(function() {
                echo 'foo';
            }));

        $instance->setWordpress($wordpress);

        $this->assertEquals($instance->getThe('title'), 'foo');
    }

    public function testGetFieldSingle()
    {
        $meta = array(
            (object) array(
                "meta_key" => "foo",
                "meta_value" => "baz"
            ),

            (object) array(
                "meta_key" => "foo",
                "mata_value" => "baz1"
            )
        );

        $instance = new WpPost();

        $instance->meta = $meta;

        $result = $instance->getField('foo');

        $expected = $meta[0]->meta_value;

        $this->assertEquals($expected, $result);
    }

    public function testGetField()
    {
        $meta = array(
            (object) array(
                "meta_key" => "foo",
                "meta_value" => "baz"
            ),

            (object) array(
                "meta_key" => "foo",
                "meta_value" => "baz1"
            )
        );

        $instance = new WpPost();

        $instance->meta = $meta;

        $result = $instance->getField('foo', false);

        $expected = array('baz', 'baz1');

        $this->assertEquals($expected, $result);
    }
}
