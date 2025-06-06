<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Manage_Exam_DataCest
{
    public function _before(AcceptanceTester $I): void
    {
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
        $I->click("Manage Exam Data");
    }

    public function tryToTestArchiveNormal(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->amOnPage('/manage_exam_data.php');
        $I->click('Archive');
        $I->click('Unarchive');
    }
}
