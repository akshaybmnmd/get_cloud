<?php
session_start();
require "../common/db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `videos` WHERE `user_id` = '$user_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while ($row = $result->fetch_assoc()) {
    $videos[] = array(
      'path' => $row["path"],
      'size' => getimagesize("../" . $row["path"]),
      'mem' => round($row["size"] / 1024),
      'like' => $row["likes"],
      'privacy' => $row['privacy']
    );
  }
} else {
  $videos = [];
}
$conn->close();
?>

<script src="jquery-1.11.3.min.js"></script>
<script src="jquery.lazyload.js"></script>
<script>
  if (isMobile) {
    var head = document.getElementsByTagName('HEAD')[0];
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = './css/mobile/images.css';
    head.appendChild(link);
  } else {
    var head = document.getElementsByTagName('HEAD')[0];
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = './css/computer/images.css';
    head.appendChild(link);
  }
</script>

<div class="container">

  <?php
  $ratio = 0.3;
  $output = "";

  foreach ($videos as $vid) {
    $output .= "<div class=\"photo\">";
    $output .= '<video id="my-video" class="" controls preload="auto" width="372px" data-setup="{}">';
    $output .= '<source src="' . $vid["path"] . '" type="video/webm">';
    $output .= '<p class="vjs-no-js">';
    $output .= "To view this video please enable JavaScript, and consider upgrading to a web browser that";
    $output .= '<a href="https://videojs.com/html5-video-support/" target="_blank"> supports HTML5 video</a>';
    $output .= "</p>";
    $output .= "</video><br>";
    $output .= "({$vid['mem']} KB)<br>\n";
    $output .= "like {$vid['like']}<br>\n";
    $output .= "</div>";
  }

  if (!empty($output)) {
    print $output;
  }
  ?>

</div>

<script type="text/javascript" charset="utf-8">
  $(function() {
    $("img.lazy").lazyload();
  });
</script>