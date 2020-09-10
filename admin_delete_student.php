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

require_once "dbconnect.php";

$deleted = false;

function deletion() {
    global $deleted;
    global $conn;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $deleted = true;
        $stid = empty(trim($_POST["stid"])) ? NULL : trim($_POST["stid"]);
        if(is_null($stid)) {
            return "ID cannot be empty";
        }
        $del_sql = sprintf("DELETE FROM student WHERE student.Student_ID = %s;", $stid);
        $query = $conn->query($del_sql);
        if(mysqli_affected_rows($conn) > 0) {
            return "OK";
        } else {
            return "Deletion unsuccessful. Given ID may not exist";
        }
    }
}

$msg = deletion();

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./static/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
            background-color: #400e7d;
        }

        .wrapper {
            width: 600px;
            padding: 40px;
            margin: auto;
            background-color: white;
            padding-bottom: 40px;
            margin-top: 80px;
            margin-bottom: 80px;
            border-radius: 20px;
        }

        .wrapper {
            width: 350px;
            padding: 20px;
        }

    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Delete Student by ID</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if ($deleted) : ?>
                <?php if ($msg === "OK") : ?>
                    <div class="alert alert-success" role="alert">Deletion Success (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php else : ?>
                    <div class="alert alert-danger" role="alert"><?php echo $msg; ?> (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php endif ?>
            <?php endif ?>

            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="stid" class="form-control">
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Delete">
            </div>
        </form>
    </div>
</body>

</html>