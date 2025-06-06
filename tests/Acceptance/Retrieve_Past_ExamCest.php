<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Retrieve_Past_ExamCest
{
    public function _before(AcceptanceTester $I): void
    {
        // Code here will be executed before each test.
        // Code here will be executed before each test.
        $I->setCookie('TEST_MODE', 'true');
        $I->amOnPage('/login.php');

        $I->see('Login');
        $I->fillField('username', 'test.coordinator@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->see('Coordinator Actions');
        $I->amOnPage('/dashboard.php');
        $I->click("Retrieve Past Exams");
    }

    public function tryToTestCheckHistoricalExamNormal(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/retrieve_past_exams.php');
        $I->see('Advanced Engineering Truss Exam');
        $I->see('Roof Truss Design 101');
        $I->click('CSV');
    }
}
