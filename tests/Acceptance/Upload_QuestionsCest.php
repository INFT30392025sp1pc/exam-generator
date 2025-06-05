<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Upload_QuestionsCest
{
    public function _before(AcceptanceTester $I): void
    {
        // Code here will be executed before each test.
        $I->setCookie('TEST_MODE', 'true');
        $I->amOnPage('/login.php');

        $I->amOnPage('/login.php');
        $I->see('Login');
        $I->fillField('username', 'test.coordinator@gmail.com');
        $I->fillField('password', '123');
        $I->click('Login');
        $I->see('Welcome');
        $I->see('Coordinator Actions');
        $I->amOnPage('/dashboard.php');
        $I->click("Create Exam Questions");
        $I->see('Please enter details for the exam to be created:');
        $I->selectOption('study_period', 'SP3');
        $I->fillField('exam_name', 'PHP 101');
        $I->selectOption('subject_code', 'AEUO');
        $I->click('Next Step');
        $I->see('Would you like to upload a question file, modify an existing question list, or manually create a new question list?');
        $I->click('Upload');

    }

    public function tryToTestNormal(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->attachFile('Select CSV File', 'test exam questions.csv');
        $I->click('Upload Questions');
        $I->see('Successfully added 3 questions.');

    }


    public function tryToTestNoFileGiven(AcceptanceTester $I): void
    {
        $I->amOnPage('/upload_question_file.php');
        $I->click('Upload Questions');
        $I->see('The file is empty.');
    }

}
