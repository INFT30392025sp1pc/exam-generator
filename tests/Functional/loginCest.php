<?php

declare(strict_types=1);


namespace Tests\Functional;

use Tests\Support\FunctionalTester;

final class loginCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function trySuccessfulLogin(FunctionalTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'j-smith@email.com');
        $I->fillField('password', md5(''));
        $I->click('Login');
        $I->seeCurrentUrlEquals('/exam-generator/login.php');

    }

    public function tryForgetPassword(FunctionalTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->see('Forgotten password');
        $I->click('Forgotten password');
        $I->see('404 Not Found');
    }
}
