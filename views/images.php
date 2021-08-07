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
      'name' => $row['Name'],
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

  <!-- The Modal -->
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>

  <?php
  $ratio = 0.3;
  $output = "";
  foreach ($images as $key => $img) {
    $output .= "<div class=\"photo\">";
    $output .= "<img class=\"lazy\" id=\"$key\" src=\"{$img['path']}\" data-original=\"{$img['path']}\" alt=\"{$img['name']}\"><br>\n";
    $output .= "({$img['mem']} KB) <button id='{$img['id']}' onclick='change_privacy({$img['id']})'>Make " . ($img['privacy'] == 'public' ? "private" : "public") . "</button><br>";
    $output .= "{$img['like']} likes<br>\n";
    $output .= "</div>\n";
    if ($key == 49)
      break;
  }

  if (!empty($output)) {
    print $output;
  }
  ?>

</div>

<script type="text/javascript" charset="utf-8">
  var images = <?php echo json_encode($images); ?>;

  var modal = document.getElementById("myModal");

  // Get the image and insert it inside the modal - use its "alt" text as a caption
  var modalImg = document.getElementById("img01");
  var captionText = document.getElementById("caption");

  $('img').click((t) => {
    id = t.target.id;
    size = images[id].mem + " KB ";
    status = images[id].privacy;
    modal.style.display = "block";
    modalImg.src = t.target.src;
    likes = status == "public" ? "<br>" + images[id].like + " Likes" : "";
    captionText.innerHTML = t.target.alt + '<br>size  ' + size + status + likes + '<br>Download &nbsp; <i class="fas fa-download"></i>';
  });

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";

    $(function() {
      $("img.lazy").lazyload();
    });
  }

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

  function loadrest() {
    console.log("oops");
  }

  $(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
      loadrest();
    }
  });
</script>