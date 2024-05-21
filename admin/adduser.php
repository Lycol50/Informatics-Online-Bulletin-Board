<?php
$home_dir = '..';
include $home_dir . '/config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// if user is not admin redirect to home page
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != 'admin') {
    header("location: ../home.php");
    exit;
}

$error = '';
$success = '';
$student_id = $first_name = $last_name = $grade_level = $section = $interest = $password = $confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["student_id"]))) {
        $error = "Please enter Student ID.";
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    if (empty(trim($_POST["first_name"]))) {
        $error = "Please enter First Name.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }

    if (empty(trim($_POST["last_name"]))) {
        $error = "Please enter Last Name.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }

    if (empty(trim($_POST["grade_level"]))) {
        $error = "Please select Grade Level.";
    } else {
        $grade_level = trim($_POST["grade_level"]);
    }

    if (empty(trim($_POST["section"]))) {
        $error = "Please enter Section.";
    } else {
        $section = trim($_POST["section"]);
    }

    $interest = trim($_POST["interest"]);

    if (empty(trim($_POST["password"]))) {
        $error = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $error = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
    }

    if ($password != $confirm_password) {
        $error = "Password did not match.";
    }

    $role = trim($_POST["role"]);

    if (empty($error)) {
        $sql = "INSERT INTO users (student_id, first_name, last_name, grade_level, section, interest, password, role) VALUES ('$student_id', '$first_name', '$last_name', '$grade_level', '$section', '$interest', '" . password_hash($password, PASSWORD_DEFAULT) . "', '$role')";
        if (mysqli_query($conn, $sql)) {
            $success = "User added successfully";
        } else {
            $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Add Event - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmAction() {
            return confirm("Are you sure to add this user?");
        }
    </script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php } ?>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2 class="fw-bold">Add User</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID (without dash)*</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name*</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name*</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="grade_level" class="form-label">Grade Level*</label>
                                <select class="form-select" id="grade_level" name="grade_level" required>
                                    <option value="11">Grade 11</option>
                                    <option value="12">Grade 12</option>
                                    <option value="HE">Higher Education</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="section" class="form-label">Section*</label>
                                <input type="text" class="form-control" id="section" name="section" required>
                            </div>
                            <div class="mb-3">
                                <label for="interest" class="form-label">Interests</label>
                                <input type="text" class="form-control" id="interest" name="interest">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password*</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password*</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role*</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <p class="text-muted">* Required</p>
                            <button onclick='return confirmAction();' type="submit" class="btn btn-primary">Add Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>