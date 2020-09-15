<?php

// Author: Abrar Mahmud


session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if ($_SESSION["role"] === "student") {
        header("location: student_panel.php");
        exit;
    } else {
        header("location: admin_panel.php");
        exit;
    }
}

require_once "dbconnect.php";

$err_msg  = "";
$email    = "";
$password = "";


if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["email"]))) {
        $err_msg .= "Email can't be empty. ";
    }

    if(empty(trim($_POST["password"]))){
        $err_msg .= "Password can't be empty. ";
    }

    if(empty($err_msg)) {
        $admin_result = $conn->query("SELECT * FROM `admin`");
        $admin_row    = $admin_result->fetch_assoc();

        $email    = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        $admin_email_exists = $email    == $admin_row["Email"];
        $admin_pass_matches = $password == $admin_row["Password"];

        if($admin_email_exists && $admin_pass_matches) {
            $_SESSION["loggedin"]   = true;
            $_SESSION["role"]       = "admin";
            $_SESSION["identifier"] = $admin_row["Email"];
            header("location: admin_panel.php");
        } else if($admin_email_exists && !$admin_pass_matches) {
            $err_msg .= "Password Incorrect. ";
        }
    }

    if(empty($err_msg) && !(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
        $student_result = $conn->query("SELECT * FROM `student`");
        $matched_row = NULL;

        $student_email_exists = false;
        $student_pass_matches = false;

        while($student_row = $student_result->fetch_assoc()) {
            $email    = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            $student_email_exists = $email    == $student_row["Email"];
            $student_pass_matches = $password == $student_row["Password"];

            if($student_email_exists) {
                $matched_row = $student_row;
                break;
            }
        }

        if(!is_null($matched_row)) {
            if($student_email_exists && $student_pass_matches) {
                $_SESSION["loggedin"]   = true;
                $_SESSION["role"]       = "student";
                $_SESSION["identifier"] = $matched_row["Student_ID"];
                header("location: student_panel.php");
            } else if($student_email_exists && !$student_pass_matches) {
                $err_msg .= "Password Incorrect. ";
            }
        } else {
            $err_msg .= "No entry exists for: " . $_POST["email"];
        }
    }
}

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
            box-shadow: 0px 0px 28px 3px rgba(0, 0, 0, 0.75);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if(!empty($err_msg)) : ?>
            <div class="alert alert-danger" role="alert"><?php echo $err_msg; ?></div>
            <?php endif ?>

            <div class="form-group <?php echo (!empty($err_msg)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            </div>    
            <div class="form-group <?php echo (!empty($err_msg)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>    
</body>
</html>