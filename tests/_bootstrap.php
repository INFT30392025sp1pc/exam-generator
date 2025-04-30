<?php

//Set a static state only true during test
class TestState { public static $isTestMode = true; }

// In bootstrap.php or _before() hook:
$_COOKIE['TEST_MODE'] = 'true';  // Works for HTTP requests
// OR
$_SERVER['HTTP_X_TEST_MODE'] = 'true';  // Custom header


?>

