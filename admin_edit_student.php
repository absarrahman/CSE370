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

$edited = false;

function do_edit()
{
    global $edited;
    global $conn;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $edited = true;
        $street = empty(trim($_POST["street"])) ? NULL : trim($_POST["street"]);
        $house  = empty(trim($_POST["house"]))  ? NULL : trim($_POST["house"]);
        $city   = empty(trim($_POST["city"]))   ? NULL : trim($_POST["city"]);
        $ssn    = empty(trim($_POST["ssn"]))    ? NULL : trim($_POST["ssn"]);
        $stid   = empty(trim($_POST["stid"]))   ? NULL : trim($_POST["stid"]);
        $email  = empty(trim($_POST["email"]))  ? NULL : trim($_POST["email"]);
        $fname  = empty(trim($_POST["fname"]))  ? NULL : trim($_POST["fname"]);
        $lname  = empty(trim($_POST["lname"]))  ? NULL : trim($_POST["lname"]);
        $sems   = empty(trim($_POST["sems"]))   ? NULL : trim($_POST["sems"]);
        $cgpa   = empty(trim($_POST["cgpa"]))   ? NULL : trim($_POST["cgpa"]);
        $pass   = empty(trim($_POST["pass"]))   ? NULL : trim($_POST["pass"]);

        $majors = empty($_POST["majors"]) ? array() : $_POST["majors"];
        $phones = empty($_POST["phones"]) ? array() : $_POST["phones"];

        $grad_bach_cgpa = array_key_exists("grad_bach_cgpa", $_POST)
            ? (empty(trim($_POST["grad_bach_cgpa"])) ? NULL : trim($_POST["grad_bach_cgpa"]))
            : NULL;
        $grad_ielts     = array_key_exists("grad_ielts", $_POST)
            ? (empty(trim($_POST["grad_ielts"])) ? NULL : trim($_POST["grad_ielts"]))
            : NULL;
        $grad_gre       = array_key_exists("grad_gre", $_POST)
            ? (empty(trim($_POST["grad_gre"])) ? NULL : trim($_POST["grad_gre"]))
            : NULL;

        $ungrad_high = array_key_exists("ungrad_high", $_POST)
            ? (empty(trim($_POST["ungrad_high"])) ? NULL : trim($_POST["ungrad_high"]))
            : NULL;
        $ungrad_adms = array_key_exists("ungrad_adms", $_POST)
            ? (empty(trim($_POST["ungrad_adms"])) ? NULL : trim($_POST["ungrad_adms"]))
            : NULL;

        if (
            is_null($fname)
            || is_null($lname)
            || is_null($cgpa)
            || is_null($email)
            || is_null($stid)
            || is_null($pass)
            || is_null($sems)
        ) {
            return "One or more required fields are empty";
        }

        $test_sql = sprintf("SELECT * FROM student WHERE student.Student_ID = %s;", $stid);
        $entry = $conn->query($test_sql)->fetch_assoc();
        $type = $entry["Type"];

        $sql_main = sprintf(
            "UPDATE `student` SET `Street`=%s,`House`=%s,`City`=%s,`SSN`=%s,`Email`=%s,`Fname`=%s,`Lname`=%s,`Semester`=%s,`CGPA`=%s,`Password`=%s WHERE `Student_ID`=%s;",
            is_null($street) ? 'NULL' : '"' . $street . '"',
            is_null($house)  ? 'NULL' : '"' . $house . '"',
            is_null($city)   ? 'NULL' : '"' . $city . '"',
            is_null($ssn)    ? 'NULL' : '"' . $ssn . '"',
            '"' . $email . '"',
            '"' . $fname . '"',
            '"' . $lname . '"',
            $sems,
            $cgpa,
            '"' . $pass . '"',
            $stid
        );


        if (!$conn->query($sql_main)) {
            return "Update failed";
        }

        if ($type === "ungrad") {
            if (!$conn->query(sprintf(
                "UPDATE `undergrad_student` SET `Highschool_Result`=%s,`Admission_Result`=%s WHERE `Student_ID`=%s;",
                is_null($ungrad_high) ? 'NULL' : $ungrad_high,
                is_null($ungrad_adms) ? 'NULL' : $ungrad_adms,
                $stid
            ))) {
                return "Update failed : Undergrad";
            }
        } else {
            if (!$conn->query(sprintf(
                "UPDATE `grad_student` SET `Bachelor_CGPA`=%s,`GRE_Score`=%s,`IELTS_Score`=%s WHERE `Student_ID`=%s;",
                is_null($grad_bach_cgpa) ? 'NULL' : $grad_bach_cgpa,
                is_null($grad_gre) ?       'NULL' : $grad_gre,
                is_null($grad_ielts) ?     'NULL' : $grad_ielts,
                $stid
            ))) {
                return "Update failed : Grad";
            }
        }

        $conn->query(sprintf("DELETE FROM student_majors WHERE student_majors.Student_ID = %s;", $stid));
        $conn->query(sprintf("DELETE FROM phone_no WHERE phone_no.Student_ID = %s;", $stid));

        foreach ($majors as $m) {
            if (empty($m)) {
                continue;
            }
            if (!$conn->query(sprintf("INSERT INTO `student_majors`(`Majors`, `Student_ID`) VALUES (\"%s\", %s);", $m, $stid))) {
                return "Update failed : Majors";
            }
        }

        foreach ($phones as $p) {
            if (empty($p)) {
                continue;
            }
            if (!$conn->query(sprintf("INSERT INTO `phone_no`(`PhoneNumber`, `Student_ID`) VALUES (\"%s\", %s);", $p, $stid))) {
                return "Update failed : Phones";
            }
        }

        $edited = true;
        return "OK";
    }
}

