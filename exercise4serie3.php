<?php

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'attendance_system');

function connectDatabase()
{
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        return null;
    }
}

function createStudentsTable()
{
    $conn = connectDatabase();
    if ($conn) {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS students (
                id INT AUTO_INCREMENT PRIMARY KEY,
                fullname VARCHAR(100) NOT NULL,
                matricule VARCHAR(20) NOT NULL UNIQUE,
                group_id VARCHAR(10) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->exec($sql);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectDatabase();

    if (isset($_POST['add_student'])) {
        $fullname = $_POST['fullname'];
        $matricule = $_POST['matricule'];
        $group_id = $_POST['group_id'];

        if ($conn) {
            try {
                $sql = "INSERT INTO students (fullname, matricule, group_id) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$fullname, $matricule, $group_id]);
                $message = "✅ Student added successfully!";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['delete_student'])) {
        // حذف طالب
        $student_id = $_POST['student_id'];

        if ($conn) {
            try {
                $sql = "DELETE FROM students WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$student_id]);
                $message = "✅ Student deleted successfully!";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
            }
        }
    }
}

function getStudents()
{
    $conn = connectDatabase();
    if ($conn) {
        try {
            $sql = "SELECT * FROM students ORDER BY id DESC";
            $stmt = $conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    return [];
}

createStudentsTable();
$students = getStudents();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Exercise 4 - Students Management</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        .form-group {
            margin: 15px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Exercise 4 - Students Management</h1>

    <?php if (isset($message))
        echo "<p>$message</p>"; ?>

    <h2>Add Student</h2>
    <form method="post">
        <div class="form-group">
            <label>Full Name:</label><br>
            <input type="text" name="fullname" required>
        </div>
        <div class="form-group">
            <label>Matricule:</label><br>
            <input type="text" name="matricule" required>
        </div>
        <div class="form-group">
            <label>Group ID:</label><br>
            <input type="text" name="group_id" required>
        </div>
        <button type="submit" name="add_student">Add Student</button>
    </form>

    <h2>Students List</h2>
    <?php if (count($students) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Matricule</th>
                <th>Group</th>
                <th>Action</th>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['id'] ?></td>
                    <td><?= $student['fullname'] ?></td>
                    <td><?= $student['matricule'] ?></td>
                    <td><?= $student['group_id'] ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                            <button type="submit" name="delete_student"
                                onclick="return confirm('Delete student?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No students found.</p>
    <?php endif; ?>
</body>

</html>