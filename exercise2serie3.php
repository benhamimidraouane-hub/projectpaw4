<!DOCTYPE html>
<html>

<head>
    <title>Exercise 2 - Take Attendance</title>
    <meta charset="utf-8">
</head>

<body>
    <h1>Take Attendance</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $today = date('Y-m-d');
        $filename = "attendance_$today.json";
        if (file_exists($filename)) {
            echo "<p style='color: red;'>Attendance for today has already been taken.</p>";
        } else {
            $attendance = [];
            foreach ($_POST['attendance'] as $student_id => $status) {
                $attendance[] = [
                    'student_id' => $student_id,
                    'status' => $status
                ];
            }
            file_put_contents($filename, json_encode($attendance, JSON_PRETTY_PRINT));
            echo "<p style='color: green;'>Attendance saved successfully for $today!</p>";
        }
    }
    $students = [];
    if (file_exists('students.json')) {
        $students = json_decode(file_get_contents('students.json'), true);
    }
    ?>

    <?php if (!empty($students)): ?>
        <form method="post">
            <table border="1" style="border-collapse: collapse; width: 100%;">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Group</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['name']; ?></td>
                        <td><?php echo $student['group']; ?></td>
                        <td>
                            <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="present"
                                checked>
                        </td>
                        <td>
                            <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="absent">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit">Save Attendance</button>
        </form>
    <?php else: ?>
        <p>No students found. Please add students first.</p>
        <a href="exercise1.php">Add Students</a>
    <?php endif; ?>
</body>

</html>