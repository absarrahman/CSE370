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

$table_name = "student";

$id_asc = "SELECT * FROM `student` ORDER BY `Student_ID` ASC";
$id_desc = "SELECT * FROM `student` ORDER BY `Student_ID` DESC";
$name_asc = "SELECT * FROM `student` ORDER BY `Fname` ASC";
$name_desc = "SELECT * FROM `student` ORDER BY `Fname` DESC";
$default_value = "SELECT * FROM `student`";

$action = "SELECT * FROM $table_name";

function getSelected($query)
{
    if (isset($_GET["query"])) {
        if ($_GET["query"] === $query) {
            return "selected";
        } else {
            return "";
        }
    } else {
        return "";
    }
}

if (isset($_GET["query"])) {
    switch ($_GET["query"]) {
        case "id_asc":
            $action = $id_asc;
            break;
        case "id_desc":
            $action = $id_desc;
            break;
        case "name_asc":
            $action = $name_asc;
            break;
        case "name_desc":
            $action = $name_desc;
            break;
        default:
            $action = $action;
            break;
    }
} else {
    echo "Neeh";
}




$result_response = mysqli_query($conn, $action);

if (!$result_response) {
    die("Result response not found" . mysqli_error($conn));
}

$conn->close();

?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Student List</title>
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
            <form action="admin_student_list.php" method="get">
                <div class="form-group">
                    <label for="options">Sort By</label>
                    <select name="query" class="form-control" id="options" onchange="this.form.submit()">
                        <option value="id_asc" <?php echo getSelected("id_asc"); ?>>ID (ASC)</option>
                        <option value="id_desc" <?php echo getSelected("id_desc"); ?>>ID (DESC)</option>
                        <option value="name_asc" <?php echo getSelected("name_asc"); ?>>Name (ASC)</option>
                        <option value="name_desc" <?php echo getSelected("name_desc"); ?>>Name (DESC)</option>
                        <option value="default_value" <?php echo getSelected("default_value"); ?>>None</option>
                    </select>
                </div>
            </form>
        </div>
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
    </body>

    </html>
