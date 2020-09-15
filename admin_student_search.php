<?php

// Author: Absar Rahman Prottoy

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


$search_value = "";

if (isset($_GET["submit"])) {
    $search_value = $_GET["search_value"];
}

$by_id = "SELECT * FROM `student` WHERE `Student_ID` = $search_value";
$by_name = "SELECT * FROM `student` WHERE `Fname` = \"$search_value\"";
$action = "";

function getSelected($search)
{
    if (isset($_GET["search"])) {
        if ($_GET["search"] === $search) {
            return "selected";
        } else {
            return "";
        }
    } else {
        return "";
    }
}

if (isset($_GET["search"])) {
    switch ($_GET["search"]) {
        case "by_name":
            $action = $by_name;
            break;
        case "by_id":
            $action = $by_id;
            break;
    }
}


$result_response = array();

if (!empty($action) && !empty($search_value)) {
    $result_response = mysqli_query($conn, $action);

    if (!$result_response) {
        die("Result response not found" . mysqli_error($conn));
    }
}

$conn->close();

?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Student Search</title>
        <link rel="stylesheet" href="./static/css/bootstrap.css">
        <style type="text/css">
            body {
                font: 14px sans-serif;
                background-color: #400e7d;
            }

            .wrapper {
                width: 900px;
                padding: 20px;
                margin: auto;
                background-color: white;
                padding-bottom: 40px;
                margin-top: 30px;
                margin-bottom: 30px;
                border-radius: 20px;
                box-shadow: 0px 0px 28px 3px rgba(0, 0, 0, 0.75);
            }
            .attrib {
                padding: 7px;
            }
        </style>
    </head>

    <body>
        <div class="wrapper">
            <form action="admin_student_search.php" method="get">
                <div class="form-group">
                    <label for="options">Search By</label>
                    <select name="search" class="form-control" id="options">
                        <option value="by_id" <?php echo getSelected("by_id"); ?>>ID</option>
                        <option value="by_name" <?php echo getSelected("by_name"); ?>>Name</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Search Text</label>
                    <input type="text" class="form-control" name="search_value">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="submit" value="Search">
                </div>
            </form>
        </div>

        <?php if (!empty($action)) : ?>
            <?php if (mysqli_num_rows($result_response) === 0) : ?>
                <div class="wrapper">
                    <div class="d-flex justify-content-center">
                        <p class="h1">No Results</p>
                    </div>
                </div>
            <?php endif ?>
            <?php while ($row = $result_response->fetch_assoc()) : ?>
                <div class="wrapper container">
                    <div class="row">
                        <div class="col-lg-3 attrib"><b>First Name:<b> <?php echo $row["Fname"]; ?></div>
                        <div class="col-lg-3 attrib"><b>Last Name:<b> <?php echo $row["Lname"]; ?></div>
                        <div class="col-lg-3 attrib"><b>Student ID:<b> <?php echo $row["Student_ID"]; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 attrib"><b>Street:<b> <?php echo $row["Street"]; ?></div>
                        <div class="col-lg-3 attrib"><b>City:<b> <?php echo $row["City"]; ?></div>
                        <div class="col-lg-3 attrib"><b>House:<b> <?php echo $row["House"]; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 attrib"><b>Email:<b> <?php echo $row["Email"]; ?></div>
                        <div class="col-lg-3 attrib"><b>Semester:<b> <?php echo $row["Semester"]; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 attrib"><b>CGPA:<b> <?php echo $row["CGPA"]; ?></div>
                        <div class="col-lg-3 attrib"><b>Enrollment Date:<b> <?php echo $row["Enrollment_date"]; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 attrib"><b>Type:<b> <?php echo $row["Type"]; ?></div>
                        <div class="col-lg-3 attrib"><a href="admin_edit_student.php?stid=<?php echo $row["Student_ID"]; ?>">Edit >></a></div>
                    </div>
                </div>
            <?php endwhile ?>
        <?php endif ?>


    </body>

    </html>