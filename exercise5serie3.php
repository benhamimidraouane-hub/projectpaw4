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

function createSessionsTable()
{
    $conn = connectDatabase();
    if ($conn) {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS attendance_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                course_id VARCHAR(20) NOT NULL,
                group_id VARCHAR(10) NOT NULL,
                date DATE NOT NULL,
                opened_by VARCHAR(50) NOT NULL,
                status ENUM('open', 'closed') DEFAULT 'open',
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

    if (isset($_POST['create_session'])) {
        $course_id = $_POST['course_id'];
        $group_id = $_POST['group_id'];
        $opened_by = $_POST['opened_by'];
        $date = date('Y-m-d');

        if ($conn) {
            try {
                $sql = "INSERT INTO attendance_sessions (course_id, group_id, date, opened_by) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$course_id, $group_id, $date, $opened_by]);
                $session_id = $conn->lastInsertId();
                $message = "✅ Session created! ID: $session_id";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['close_session'])) {
        $session_id = $_POST['session_id'];

        if ($conn) {
            try {
                $sql = "UPDATE attendance_sessions SET status = 'closed' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$session_id]);
                $message = "✅ Session closed successfully!";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
            }
        }
    }
}
function getSessions()
{
    $conn = connectDatabase();
    if ($conn) {
        try {
            $sql = "SELECT * FROM attendance_sessions ORDER BY created_at DESC";
            $stmt = $conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    return [];
}

createSessionsTable();
$sessions = getSessions();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Exercise 5 - Attendance Sessions</title>
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

        .open {
            color: green;
        }

        .closed {
            color: red;
        }
    </style>
</head>

<body>
    <h1>Exercise 5 - Attendance Sessions</h1>

    <?php if (isset($message))
        echo "<p>$message</p>"; ?>

    <h2>Create Session</h2>
    <form method="post">
        <div class="form-group">
            <label>Course ID:</label><br>
            <input type="text" name="course_id" required>
        </div>
        <div class="form-group">
            <label>Group ID:</label><br>
            <input type="text" name="group_id" required>
        </div>
        <div class="form-group"></div>
        <label>Opened By:</label><br>
        <input type="text" name="opened_by" required>
        </div>
        <button type="submit" name="create_session">Create Session</button>
    </form>

    <!-- جزء 2: قائمة الجلسات -->
    <h2>Sessions List</h2>
    <?php if (count($sessions) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Group</th>
                <th>Date</th>
                <th>Opened By</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?= $session['id'] ?></td>
                    <td><?= $session['course_id'] ?></td>
                    <td><?= $session['group_id'] ?></td>
                    <td><?= $session['date'] ?></td>
                    <td><?= $session['opened_by'] ?></td>
                    <td class="<?= $session['status'] ?>"><?= $session['status'] ?></td>
                    <td>
                        <?php if ($session['status'] == 'open'): ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                <button type="submit" name="close_session">Close</button>
                            </form>
                        <?php else: ?>
                            <span>Closed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No sessions found.</p>
    <?php endif; ?>
</body>

</html>