<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Create_Exam_QuestionsCest
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
    }

    public function tryToTestCreateExamQuestionsNormal(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->fillField('study_period', 'SP3');
        $I->fillField('exam_name', 'PHP 101');
        $I->see('Please enter details for the exam to be created:');
        $I->selectOption('subject_code', 'php101');
        $I->click('Next Step');
        $I->see('Would you like to upload a question file, modify an existing question list, or manually create a new question list?');

    }

    public function tryToTestCreateExamQuestionsMissingInfo(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->fillField('study_period', 'SP3');
        $I->fillField('exam_name', 'PHP 101');
        $I->see('Please enter details for the exam to be created:');
        $I->click('Next Step');
        $I->amOnPage('/create_exam_questions.php');

    }



}
