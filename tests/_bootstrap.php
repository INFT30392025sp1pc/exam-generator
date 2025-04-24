<?php
// Override db.php for all tests
copy(__DIR__ . '/Data/test_db.php', __DIR__.'/../../db.php');

// Register cleanup (optional)
register_shutdown_function(function() {
    if (file_exists(__DIR__.'/../../db.php.backup')) {
        copy(__DIR__.'/../../db.php.backup', __DIR__.'/../../db.php');
    }
});