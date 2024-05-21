<?php
include 'config.php';
session_start();

$today = date('Y-m-d');

// Use prepared statements to secure SQL queries
$get_events_stmt = $conn->prepare("SELECT * FROM events WHERE start_date >= ? ORDER BY start_date ASC");
$get_events_stmt->bind_param("s", $today);
$get_events_stmt->execute();
$events_result = $get_events_stmt->get_result();

$get_announcements_stmt = $conn->prepare("SELECT * FROM announcements");
$get_announcements_stmt->execute();
$announcements_result = $get_announcements_stmt->get_result();

$active = true;


    $user_id = $_SESSION['user_id'];
    $get_user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $get_user_stmt->bind_param("i", $user_id);
    $get_user_stmt->execute();
    $user_result = $get_user_stmt->get_result();
    $user = $user_result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="info-logo.png">
    <title>Informatics Online Bulletin Board</title>

    <link rel="stylesheet" href="style.css?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container-fluid" style="background: rgb(2,0,36); background: linear-gradient(135deg, rgba(2,0,36,1) 0%, rgba(93,224,230,1) 0%, rgba(0,74,173,1) 100%);">
        <div class="container">
            <h1 class="text-white text-center py-3 fw-bold">Welcome to Informatics Online Bulletin Board</h1>
            <p class="text-white text-center">Hello, <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>!
            <br>Here are the latest announcements and upcoming events.</p>
        </div>
    </div>
    <div class="container">
        <!-- Carousel for Announcements -->
        <h1 class="text-center fw-bold py-2">Announcements</h1>
        <div id="announcementsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                if ($announcements_result->num_rows > 0) {
                    while ($announcement = $announcements_result->fetch_assoc()) {
                ?>
                        <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                            <div class="card">
                                <div class="card-body">
                                    <img src="admin/uploads/<?php echo htmlspecialchars($announcement['image']); ?>" class="d-block mx-auto" width="40%" alt="Logo"><br>
                                    <h3 class="fw-bold"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                    <p class="text-truncate" style="display: inline-block; max-width:1000px;"><?php echo htmlspecialchars($announcement['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php
                        $active = false;
                    }
                } else {
                    ?>
                    <div class="carousel-item active">
                        <div class="card alert alert-warning">
                            <div class="card-body">
                                <h3 class="card-title">No Announcements Yet</h3>
                                <p class="card-text">There are no announcements yet. Please check back later.</p>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#announcementsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#announcementsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            <script>
                // automatic scrolling of carousel
                document.addEventListener('DOMContentLoaded', function() {
                    var carousel = new bootstrap.Carousel('#announcementsCarousel', {
                        interval: 3000
                    });
                });
            </script>
        </div>

        <!-- Announcement Summary Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4 py-3">
            <?php
            // Reset announcement result set for the summary cards
            $announcements_result->data_seek(0);

            if ($announcements_result->num_rows > 0) {
                while ($announcement = $announcements_result->fetch_assoc()) {
            ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="fw-bold"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                            </div>
                            <a href="announcement.php?id=<?php echo htmlspecialchars($announcement['announcement_id']); ?>" class="btn btn-primary w-100">Learn More</a>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>

        <hr>
        <h1 class="text-center fw-bold py-2">Upcoming Events</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4 py-3">
            <?php
            if ($events_result->num_rows > 0) {
                while ($event = $events_result->fetch_assoc()) {
                    // convert sql time to h:i a format
                    $event['start_time'] = date("h:i a", strtotime($event['start_time']));
                    $event['end_time'] = date("h:i a", strtotime($event['end_time']));
            ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="fw-bold"><?php echo htmlspecialchars($event['event_title']); ?></h3>
                                <p class="text-muted"><?php echo htmlspecialchars($event['start_date']); ?> - <?php echo htmlspecialchars($event['end_date']); ?></p>
                                <p class="text-muted"><?php echo htmlspecialchars($event['start_time']); ?> - <?php echo htmlspecialchars($event['end_time']); ?></p>

                            </div>
                            <a href="event.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn btn-primary w-100">Learn More</a>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="card alert alert-warning">
                    <div class="card-body">
                        <h3 class="card-title">No Upcoming Events</h3>
                        <p class="card-text">There are no upcoming events yet. Please check back later.</p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
</body>

</html>