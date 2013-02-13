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
        $GLOBALS['post'] = (object) array();

        $instance = new WpPost();

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('the_title'));

        $wordpress->expects($this->once())
            ->method('the_title')
            ->will($this->returnCallBack(function() {
                echo 'foo';
            }));

        $instance->setWordpress($wordpress);

        $this->assertEquals($instance->getThe('title'), 'foo');
    }
}