$msg = "";
$stid_e = "";
$prevstate_student = array();
$prevstate_ungrad_student = array();
$prevstate_grad_student = array();
$prevstate_majors = array();
$prevstate_phones = array();

function null_or_val($map, $key)
{
    return is_null($map[$key]) ? "" : $map[$key];
}

$msg = do_edit();
$stid_e = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stid_e = $_POST["stid"];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    if(!array_key_exists("stid", $_GET)) {
        $conn->close();
        header("location: admin_panel.php");
        exit;
    } else {
        $stid_e = $_GET["stid"];
    }
}



$prevstate_student =        $conn->query(sprintf("SELECT * FROM student WHERE student.Student_ID = %s;", $stid_e))->fetch_assoc();
$prevstate_ungrad_student = $conn->query(sprintf("SELECT * FROM undergrad_student WHERE undergrad_student.Student_ID = %s;", $stid_e))->fetch_assoc();
$prevstate_grad_student =   $conn->query(sprintf("SELECT * FROM grad_student WHERE grad_student.Student_ID = %s;", $stid_e))->fetch_assoc();

$prevstate_majors = $conn->query(sprintf("SELECT * FROM student_majors WHERE student_majors.Student_ID = %s;", $stid_e));
$prevstate_phones = $conn->query(sprintf("SELECT * FROM phone_no WHERE phone_no.Student_ID = %s;", $stid_e));


