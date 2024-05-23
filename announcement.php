<?php
include 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$announcement_id = $_GET['id'];
$sql = "SELECT * FROM announcements WHERE announcement_id = '$announcement_id'";
$result = mysqli_query($conn, $sql);
$announcement = mysqli_fetch_assoc($result);

/* if (mysqli_num_rows($result) == 0) {
    header("location: 404.php");
    exit;
} */

// convert sql new lines to html new lines
$announcement['description'] = nl2br($announcement['description']);

// convert sql date to M d, Y h:i a format
$announcement['created_at'] = date('M d, Y h:i a', strtotime($announcement['created_at']));

// get name from user_id
$user_id = $announcement['user_id'];
$sql = "SELECT first_name, last_name FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$announcement['user_id'] = $user['first_name'] . ' ' . $user['last_name'];

// comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $announcement_id = $_POST['announcement_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO comments (comment, announcement_id, user_id) VALUES ('$comment', '$announcement_id', '$user_id')";
    if (mysqli_query($conn, $sql)) {
        header("Location: announcement.php?id=$announcement_id");
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Announcement - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col">
                <img src="admin/uploads/<?php echo $announcement['image']; ?>" class="img-fluid" alt="Announcement Image" width="450px">
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2 class="fw-bold"><?php echo $announcement['title']; ?></h2>
                    </div>
                    <div class="card-body">
                        <p><?php echo $announcement['description']; ?></p>
                        <p><strong>Created at:</strong> <?php echo $announcement['created_at']; ?></p>
                        <p><strong>Created by:</strong> <?php echo $announcement['user_id']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <h3>Comments</h3>
                <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="post">
                    <input type="hidden" name="announcement_id" value="<?php echo $announcement_id; ?>">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Insert Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <?php
                $sql = "SELECT * FROM comments WHERE announcement_id = '$announcement_id' ORDER BY created_at DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($comment = mysqli_fetch_assoc($result)) {
                        // get name from user_id
                        $user_id = $comment['user_id'];
                        $sql = "SELECT first_name, last_name FROM users WHERE user_id = '$user_id'";
                        $result2 = mysqli_query($conn, $sql);
                        $user = mysqli_fetch_assoc($result2);
                        $comment['user_id'] = $user['first_name'] . ' ' . $user['last_name'];
                        $comment['comment'] = nl2br($comment['comment']);
                ?>
                        <div class="card mt-4">
                            <div class="card-header">
                                <strong><?php echo $comment['user_id']; ?></strong>
                            </div>
                            <div class="card-body">
                                <p><?php echo $comment['comment']; ?></p>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="card mt-4">
                        <div class="card-body">
                            <p>No comments yet.</p>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
