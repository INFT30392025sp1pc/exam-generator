<?php

use Tests\Support\AcceptanceTester;

class loginCest {
    protected $validUsername = 'j-smith@email.com';
    protected $validPassword = '';
    protected $invalidUsername = 'wrong@example.com';
    protected $invalidPassword = 'wrong_password';

    public function _before(AcceptanceTester $I)
    {
        // Set up test data in database
        $I->haveInDatabase('user', [
            'user_email' => $this->validUsername,
            'user_password' => md5($this->validPassword)
        ]);
    }

    public function testSuccessfulLogin(AcceptanceTester $I)
    {
        $I->wantTo('Login with valid credentials');
        $I->amOnPage('/login.php');
        $I->see('Login');

        // Fill and submit the form
        $I->fillField('username', $this->validUsername);
        $I->fillField('password', $this->validPassword);
        $I->click('Login');

        // Verify successful login
        $I->see('Welcome');
    }

}
