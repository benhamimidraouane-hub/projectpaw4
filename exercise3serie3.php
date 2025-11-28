<?php
//config.php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'attendance_system');

//db_connect.php
function connectDatabase()
{
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        error_log("Connection failed: " . $e->getMessage(), 3, "errors.log");
        return null;
    }
}

//test connection
echo "<h1>Exercise 3 - Database Connection</h1>";

$conn = connectDatabase();

if ($conn) {
    echo "<h2 style='color: green;'>✅ Connection successful!</h2>";
    echo "<p>Connected to database: " . DB_NAME . "</p>";
} else {
    echo "<h2 style='color: red;'>❌ Connection failed!</h2>";
}
?>