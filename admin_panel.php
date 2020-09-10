<?php

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["role"] === "student") {
        header("location: student_panel.php");
        exit;
    }
} else {
    header("location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin Panel</title>

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
                <h1 class="display-3">Hello, Admin!</h1>
                <p>Here you can insert/view/edit/delete student info and approve/decline their scholarship requests.</p>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2>Insert Student</h2>
                    <p>Add a new student with their detailed information</p>
                    <p><a class="btn btn-secondary" href="admin_insert_student.php" role="button">Go »</a></p>
                </div>
                <div class="col-md-4">
                    <h2>Delete Student</h2>
                    <p>Remove a student from the database</p>
                    <p><a class="btn btn-secondary" href="admin_delete_student.php" role="button">Go »</a></p>
                </div>
                <div class="col-md-4">
                    <h2>Student List</h2>
                    <p>Show students as a list. In a sorted manner</p>
                    <p><a class="btn btn-secondary" href="#" role="button">Go »</a></p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <h2>Search Student</h2>
                    <p>Search for a student by Name or ID</p>
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
