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

    public function testSuccessfulLoginAsAdmin(AcceptanceTester $I): void
    {

        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test.admin@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->amOnPage('/dashboard.php');
    }

    public function testSuccessfulLoginAsCoordinator(AcceptanceTester $I): void
    {

        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test.coordinator@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->see('Coordinator');
        $I->amOnPage('/dashboard.php');
    }

    public function testUnSuccessfulLogin2(AcceptanceTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Invalid username or password!');
        $I->amOnPage('/login.php');
    }

    public function testSQLInjection1(AcceptanceTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'INSERT INTO table_name (column1, column2, column3)
        VALUES (value1, value2, value3);');
        $I->fillField('password', '');
        $I->click('Login');
        $I->see('Invalid username or password!');
        $I->amOnPage('/login.php');
    }

    public function testSQLInjection2(AcceptanceTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->see('Login');

        //Try comment out the rest of the query nullifying password checks
        $I->fillField('username', "' --admin' /*");
        $I->fillField('password', '');
        $I->click('Login');
        $I->see('Invalid username or password!');
        $I->amOnPage('/login.php');
    }

    public function testSQLInjection3(AcceptanceTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->see('Login');

        //Try use or keyword to make the where clause always true
        $I->fillField('username', "' OR '1'='1");
        $I->fillField('password', '');
        $I->click('Login');
        $I->see('Invalid username or password!');
        $I->amOnPage('/login.php');
    }

}
