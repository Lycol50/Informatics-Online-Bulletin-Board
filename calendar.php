<?php
include 'config.php';
session_start();

$today = date('Y-m-d');
$upcoming_events = "SELECT * FROM events WHERE start_date >= '$today' ORDER BY start_date ASC";
$result = mysqli_query($conn, $upcoming_events);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="info-logo.png">
  <title>Calendar - Informatics Online Bulletin Board</title>

  <link rel="stylesheet" href="style.css?v=1.1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <!-- calendar library -->
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'fetch-events.php' // Load events via separate PHP script
      });
      calendar.render();
    });
  </script>
</head>

<body>
  <?php include 'nav.php'; ?>
  <div class="container">
    <div class="row">
      <h1>Calendar</h1>
      <!-- insert calendar using fullcalendar js -->
      <div id='calendar'></div>
    </div>
    <hr>
    <h2>Upcoming Events</h2><br>
    <div class="row">
      <?php
      if (mysqli_num_rows($result) > 0) {
        // makae this show as cards
        while ($row = mysqli_fetch_assoc($result)) {

          // convert sql time to h:i a format
          $row['start_time'] = date('h:i a', strtotime($row['start_time']));
          $row['end_time'] = date('h:i a', strtotime($row['end_time']));
          // make card clickable
          echo '<div class="col-md-4">';
          echo '<div class="card">';
          echo '<div class="card-body">';
          echo '<h5 class="card-title">' . $row['event_title'] . '</h5>';
          echo '<h6 class="card-subtitle mb-2 text-muted">' . $row['start_date'] . ' - ' . $row['end_date'] . '</h6>';
          echo '<h6 class="card-subtitle mb-2 text-muted">' . $row['start_time'] . ' - ' . $row['end_time'] . '</h6>';
          echo '<a href="event.php?id=' . $row['event_id'] . '" class="btn btn-primary stretched-link">View Event</a>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }
      } else {
        echo '<p>No upcoming events.</p>';
      }
      ?>
    </div>
  </div>
</body>

</html>