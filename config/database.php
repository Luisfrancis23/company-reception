<?php
/* =========================================================
   DATABASE CONNECTION FILE
   This file connects our PHP project to the MySQL database
   using PDO (PHP Data Objects). PDO lets us use Prepared
   Statements, which protect the site from SQL Injection.
   ========================================================= */

// ---- Database settings (default XAMPP settings) ----
$db_host = "localhost";
$db_name = "company_reception";
$db_user = "root";
$db_pass = ""; // XAMPP default has no password for root

try {
    // Create a new PDO connection
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass
    );

    // Show real errors as exceptions (easier for beginners to debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Stop the script and show a friendly error if connection fails
    die("Database connection failed: " . $e->getMessage());
}
