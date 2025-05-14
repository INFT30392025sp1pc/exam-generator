<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Add_Modify_UserCest
{
    public function _before(AcceptanceTester $I): void
    {
        // Code here will be executed before each test.
        $I->setCookie('TEST_MODE', 'true');
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test.admin@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->amOnPage('/dashboard.php');
        $I->click('Add/Modify Users');
        $I->amOnPage('/users.php');
    }

    public function tryToTestAddUser(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->click('Add User');
        $I->amOnPage('/add_user.php');
        $I->see("Please complete the fields below to add a new user:");
        $I->fillField('email', 'test@gmail.com');
        $I->fillField('first_name', 'John');
        $I->fillField('last_name', 'Smith');
        $I->selectOption('role', 'Administrator');
        $I->fillField('password', '123');
        $I->click('Save');
        $I->amOnPage('/users.php');
    }

    public function tryToTestModifyUser(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->click('Modify User');
        $I->amOnPage('/modify_user.php');
        $I->selectOption('users', 'alice.smith@gmail.com');
        $I->selectOption('new_role', 'Coordinator');
        $I->click('Save');
    }
}
