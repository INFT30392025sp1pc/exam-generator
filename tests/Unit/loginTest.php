<?php


namespace Tests\Unit;

use Codeception\Stub;
use Tests\Support\UnitTester;
use  Codeception\Stub as s;

class loginTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {


    }

    public function _after()
    {
        //Tear down pre-test setting
        session_destroy();
    }


}
