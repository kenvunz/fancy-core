<?php

use Fancy\Core\Support\Wordpress;
use Fancy\Core\Facade\Core;

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
}
