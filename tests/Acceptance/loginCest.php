<?php

use Tests\Support\AcceptanceTester;

class loginCest {
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/login.php');
        $I->haveInDatabase('user', [
            'user_id' => '123',
            'user_password' => md5('password')
        ]);
    }

    public function testSuccessfulLogin(AcceptanceTester $I)
    {
        $I->fillField('input[name="username"]', '123');
        $I->fillField('input[name="password"]', '123');
        $I->click('Login');
        $I->see('Welcome');
    }
}
