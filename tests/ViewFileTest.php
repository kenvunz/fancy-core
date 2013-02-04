<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\Support\ViewFile;
use Fancy\Core\Support\Wordpress;
use Fancy\Core\Entity\ViewName;

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

        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinder_' . uniqid(), false);

        $finder->expects($this->once())
            ->method('find')
            ->will($this->returnValue('foo'));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertTrue($viewFile->exists(new ViewName('foo')));
    }

    public function testExistsFalse()
    {
        $wordpress = new Wordpress;

        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinder_' . uniqid(), false);

        $finder->expects($this->once())
            ->method('find')
            ->will($this->throwException(new InvalidArgumentException));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertFalse($viewFile->exists(new ViewName('foo')));
    }

    public function testIntuitByContext()
    {
        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinder_' . uniqid(), false);

        $finder->expects($this->once())
            ->method('find')
            ->with($this->equalTo('home'))
            ->will($this->returnValue('home'));

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('is_front_page', 'is_home'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->once())
            ->method('is_front_page')
            ->will($this->returnValue(false));

        $wordpress->expects($this->once())
            ->method('is_home')
            ->will($this->returnValue(true));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertEquals($viewFile->intuitByContext(), 'home');
    }

    public function testIntuitByMeta()
    {
        $finder = $this->getMock('Illuminate\View\FileViewFinder', array('find'), array(), 'FileViewFinder_' . uniqid(), false);

        $finder->expects($this->once())
            ->method('find')
            ->with($this->equalTo('meta-page-home'))
            ->will($this->returnValue('home'));

        $wordpress = $this->getMock('Fancy\Core\Support\Wordpress', array('post', 'get_post_meta'), array(), 'WordpressMock_' . uniqid(), false);

        $wordpress->expects($this->once())
            ->method('post')
            ->will($this->returnValue((object) array('ID' => 1)));

        $wordpress->expects($this->once())
            ->method('get_post_meta')
            ->with($this->equalTo(1), $this->equalTo('page'), $this->equalTo(true))
            ->will($this->returnValue('home'));

        $viewFile = new ViewFile($wordpress, $finder);

        $this->assertEquals($viewFile->intuitByMeta(), 'meta-page-home');
    }
}
