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

$title = $description = $image = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // title and description must be safely passed inot database, include new lines
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Check if file already exists
    if (file_exists($target_file)) {
        //rename to add unix time after name
        $image = pathinfo($image, PATHINFO_FILENAME) . time() . '.' . pathinfo($image, PATHINFO_EXTENSION);
    }
    
    $newtarget_file = $target_dir . $image;

    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = "Sorry, only JPG, JPEG, PNG files are allowed.";
    }

    if (empty($error)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $newtarget_file)) {
            $sql = "INSERT INTO announcements (title, description, image, user_id) VALUES ('$title', '$description', '$image', '" . $_SESSION['user_id'] . "')";
            if (mysqli_query($conn, $sql)) {
                $success = "Announcement added successfully";
            } else {
                $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
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
    <title>Add Announcement - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="<?php echo $home_dir ?>/style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmAction() {
            return confirm("Are you sure to add this announcement?");
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
                        <h2 class="fw-bold">Add Announcement</h2>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image" name="image" required>
                            </div>
                            <button onclick='return confirmAction();' type="submit" class="btn btn-primary">Add Announcement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
