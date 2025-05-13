<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class DashboardAsAdminCest
{
    public function _before(AcceptanceTester $I): void
    {
        // Code here will be executed before each test.
        // provide admin credentials
        $I->setCookie('TEST_MODE', 'true');
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test.admin@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->amOnPage('/dashboard.php');
        $I->see('Administrator');
    }

//    public function _after(AcceptanceTester $I): void
//    {
//        $I->amOnPage('/dashboard.php');
//        $I->click('Logout');
//    }


    public function tryToTestChangePassword(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->click( "Change/update password");
        $I->see('Please enter and confirm your new password:');
        $I->amOnPage('/change_password.php');
        //return to dashboard to reset after test
        $I->click('Back');
    }

    public function tryToTestAdd_Modify_Users(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/dashboard.php');
        $I->click( "Add/Modify Users");
        $I->amOnPage('users.php');
        $I->see('Add User');
        $I->see('Modify User');
        //return to dashboard to reset after test
        $I->click('Back');
    }

    public function tryToTestAdd_Modify_Subjects(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/dashboard.php');
        $I->click( "Add/Modify Subjects");
        $I->amOnPage('subjects.php');
        $I->see('Add Subject');
        $I->see('Modify Subject');
        //return to dashboard to reset after test
        $I->click('Back');
    }

    public function tryToTestLogOut(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/dashboard.php');
        $I->click( "Logout");
        $I->see("Login");
        $I->amOnPage('login.php');
    }

    public function tryToTestBack(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/dashboard.php');
        $I->click( "Change/update password");
        $I->see('Please enter and confirm your new password:');
        $I->click( "Back");
        $I->amOnPage('dashboard.php');
    }
}




