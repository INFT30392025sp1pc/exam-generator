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

    public function testForgetPassword(AcceptanceTester $I): void
    {
        $I->amOnPage('/login.php');
        $I->click('Forgotten password');
        $I->see('please contact your administrator');
        $I->amOnPage('/forgot_password.php');
    }

    public function testURLInjection(AcceptanceTester $I)
    {
        // Test SQL injection via URL
        $I->amOnPage('/login?username=admin%27+OR+%271%27%3D%271&password=anypassword');
        $I->dontSee('Welcome'); // Should not log in

        // Test XSS via URL
        $I->amOnPage('/login?username=<script>alert(1)</script>');
        $I->dontSeeElement('script'); // Should not render script tags

        // Test path traversal (this is Linux specific)
        $I->amOnPage('/login?redirect=../../etc/passwd');
        $I->dontSee('root:x:0:0'); // Should not expose system files
    }

    public function testOpenRedirect(AcceptanceTester $I)
    {
        $I->amOnPage('/login.php?redirect=https://evil.com');
        $I->dontSeeCurrentUrlEquals('https://evil.com'); // Should not redirect to external site
    }

    public function testParameterPollution(AcceptanceTester $I)
    {
        $I->amOnPage('/login.php?user=admin&user=attacker');
        $I->fillField('password', 'wrongpass');
        $I->click('Login');
        $I->dontSee('Welcome, attacker'); // Should not allow multiple users
    }

}
