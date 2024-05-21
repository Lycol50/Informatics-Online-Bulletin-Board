<?php
include 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// get user information from database, get it from get url otherwise the session
$student_id = $_GET['id'] ?? $_SESSION['student_id'];
$sql = "SELECT * FROM users WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Profile - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <!-- 2 column, 1st column is information about the user, 2nd column is profile picture -->
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="fw-bold">Profile</h2>
                    </div>
                    <div class="card-body">
                        <p><strong>Student ID:</strong> <?php echo $user['student_id']; ?></p>
                        <p><strong>Name:</strong> <?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></p>
                        <p><strong>Grade Level:</strong> <?php echo $user['grade_level']; ?></p>
                        <p><strong>Section:</strong> <?php echo $user['section']; ?></p>
                        <p><strong>Interest:</strong> <?php echo $user['interest']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="cat.png" class="img-fluid" alt="Profile Picture" width="300px">
            </div>
        </div>
</body>

</html>