<?php

use Gladeye\LaPress\Support\Factory;
use Gladeye\LaPress\Facade\LaPress;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFactoryMagicMethodCall()
    {
        $app['la-press.view'] = 'view';

        $factory = new Factory($app);

        $this->assertEquals($factory->view(), 'view');
    }

    public function testFactoryMagicMethodCallWithException()
    {
        $app['la-press.view'] = 'view';

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
        $app['la-press'] = new Factory(array('la-press.view' => 'view'));

        LaPress::setFacadeApplication($app);

        $this->assertEquals(LaPress::view(), 'view');
    }
}
