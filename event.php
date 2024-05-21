<?php
include 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$event_id = $_GET['id'];
$sql = "SELECT * FROM events WHERE event_id = $event_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $event = mysqli_fetch_assoc($result);
    $announcement_id = $event['announcement_id'];
    $sql_announcement = "SELECT * FROM announcements WHERE announcement_id = $announcement_id";
    $result_announcement = mysqli_query($conn, $sql_announcement);
    $announcement = mysqli_fetch_assoc($result_announcement);
} else {
    header("location: 404.php");
    exit;
}

// convert sql new line to html new line
$event['event_description'] = nl2br($event['event_description']);

// convert sql date to M d, Y h:i a format
$event['created_at'] = date('M d, Y h:i a', strtotime($event['created_at']));

// convert sql time to h:i a format
$event['start_time'] = date('h:i a', strtotime($event['start_time']));
$event['end_time'] = date('h:i a', strtotime($event['end_time']));

// get announcement title
$announcement_id = $event['announcement_id'];
$sql = "SELECT title FROM announcements WHERE announcement_id = $announcement_id";
$result = mysqli_query($conn, $sql);
$announcement = mysqli_fetch_assoc($result);

// get name from user_id
$user_id = $event['user_id'];
$sql = "SELECT first_name, last_name FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$event['user_id'] = $user['first_name'] . ' ' . $user['last_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Events - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2 class="fw-bold"><?php echo $event['event_title']; ?></h2>
                    </div>
                    <div class="card-body">
                        <p><?php echo $event['event_description']; ?></p>
                        <p><strong>Location:</strong> <?php echo $event['location']; ?></p>
                        <p><strong>Date:</strong> <?php echo $event['start_date']; ?> - <?php echo $event['end_date']; ?></p>
                        <p><strong>Time:</strong> <?php echo $event['start_time']; ?> - <?php echo $event['end_time']; ?></p>
                        <p><strong>Linked Announcement:</strong><br><a href="announcement.php?id=<?php echo $event['announcement_id']; ?>" class="btn btn-primary"><?php echo $announcement['title']; ?></a></p>
                        <p><strong>Created by:</strong> <?php echo $event['user_id']; ?></p>
                        <p><strong>Created at:</strong> <?php echo $event['created_at']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>