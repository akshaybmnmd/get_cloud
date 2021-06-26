<?php
session_start();
require "../common/db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT `name`,`id` FROM `users`";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $users[$row["id"]] = $row;
    }
} else {
    $users = [];
}

$sql = "SELECT * FROM `images` WHERE `privacy` = 'public' ORDER BY `time` DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $sql = "SELECT * FROM `likes` WHERE `user_id` = '$user_id' AND `image_id` = '$id'";
        $res = $conn->query($sql);
        $images[] = array(
            'id' => $id,
            'path' => $row["path"],
            'by' => isset($users[$row["user_id"]]) ? $users[$row["user_id"]]['name'] : $row["user_id"],
            'like' => $row["likes"],
            'liked' => $res->num_rows > 0 ? false : true
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
<!-- <button onclick="get_html('global_videos.php')">Videos</button><br> -->
<div class="container">

    <?php
    $ratio = 0.3;
    $output = "";

    foreach ($images as $img) {
        $output .= "<div id='div_{$img['id']}' class=\"photo\">";
        $output .= "<a target=\"_blank\" href=\"{$img['path']}\">";
        $output .= "<img class=\"lazy\" data-original=\"{$img['path']}\" alt=\"\"></a><br>\n";
        $output .= "by {$img['by']}<br><div id='{$img['id']}'>";
        $output .= $img['liked'] ? "<button onclick='like({$img['id']})'><i class='fas fa-thumbs-up'>like</i> {$img['like']}</button>" : "<button onclick='unlike({$img['id']})'><i class='fas fa-thumbs-down'>unlike</i> {$img['like']}</button>";
        $output .= "</div></div>\n";
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

    function like(id) {
        $.post("actions/edit_like.php", {
            id: id,
            user_id: user_id,
            action: 'like_image'
        }, function(result) {
            if (result.query_2 === true && result.query_3 === true) {
                var innerText = $("#" + id)[0].innerText;
                var likes = parseInt(innerText.split(" ")[1]) + 1;
                $("#" + id)[0].innerHTML = `<button onclick="unlike(${id})"><i class="fas fa-thumbs-down">unlike</i> ${likes}</button>`;
            }
        });
    }

    function unlike(id) {
        $.post("actions/edit_like.php", {
            id: id,
            user_id: user_id,
            action: 'unlike_image'
        }, function(result) {
            if (result.query_1 === true && result.query_2 === true) {
                var innerText = $("#" + id)[0].innerText;
                var likes = parseInt(innerText.split(" ")[1]) - 1;
                $("#" + id)[0].innerHTML = `<button onclick="like(${id})"><i class="fas fa-thumbs-up">like</i> ${likes}</button>`;
            }
        });
    }
</script>