<?php

declare(strict_types=1);


namespace Tests\Functional;

use Tests\Support\FunctionalTester;

final class loginCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test.
        $I->setHeader('X_TEST_MODE', 'true');
    }

    public function trySuccessfullLogin01(FunctionalTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('exam-generator/login.php');
        $I->see('Login');
        $I->fillField('username', 'test@email.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->amOnPage('/dashboard.php');
    }

    public function trySuccessfullLogin02(FunctionalTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('exam-generator/login.php');
        $I->see('Login');
        $I->fillField('username', 'j_smith@gmail.com');
        $I->fillField('password', 'abc');
        $I->click('Login');
        $I->see('Welcome');
        $I->amOnPage('/dashboard.php');
    }
}
