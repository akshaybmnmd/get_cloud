<?php
session_start();
require "../common/db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' AND `location` = 'local_hdd_U_i' ORDER BY `time` DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $images[] = array(
      'id' => $id,
      'path' => $row["path"],
      'mem' => round($row["size"] / 1024),
      'like' => $row["likes"],
      'privacy' => $row['privacy']
    );
  }
} else {
  $images = [];
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

  foreach ($images as $img) {
    $output .= "<div class=\"photo\">";
    $output .= "<a target=\"_blank\" href=\"{$img['path']}\">";
    $output .= "<img class=\"lazy\" data-original=\"{$img['path']}\" alt=\"\"></a><br>\n";
    $output .= "({$img['mem']} KB) <button id='{$img['id']}' onclick='change_privacy({$img['id']})'>Make " . ($img['privacy'] == 'public' ? "private" : "public") . "</button><br>";
    $output .= "{$img['like']} likes<br>\n";
    $output .= "</div>\n";
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

  function change_privacy(id) {
    $.post("actions/edit_privacy.php", {
      id: id,
      user_id: user_id,
      action: 'change_image'
    }, function(result) {
      console.log(result);
      if (result.query_1 === true) {
        var innerText = $("#" + id)[0].innerText;
        if (innerText == 'Make public') $("#" + id)[0].innerText = "Make private";
        else $("#" + id)[0].innerText = "Make public";
      }
    });
  }
</script>