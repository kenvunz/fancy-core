<?php

use Fancy\Core\Facade\Core;
use Fancy\Core\View\Environment;

class EnviromentTest extends \TestCase
{

    public function testInstantiationViaFacade()
    {
        $this->assertTrue(Core::view() instanceof Environment);
    }

}
