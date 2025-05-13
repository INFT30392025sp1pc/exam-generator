<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Change_passwordCest
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
        $I->click( "Change/update password");
        $I->amOnPage('/change_password.php');
    }

    public function tryToTestValidPassword(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->see('Please enter and confirm your new password:');
        $I->fillField('new_password', '12345678');
        $I->fillField('confirm_password', '12345678');
        $I->click('Save');
        // successful password change will return to dashboard
        $I->amOnPage('/dashboard.php');

    }


    public function tryToTestShortPassword(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->see('Please enter and confirm your new password:');
        $I->fillField('new_password', '12345678');
        $I->fillField('confirm_password', '123');
        $I->click('Save');
        // this change should fail because password is too short
        $I->amOnPage('/change_password.php');

        //The following test would check alter pop out, it's disabled
        // because it requires additional selenium standalone server
        // and specific version of webdriver to match the local web browser
        // used, if these prerequisites are not met, it would fail
//        $I->see('Password must be at least 8 characters');
    }
}
