<?php

use Fancy\Core\Support\Wordpress;
use Fancy\Core\Facade\Core;
use Fancy\Core\Model\WpPost;

class WordpressTest extends \TestCase
{

    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::wordpress() instanceof Wordpress);
    }

	public function testFactoryMagicMethodCallWithException()
	{
		$wordpress = new Wordpress();

	    try {
	        $this->assertEquals($wordpress->foo(), 'baz');
	    } catch (\RuntimeException $e) {
	        return;
	    }

	    $this->fail('An expected exception has not been raised.');
	}

    public function testFactoryMagicMethodCall()
    {
        $wordpress = new Wordpress();

        function foo() {
        	return 'baz';
        }

        $this->assertEquals($wordpress->foo(), 'baz');
    }

    public function testThePost()
    {
        $GLOBALS['post'] = (object) array('post_type' => 'foo', 'ID' => 1);

        $wordpress = $this->app['fancy.wordpress'];

        $the_post = $wordpress->the_post();

        $this->assertTrue($the_post instanceof WpPost);

        $this->assertEquals($wordpress->the_post(), $the_post);
    }
}
