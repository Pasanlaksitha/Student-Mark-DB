
<!DOCTYPE html>

<html lang="eng">

<head>
    <title>Student Marks</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>

<body>

<h2>Retrieve Student Marks</h2>

<div style="text-align: center; display: flex; justify-content: center">
    <form method="post" action="">
        <label for="student_id">Enter Student ID:</label>
        <input type="number" id="student_id" name="student_id" required><br><br>

        <label for="week">Select Week:</label>
        <select id="week" name="week">
            <option value="week1">Week 1</option>
            <option value="week2">Week 2</option>
            <option value="week3">Week 3</option>
            <option value="week4">Week 4</option>
        </select><br><br>

        <input type="submit" name="submit" value="Submit">
    </form>
</div>


</body>

</html>

<?php
//require './includes/visitor.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_mark_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted
if (isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $week = $_POST['week'];

    // SQL query to retrieve marks
    $marks = "SELECT $week FROM student WHERE student_id = $student_id";
    $marks_result = $conn->query($marks);

    $datetime_sql = "SELECT date FROM student WHERE student_id = $student_id";
    $datetime_result = $conn->query($datetime_sql);
    $datetime_row = $datetime_result->fetch_assoc();
    if ($datetime_row) {
        $datetime = $datetime_row['date'];
    } else {
        $datetime = "Not available";
    }

    echo "<br><br><br><h2 css='text-align: center;'>Results</h2>";
    echo "<h4 css='text-align: center;'>Last Update      -    " . $datetime . "</h4>";
    echo "<table css='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th css='border: 1px solid #000; padding: 8px;'>Student ID</th><th css='border: 1px solid #000; padding: 8px;'>Marks for $week</th></tr>";

    if ($marks_result->num_rows > 0) {
        while ($row = $marks_result->fetch_assoc()) {
            echo "<tr><td css='border: 1px solid #000; padding: 8px; text-align: center'>" . $student_id . "</td>";
            echo "<td css='border: 1px solid #000; padding: 8px; text-align: center'>" . $row[$week] . "</td></tr></table><br>";

            $rank_sql = "SELECT student_id, rank FROM (SELECT student_id, RANK() OVER (ORDER BY $week DESC) AS rank FROM student WHERE $week IS NOT NULL) AS ranked_table WHERE student_id = $student_id";

            $rank_result = $conn->query($rank_sql);

            if ($rank_result->num_rows > 0) {
                // Output rank data
                while ($rank_row = $rank_result->fetch_assoc()) {
                    echo "<center><h3 css='color: crimson'>Rank     -     " . $rank_row["rank"] . "</h3></center>";
                }
            } else {
                echo "No rank found for student ID: $student_id in $week";
            }


        }
    } else {
        echo "<center><h3 css='color: crimson'>No results found for student ID: " . $student_id . " and week: " . $week . "</h3></center>";
    }
}
$conn->close();
?>


