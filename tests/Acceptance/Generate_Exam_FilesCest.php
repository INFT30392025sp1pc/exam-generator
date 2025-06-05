<?php

declare(strict_types=1);


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

final class Generate_Exam_FilesCest
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
        $I->click("Generate Exam Files");
        $I->see('Please enter details for the exam to be generated');
        $I->amOnPage('/generate_exam_files.php');
    }

    public function tryToTestNormalUploadStudentCSV(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->selectOption('question_ID', '309');
        $I->click('Next');
        $I->attachFile('student_csv', 'student.csv');
        $I->click('Upload CSV');
        $I->see("CSV file 'student.csv' uploaded successfully. Students will be merged with existing list");
        $I->see('Add Student');
    }

    public function tryToTestNumberOfExamGenerated(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->selectOption('question_ID', '309');
        $I->click('Next');
        $I->attachFile('student_csv', 'student.csv');
        $I->click('Upload CSV');
        $I->see("CSV file 'student.csv' uploaded successfully. Students will be merged with existing list");
        $I->see('Add Student');
        $I->click('Next');
        $I->see("(156 Pending Exams)");

    }

    public function tryToTestGeneratePDF(AcceptanceTester $I): void
    {
        // Write your tests here. All `public` methods will be executed as tests.
        $I->selectOption('question_ID', '309');
        $I->click('Next');
        $I->attachFile('student_csv', 'student.csv');
        $I->click('Upload CSV');
        $I->see("CSV file 'student.csv' uploaded successfully. Students will be merged with existing list");
        $I->see('Add Student');
        $I->click('Next');
        $I->see("(156 Pending Exams)");
        $I->click('Generate (Create PDFs)');
        $I->see("CSV Summary Available:");
        $I->see("Download");
        $I->click('Download');

    }
}
