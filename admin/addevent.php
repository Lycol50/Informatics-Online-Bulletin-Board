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

$title = $description = $start_date = $end_date = $announcement_id = $location = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    // convert start_time and end_time to 24 hrs
    $start_time = date('H:i:s', strtotime($_POST['start_time']));
    $end_time = date('H:i:s', strtotime($_POST['end_time']));
    $announcement_id = $_POST['announcement_id'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    // Ensure announcement_id exists in the announcements table
    $check_announcement = "SELECT announcement_id FROM announcements WHERE announcement_id = '$announcement_id'";
    $result = mysqli_query($conn, $check_announcement);
    if (mysqli_num_rows($result) == 0) {
        $error = "Invalid announcement ID.";
    } else {
        $sql = "INSERT INTO events (event_title, event_description, start_date, end_date, announcement_id, user_id, location, start_time, end_time) VALUES ('$title', '$description', '$start_date', '$end_date', '$announcement_id', '$user_id', '$location', '$start_time', '$end_time')";
        if (mysqli_query($conn, $sql)) {
            $success = "Event added successfully";
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
            return confirm("Are you sure to add this event?");
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
                        <h2 class="fw-bold">Add Event</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Event Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="mb-3">
                                <label for="announcement_id" class="form-label">Announcement</label>
                                <select class="form-select" id="announcement_id" name="announcement_id" required>
                                    <option value="">Select Announcement</option>
                                    <?php
                                    $sql = "SELECT announcement_id, title FROM announcements";
                                    $result = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['announcement_id'] . '">' . $row['title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button onclick='return confirmAction();' type="submit" class="btn btn-primary">Add Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
