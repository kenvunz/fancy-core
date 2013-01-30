<?php

use Fancy\Core\Support\Factory;
use Fancy\Core\Facade\Core;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFactoryMagicMethodCall()
    {
        $app['fancy.view'] = 'view';

        $factory = new Factory($app);

        $this->assertEquals($factory->view(), 'view');
    }

    public function testFactoryMagicMethodCallWithException()
    {
        $app['fancy.view'] = 'view';

        $factory = new Factory($app);

        try {
            $factory->view1();
        } catch (\RuntimeException $e) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testFactoryMagicMethodCallViaFacade()
    {
        $app['fancy'] = new Factory(array('fancy.view' => 'view'));

        Core::setFacadeApplication($app);

        $this->assertEquals(Core::view(), 'view');
    }
}
