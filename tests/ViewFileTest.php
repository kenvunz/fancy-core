<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\Support\ViewFile;

class ViewFileTest extends \TestCase
{

    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::view() instanceof ViewFile);
    }

    public function testSetContext()
    {
        $view = Core::view();

        $view->setContext('template');

        $reflectedClass = new ReflectionClass($view);
        $property = $reflectedClass->getProperty('context');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($view), 'template');
    }

    public function testGet()
    {
        $namespace = FANCY_NAME;

        $expected = "$namespace::foo";

        $this->assertEquals(Core::view()->get('foo'), $expected);
    }

    public function testGetWithContext()
    {
        $namespace = FANCY_NAME;

        $expected = "$namespace::baz.foo";

        $this->assertEquals(Core::view()->setContext('baz')->get('foo'), $expected);
    }
}
