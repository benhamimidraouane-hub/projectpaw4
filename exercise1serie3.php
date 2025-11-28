<!DOCTYPE html>
<html>

<head>
    <title>Exercise 1 - Add Student</title>
    <meta charset="utf-8">
</head>

<body>
    <h1>Add Student (JSON Version)</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_id = $_POST['student_id'];
        $name = $_POST['name'];
        $group = $_POST['group'];

        $errors = [];
        if (empty($student_id))
            $errors[] = "Student ID is required";
        if (empty($name))
            $errors[] = "Name is required";
        if (empty($group))
            $errors[] = "Group is required";

        if (empty($errors)) {
            $students = [];
            if (file_exists('students.json')) {
                $students = json_decode(file_get_contents('students.json'), true);
            }
            $new_student = [
                'student_id' => $student_id,
                'name' => $name,
                'group' => $group
            ];
            $students[] = $new_student;
            file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT));

            echo "<p style='color: green;'>Student added successfully!</p>";
        } else {
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        }
    }
    ?>

    <form method="post">
        <div>
            <label>Student ID:</label><br>
            <input type="text" name="student_id" required>
        </div>
        <div>
            <label>Name:</label><br>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Group:</label><br>
            <input type="text" name="group" required>
        </div>
        <button type="submit">Add Student</button>
    </form>

    <hr>
    <h3>Current Students:</h3>
    <?php
    if (file_exists('students.json')) {
        $students = json_decode(file_get_contents('students.json'), true);
        if (!empty($students)) {
            echo "<ul>";
            foreach ($students as $student) {
                echo "<li>ID: {$student['student_id']} - Name: {$student['name']} - Group: {$student['group']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No students yet.</p>";
        }
    } else {
        echo "<p>No students file found.</p>";
    }
    ?>
</body>

</html>