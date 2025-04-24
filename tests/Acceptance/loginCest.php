<?php

use Tests\Support\AcceptanceTester;

class loginCest {
    protected $validUsername = 'j-smith@email.com';
    protected $validPassword = '';
    protected $invalidUsername = 'wrong@example.com';
    protected $invalidPassword = 'wrong_password';

    //insert preset values to test database
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

    public function testFailedLogin(AcceptanceTester $I)
    {
        $I->wantTo('Login with incorrect credentials');
        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', $this->invalidUsername);
        $I->fillField('password', $this->invalidPassword);
        $I->click('Login');
        $I->see('Invalid username or password!');
    }

}
