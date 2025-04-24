<?php
$conn = new mysqli('localhost', 'root', '', 'exam-generator-testdb');

if ($conn->connect_error) {
    die("Test DB Connection failed: " . $conn->connect_error);
}