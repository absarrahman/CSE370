<?php

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["role"] === "admin") {
        header("location: admin_panel.php");
        exit;
    }
} else {
    header("location: login.php");
    exit;
}

require_once "dbconnect.php";

$stid = $_SESSION["identifier"];
$state_student = $conn->query(sprintf("SELECT * FROM student WHERE student.Student_ID = %s;", $stid))->fetch_assoc();

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Student Panel</title>

    <link href="./static/css/bootstrap.min.css" rel="stylesheet">
    <link href="./static/css/jumbotron.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a href="logout.php" class="btn btn-outline-danger my-2 my-sm-0 float-right">Logout</a>
    </nav>

    <main role="main">

        <div class="jumbotron">
            <div class="container">
                <h1 class="display-3">Hello, <?php echo $state_student["Fname"] . " " . $state_student["Lname"]; ?>!</h1>
                <p>You can apply for a scholarship and view your previous ones.</p>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2>Apply for scholarship</h2>
                    <p>Request for a scholarship by submitting an application</p>
                    <p><a class="btn btn-secondary" href="#" role="button">Go »</a></p>
                </div>
                <div class="col-md-4">
                    <h2>View Request</h2>
                    <p>View your last scholarship request</p>
                    <p><a class="btn btn-secondary" href="#" role="button">Go »</a></p>
                </div>
                <div class="col-md-4">
                    <h2>Scholarship History</h2>
                    <p>Show your scholarship history</p>
                    <p><a class="btn btn-secondary" href="#" role="button">Go »</a></p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <h2>Show Status</h2>
                    <p>Show whether your scholarship request has been approved or not</p>
                    <p><a class="btn btn-secondary" href="#" role="button">Go »</a></p>
                </div>
            </div>

            <hr>

        </div>

    </main>

    <footer class="container">
        <p>CSE370 Section 06 Group 02</p>
    </footer>

    <script src="./static/js/jquery-3.5.1.min.js"></script>
    <script src="./static/js/jquery-3.2.1.slim.min.js"></script>
    <script src="./static/js/popper.min.js"></script>
    <script src="./static/js/bootstrap.min.js"></script>

</body>

</html>