$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Student Info</title>
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

        .removal {
            position: absolute;
            right: 5px;
            top: 0;
            bottom: 0;
            height: 14px;
            margin: auto;
            font-size: 14px;
            cursor: pointer;
            color: #aa0000;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Update Student Info</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if ($edited) : ?>
                <?php if ($msg === "OK") : ?>
                    <div class="alert alert-success" role="alert">Update Succesful (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php else : ?>
                    <div class="alert alert-danger" role="alert"><?php echo $msg; ?> (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php endif ?>
            <?php endif ?>

            <div class="form-group">
                <label>Street</label>
                <input type="text" name="street" class="form-control" value="<?php echo null_or_val($prevstate_student, "Street"); ?>">
            </div>
            <div class="form-group">
                <label>House</label>
                <input type="text" name="house" class="form-control" value="<?php echo null_or_val($prevstate_student, "House"); ?>">
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo null_or_val($prevstate_student, "City"); ?>">
            </div>
            <div class="form-group">
                <label>SSN</label>
                <input type="text" name="ssn" class="form-control" value="<?php echo null_or_val($prevstate_student, "SSN"); ?>">
            </div>
            <div class="form-group">
                <label>Student ID*</label>
                <input type="text" name="stid" class="form-control" readonly="readonly" value="<?php echo $stid_e; ?>">
            </div>
            <div class="form-group">
                <label>Email*</label>
                <input type="email" name="email" class="form-control" value="<?php echo null_or_val($prevstate_student, "Email"); ?>">
            </div>
            <div class="form-group">
                <label>First Name*</label>
                <input type="text" name="fname" class="form-control" value="<?php echo null_or_val($prevstate_student, "Fname"); ?>">
            </div>
            <div class="form-group">
                <label>Last Name*</label>
                <input type="text" name="lname" class="form-control" value="<?php echo null_or_val($prevstate_student, "Lname"); ?>">
            </div>


            <?php if (null_or_val($prevstate_student, "Type") === "ungrad") : ?>
                <div class="form-group">
                    <label>Highschool Result (Only for undergraduate students)</label>
                    <input type="number" name="ungrad_high" class="form-control ungrad" step="any" value="<?php echo null_or_val($prevstate_ungrad_student, "Highschool_Result"); ?>">
                </div>
                <div class="form-group">
                    <label>Admission Result (Only for undergraduate students)</label>
                    <input type="number" name="ungrad_adms" class="form-control ungrad" step="any" value="<?php echo null_or_val($prevstate_ungrad_student, "Admission_Result"); ?>">
                </div>
            <?php endif ?>


            <?php if (null_or_val($prevstate_student, "Type") === "grad") : ?>
                <div class="form-group">
                    <label>Bachelors CGPA (Only for graduate students)</label>
                    <input type="number" name="grad_bach_cgpa" class="form-control grad" step="any" value="<?php echo null_or_val($prevstate_grad_student, "Bachelor_CGPA"); ?>">
                </div>
                <div class="form-group">
                    <label>IELTS Score (Only for graduate students)</label>
                    <input type="number" name="grad_ielts" class="form-control grad" step="any" value="<?php echo null_or_val($prevstate_grad_student, "IELTS_Score"); ?>">
                </div>
                <div class="form-group">
                    <label>GRE Score (Only for graduate students)</label>
                    <input type="number" name="grad_gre" class="form-control grad" step="any" value="<?php echo null_or_val($prevstate_grad_student, "GRE_Score"); ?>">
                </div>
            <?php endif ?>



            <div class="form-group">
                <label>Semester*</label>
                <input type="number" name="sems" class="form-control" value="<?php echo null_or_val($prevstate_student, "Semester"); ?>">
            </div>
            <div class="form-group">
                <label>CGPA*</label>
                <input type="number" name="cgpa" class="form-control" step="any" value="<?php echo null_or_val($prevstate_student, "CGPA"); ?>">
            </div>
            <div class="form-group">
                <label>Enrollment Date</label>
                <input type="date" name="edate" class="form-control" value="<?php echo null_or_val($prevstate_student, "Enrollment_date"); ?>">
            </div>
            <div class="form-group">
                <label>Password*</label>
                <input type="password" name="pass" class="form-control" value="<?php echo null_or_val($prevstate_student, "Password"); ?>">
            </div>


            <div id="phone_group" class="form-group">
                <label>Phone Number(s)</label>
                <a href="javascript:void(0);" id="add_phone" class="stretched-link" title="Add field"> (Add phone no. field)</a><br>
                <?php while ($ph = $prevstate_phones->fetch_assoc()) : ?>
                    <div class="btn-group lbc"><input type="tel" name="phones[]" class="form-control" value="<?php echo $ph["PhoneNumber"]; ?>"><span onclick="removeSelf(this)" class="glyphicon glyphicon-remove-circle removal"></span></div>
                <?php endwhile ?>
            </div>

            <div id="major_group" class="form-group">
                <label>Student Major(s)</label>
                <a href="javascript:void(0);" id="add_major" class="stretched-link" title="Add field"> (Add Major field)</a><br>
                <?php while ($mj = $prevstate_majors->fetch_assoc()) : ?>
                    <div class="btn-group lbc"><input type="tel" name="majors[]" class="form-control" value="<?php echo $mj["Majors"]; ?>"><span onclick="removeSelf(this)" class="glyphicon glyphicon-remove-circle removal"></span></div>
                <?php endwhile ?>
            </div>



            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update">
            </div>
        </form>
    </div>
    <script src="./static/js/jquery-3.5.1.min.js"></script>
    <script>
        function handleTypeChange() {
            const val = $("#sel01").children("option:selected").val();
            if (val === "grad") {
                $("input.ungrad").attr("disabled", true);
                $("input.grad").attr("disabled", false);
            } else if (val === "ungrad") {
                $("input.grad").attr("disabled", true);
                $("input.ungrad").attr("disabled", false);
            }
        }

        function removeSelf(e) {
            $(e).closest('div').remove();
        }
        $(document).ready(() => {
            $("#add_phone").click(() => {
                $("#phone_group").append('<div class="btn-group lbc"><input type="tel" name="phones[]" class="form-control"><span onclick="removeSelf(this)" class="glyphicon glyphicon-remove-circle removal"></span></div>');
            });
            $("#add_major").click(() => {
                $("#major_group").append('<div class="btn-group lbc"><input type="tel" name="majors[]" class="form-control"><span onclick="removeSelf(this)" class="glyphicon glyphicon-remove-circle removal"></span></div>');
            });
        });
    </script>
</body>

</html>