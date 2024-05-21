<?php
include 'config.php';

header('Content-Type: application/json');

$events = array();
$today = date('Y-m-d');
$sql = "SELECT * FROM events ORDER BY start_date ASC;";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
  $events[] = array(
    'title' => htmlspecialchars($row['event_title']),
    'start' => $row['start_date'].'T'. $row['start_time'],
    'end' => $row['end_date'].'T'. $row['end_time'],
    'url' => 'event.php?id=' . $row['event_id']
  );
}

echo json_encode($events);
?>
