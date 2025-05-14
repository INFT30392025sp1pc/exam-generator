<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Add_Modify_SubjectsCest
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
        $I->click('Add/Modify Subjects');
        $I->see('Add Subject');
    }

    public function tryToTestAddSubjectNormal(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->click('Add Subject');
        $I->see('Please complete the fields below to add a new Subject:');
        $I->fillField('subject_name', 'test subject');
        $I->fillField('subject_code', 'test code');
        $I->click('Save');
    }

    public function tryToTestAddSubjectWithAlreadyExistSubject(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->click('Add Subject');
        $I->see('Please complete the fields below to add a new Subject:');
        $I->fillField('subject_name', 'test subject');
        $I->fillField('subject_code', 'test code');
        $I->click('Save');
    }

    public function tryToTestModifySubject(AcceptanceTester $I): void
    {
        $I->click('Modify Subject');
        $I->see('Toggle on to enable subject, toggle off to archive');
        $I->click('#toggleSwitch');
    }
}
