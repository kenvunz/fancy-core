<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\Support\ViewFile;
use Fancy\Core\Support\Wordpress;

class ViewFileTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->boot();
    }


    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::view() instanceof ViewFile);
    }

    public function testSetDirectory()
    {
        $view = Core::view();

        $view->setDirectory('template');

        $reflectedClass = new ReflectionClass($view);
        $property = $reflectedClass->getProperty('directory');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($view), 'template');
    }

    public function testGet()
    {
        $namespace = FANCY_NAME;

        $expected = "$namespace::default";

        $this->assertEquals(Core::view()->get('default'), $expected);
    }

    public function testGetWithDirectory()
    {
        $namespace = FANCY_NAME;

        $expected = "$namespace::layouts.default";

        $this->assertEquals(Core::view()->setDirectory('layouts')->get('default'), $expected);
    }

    public function testExists()
    {
        $wordpress = new Wordpress;

        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinderMock', false);

        $finder->expects($this->once())
            ->method('find')
            ->will($this->returnValue('foo'));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertTrue($viewFile->exists('foo'));
    }

    public function testExistsFalse()
    {
        $wordpress = new Wordpress;

        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinderMock2', false);

        $finder->expects($this->once())
            ->method('find')
            ->will($this->throwException(new InvalidArgumentException));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertFalse($viewFile->exists('foo'));
    }
}
