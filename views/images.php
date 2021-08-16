<?php
session_start();
require "../common/db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' AND `location` = 'local_hdd_U_i' ORDER BY `time` DESC LIMIT 500";
// $sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' ORDER BY `time` DESC LIMIT 500";
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
    $output .= "<div class=\"photo\" id=\"code$key\">";
    $output .= "<img class=\"lazy\" id=\"$key\" src=\"{$img['path']}\" data-original=\"{$img['path']}\" alt=\"{$img['name']}\"><br>\n";
    $output .= "({$img['mem']} KB) <button id='{$img['id']}' onclick='change_privacy({$img['id']})'>Make " . ($img['privacy'] == 'public' ? "private" : "public") . "</button><br>";
    $output .= "{$img['like']} likes<br>\n";
    $output .= "</div>\n";
    $ratio = $key;
    if ($key == 69)
      break;
  }

  if (!empty($output)) {
    print $output;
  }
  ?>

</div>

<script type="text/javascript" charset="utf-8">
  var images = <?php echo json_encode($images); ?>;
  page = -1;
  page_flag = -1;
  request = 1;
  pages = [];
  id = <?php echo $ratio; ?>;

  var i, j, temporary, chunk = 20;
  for (i = 0, j = images.length; i < j; i += chunk) {
    temporary = images.slice(i, i + chunk);
    if (i >= 70) {
      pages.push(temporary);
    }
  }

  var modal = document.getElementById("myModal");

  // Get the image and insert it inside the modal - use its "alt" text as a caption
  var modalImg = document.getElementById("img01");
  var captionText = document.getElementById("caption");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";

    $(function() {
      $("img.lazy").lazyload();
    });
  }

  add_img_click();

  function change_privacy(id) {
    $.post("actions/edit_privacy.php", {
      id: id,
      user_id: user_id,
      action: 'change_image'
    }, function(result) {
      if (result.query_1 === true) {
        var innerText = $("#" + id)[0].innerText;
        if (innerText == 'Make public') $("#" + id)[0].innerText = "Make private";
        else $("#" + id)[0].innerText = "Make public";
      }
    });
  }

  function loadrest() {
    if (page == page_flag) {
      page++;

      if (pages[page]) {
        pages[page].forEach((val) => {
          id++;
          $('.container').append('<div class="photo" id="code' + id + '"><img class="lazy" id="' + id + '" src="' + val.path + '" data-original="' + val.path + '" alt="' + val.name + '"><br>(' + val.mem + ' KB) <button id="' + val.id + '" onclick="change_privacy(' + val.id + ')">Make ' + val.privacy + '</button><br>' + val.like + ' likes<br></div>');
        });

        add_img_click();

        page_flag++;
      } else {
        if (request != 0) {
          loadnextpage();
        }
      }

    }
    $('.container').append("");
  }

  function loadnextpage() {
    $.ajax({
        method: "POST",
        url: "actions/get_images.php",
        data: {
          page: request
        }
      })
      .done((data) => {
        console.log("Data Saved", data);
        img = data.data;

        if (img.length) {
          img.forEach((val) => {
            images.push(val);
          });

          var i, j, temporary, chunk = 20;
          for (i = 0, j = img.length; i < j; i += chunk) {
            temporary = img.slice(i, i + chunk);
            pages.push(temporary);
          }

          request++;
          page_flag++;
        } else {
          request = 0;
        }
      });
  }

  function add_img_click() {
    $('img').click((t) => {
      id = t.target.id;
      size = images[id].mem + " KB ";
      status = images[id].privacy;
      modal.style.display = "block";
      modalImg.src = t.target.src;
      img_id = images[id].id;
      likes = status == "public" ? "<br>" + images[id].like + " Likes" : "";
      captionText.innerHTML = t.target.alt + '<br>size  ' + size + status + likes + '<!--<br>Download &nbsp; <i class="fas fa-download" style="cursor: pointer;" onclick="download(' + img_id + ',' + id + ')"></i>--><br>Move to bin &nbsp; <i class="fas fa-trash" style="cursor: pointer;" onclick="trash(' + img_id + ',' + id + ')"></i>';
    });
  }

  function trash(img_id, id) {
    console.log("trashed..", id, img_id);
    $.ajax({
        method: "POST",
        url: "actions/remove_image.php",
        data: {
          id: img_id
        }
      })
      .done((data) => {
        console.log(data);
        // location.reload();
        $('#code' + id).remove();
      });
  }

  function download(img_id, id) {
    console.log("download..", id, img_id);
  }

  $(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
      loadrest();
    }
  });

  $(document).ready(function() {
    window.scroll({
      top: 0,
      left: 0,
      behavior: 'smooth'
    });
  });
</script>