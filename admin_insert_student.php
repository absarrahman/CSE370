<?php

// Author: Abrar Mahmud

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

$inserted = false;

function do_insertion()
{
    global $inserted;
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $inserted = true;
        $street = empty(trim($_POST["street"])) ? NULL : trim($_POST["street"]);
        $house  = empty(trim($_POST["house"]))  ? NULL : trim($_POST["house"]);
        $city   = empty(trim($_POST["city"]))   ? NULL : trim($_POST["city"]);
        $ssn    = empty(trim($_POST["ssn"]))    ? NULL : trim($_POST["ssn"]);
        $stid   = empty(trim($_POST["stid"]))   ? NULL : trim($_POST["stid"]);
        $email  = empty(trim($_POST["email"]))  ? NULL : trim($_POST["email"]);
        $fname  = empty(trim($_POST["fname"]))  ? NULL : trim($_POST["fname"]);
        $lname  = empty(trim($_POST["lname"]))  ? NULL : trim($_POST["lname"]);
        $type   = empty(trim($_POST["type"]))   ? NULL : trim($_POST["type"]);
        $sems   = empty(trim($_POST["sems"]))   ? NULL : trim($_POST["sems"]);
        $cgpa   = empty(trim($_POST["cgpa"]))   ? NULL : trim($_POST["cgpa"]);
        $edate  = empty(trim($_POST["edate"]))  ? NULL : trim($_POST["edate"]);
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
            || is_null($stid)
            || is_null($cgpa)
            || is_null($email)
            || is_null($pass)
            || is_null($type)
            || is_null($sems)
        ) {
            return "One or more required fields are empty";
        }

        $test_sql = sprintf("SELECT * FROM student WHERE student.Student_ID = %s OR student.Email = \"%s\";", $stid, $email);
        if (mysqli_num_rows($conn->query($test_sql)) > 0) {
            return "Student with same Email or Student ID already exists";
        }

        $sql_main = sprintf(
            "INSERT INTO student VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);",
            is_null($street) ? 'NULL' : '"' . $street . '"',
            is_null($house)  ? 'NULL' : '"' . $house . '"',
            is_null($city)   ? 'NULL' : '"' . $city . '"',
            is_null($ssn)    ? 'NULL' : '"' . $ssn . '"',
            $stid,
            '"' . $email . '"',
            '"' . $fname . '"',
            '"' . $lname . '"',
            '"' . $type . '"',
            $sems,
            $cgpa,
            is_null($edate) ? 'NULL' : '"' . $edate . '"',
            '"' . $pass . '"'
        );


        if (!$conn->query($sql_main)) {
            return "Insertion failed";
        }

        if ($type === "ungrad") {
            if (!$conn->query(sprintf(
                "INSERT INTO undergrad_student VALUES (%s, %s, %s);",
                is_null($ungrad_high) ? 'NULL' : $ungrad_high,
                is_null($ungrad_adms) ? 'NULL' : $ungrad_adms,
                $stid
            ))) {
                return "Insertion failed : Undergrad";
            }
        } else {
            if (!$conn->query(sprintf(
                "INSERT INTO grad_student VALUES (%s, %s, %s, %s);",
                is_null($grad_bach_cgpa) ? 'NULL' : $grad_bach_cgpa,
                is_null($grad_gre) ?       'NULL' : $grad_gre,
                is_null($grad_ielts) ?     'NULL' : $grad_ielts,
                $stid
            ))) {
                return "Insertion failed : Grad";
            }
        }

        foreach ($majors as $m) {
            if (empty($m)) {
                continue;
            }
            if (!$conn->query(sprintf("INSERT INTO `student_majors`(`Majors`, `Student_ID`) VALUES (\"%s\", %s);", $m, $stid))) {
                return "Insertion failed : Majors";
            }
        }

        foreach ($phones as $p) {
            if (empty($p)) {
                continue;
            }
            if (!$conn->query(sprintf("INSERT INTO `phone_no`(`PhoneNumber`, `Student_ID`) VALUES (\"%s\", %s);", $p, $stid))) {
                return "Insertion failed : Phones";
            }
        }

        return "OK";
    }
}

$msg = do_insertion();

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Insert Student Info</title>
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
        <h2>Insert Student Info</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php if ($inserted) : ?>
                <?php if ($msg === "OK") : ?>
                    <div class="alert alert-success" role="alert">Insertion Successful (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php else : ?>
                    <div class="alert alert-danger" role="alert"><?php echo $msg; ?> (<a href="admin_panel.php">Go to Dashboard</a>)</div>
                <?php endif ?>
            <?php endif ?>

            <div class="form-group">
                <label>Street</label>
                <input type="text" name="street" class="form-control">
            </div>
            <div class="form-group">
                <label>House</label>
                <input type="text" name="house" class="form-control">
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control">
            </div>
            <div class="form-group">
                <label>SSN</label>
                <input type="text" name="ssn" class="form-control">
            </div>
            <div class="form-group">
                <label>Student ID*</label>
                <input type="text" name="stid" class="form-control">
            </div>
            <div class="form-group">
                <label>Email*</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>First Name*</label>
                <input type="text" name="fname" class="form-control">
            </div>
            <div class="form-group">
                <label>Last Name*</label>
                <input type="text" name="lname" class="form-control">
            </div>
            <div class="form-group">
                <label for="sel01">Student Type*</label>
                <select name="type" class="form-control" id="sel01" onchange="handleTypeChange()">
                    <option value="ungrad" selected>Undergraduate</option>
                    <option value="grad">Graduate</option>
                </select>
            </div>


            <div class="form-group">
                <label>Highschool Result (Only for undergraduate students)</label>
                <input type="number" name="ungrad_high" class="form-control ungrad" step="any">
            </div>
            <div class="form-group">
                <label>Admission Result (Only for undergraduate students)</label>
                <input type="number" name="ungrad_adms" class="form-control ungrad" step="any">
            </div>



            <div class="form-group">
                <label>Bachelors CGPA (Only for graduate students)</label>
                <input type="number" name="grad_bach_cgpa" class="form-control grad" disabled step="any">
            </div>
            <div class="form-group">
                <label>IELTS Score (Only for graduate students)</label>
                <input type="number" name="grad_ielts" class="form-control grad" disabled step="any">
            </div>
            <div class="form-group">
                <label>GRE Score (Only for graduate students)</label>
                <input type="number" name="grad_gre" class="form-control grad" disabled step="any">
            </div>



            <div class="form-group">
                <label>Semester*</label>
                <input type="number" name="sems" value="1" class="form-control">
            </div>
            <div class="form-group">
                <label>CGPA*</label>
                <input type="number" name="cgpa" value="0.00" class="form-control" step="any">
            </div>
            <div class="form-group">
                <label>Enrollment Date</label>
                <input type="date" name="edate" value="2020-01-01" class="form-control">
            </div>
            <div class="form-group">
                <label>Password*</label>
                <input type="password" name="pass" class="form-control">
            </div>


            <div id="phone_group" class="form-group">
                <label>Phone Number(s)</label>
                <a href="javascript:void(0);" id="add_phone" class="stretched-link" title="Add field"> (Add phone no. field)</a><br>
            </div>

            <div id="major_group" class="form-group">
                <label>Student Major(s)</label>
                <a href="javascript:void(0);" id="add_major" class="stretched-link" title="Add field"> (Add Major field)</a><br>
            </div>



            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Insert">
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