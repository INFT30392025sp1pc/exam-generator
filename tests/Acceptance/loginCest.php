<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class loginCest
{
    public function _before(AcceptanceTester $I): void
    {

        $I->setCookie('TEST_MODE', 'true');

    }

    public function _after(AcceptanceTester $I): void
    {

    }

    public function testSuccessfulLogin1(AcceptanceTester $I): void
    {

        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test@email.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->amOnPage('/dashboard.php');
    }

}
