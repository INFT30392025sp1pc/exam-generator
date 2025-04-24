<?php
/**
 * This file is used to bypass login requirements
 * in local testing environment
 * when tester is trying to access a local app file
 * that requires authentication
 * it can be done by entering
 * "http://localhost/exam-generator/dev_auth.php?return=/path/to/protected.php"
 * in browser and replace the end after "return" to the path to
 * the file being tested
 */

session_start();
$_SESSION['username'] = 'devuser';
$_SESSION['authenticated'] = true;
header("Location: ".$_GET['return'] ?? '/');