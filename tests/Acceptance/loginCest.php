<?php

use Tests\Support\AcceptanceTester;

class loginCest {
    protected $validUsername = 'test@example.com';
    protected $validPassword = 'correct_password';
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
        $I->fillField('Username', $this->validUsername);
        $I->fillField('Password', $this->validPassword);
        $I->click('Login');

        // Verify successful login
        $I->seeCurrentUrlEquals('/dashboard.php');
        $I->seeInCurrentUrl('dashboard.php');
    }

    public function testFailedLogin(AcceptanceTester $I)
    {
        $I->wantTo('Fail login with invalid credentials');
        $I->amOnPage('/login.php');

        // Fill with invalid credentials
        $I->fillField('Username', $this->invalidUsername);
        $I->fillField('Password', $this->invalidPassword);
        $I->click('Login');

        // Verify error message and staying on login page
        $I->seeCurrentUrlEquals('/login.php');
        $I->see('Invalid username or password!');
    }

    public function testFormValidation(AcceptanceTester $I)
    {
        $I->wantTo('Test form validation');
        $I->amOnPage('/login.php');

        // Try to submit empty form
        $I->click('Login');

        // Verify validation errors
        $I->seeCurrentUrlEquals('/login.php');
        $I->seeElement('input[name="username"][required]');
        $I->seeElement('input[name="password"][required]');
    }


}
