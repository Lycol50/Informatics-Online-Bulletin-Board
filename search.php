<?php
include 'config.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Search - Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1 class="fw-bold">Search</h1>
                <form action="<?php echo htmlentities(htmlspecialchars($_SERVER["PHP_SELF"]), ENT_QUOTES); ?>" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search"><br>
                    <button class="btn btn-outline-dark" type="submit">Search</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2 class="mt-3">Search Results</h2>
                <div class="list-group">
                    <?php
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_query = "SELECT * FROM events WHERE event_title LIKE '%$search%' OR event_description LIKE '%$search%'";
                        $result = mysqli_query($conn, $search_query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                                <a href="event.php?id=<?php echo $row['event_id']; ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo $row['event_title']; ?></h5>
                                        <small><?php echo date('F j, Y', strtotime($row['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo $row['event_description']; ?></p>
                                </a>
                            <?php
                            }
                        } else {
                            ?>
                            <div class="alert alert-warning" role="alert">
                                No results found.
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>