<?php
session_start();
require "../common/db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' AND `location` = 'local_hdd_U_i_r' ORDER BY `time` DESC LIMIT 500";
// $sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' ORDER BY `time` DESC LIMIT 500";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $images["k" . $id] = array(
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

    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    var bin_images = <?php echo json_encode($images); ?>;
    page = -1;
    page_flag = -1;
    request = 1;
    pages = [];
    initial = true;
    values = Object.values(bin_images);
    final = [];
    counter = 0;
    portion = [];

    for (var key in bin_images) {
        if (counter !== 0 && counter % 20 === 0) {
            pages.push(portion);
            portion = [];
        }
        portion.push(values[counter]);
        counter++
    }
    pages.push(portion);

    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    var span = document.getElementsByClassName("close")[0];

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
                    id = val.id;
                    $('.container').append('<div class="photo" id="code' + id + '"><img class="lazy" id="' + id + '" src="' + val.path + '" data-original="' + val.path + '" alt="' + val.name + '"><br>(' + val.mem + ' KB) </div>');
                });

                add_img_click();

                page_flag++;

                if (initial) {
                    initial = false;
                    loadrest();
                }
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
                    page: request,
                    action: "images_bin"
                }
            })
            .done((data) => {
                console.log("response", data);
                img = data.data;
                k = data.limit;
                if (img.length) {
                    img.forEach((val) => {
                        console.log(val);
                        bin_images["k" + k] = val;
                        k++;
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
            size = bin_images["k" + id].mem + " KB ";
            modal.style.display = "block";
            modalImg.src = t.target.src;
            img_id = bin_images["k" + id].id;
            captionText.innerHTML = t.target.alt + '<br>size  ' + size + '<br>Restore &nbsp; <i class="fas fa-circle" style="cursor: pointer;" onclick="restore(' + img_id + ',\'' + id + '\')"></i><br>Remove from bin &nbsp; <i class="fas fa-trash" style="cursor: pointer;" onclick="trash(' + img_id + ',\'' + id + '\')"></i>';
        });
    }

    function trash(img_id, id) {
        console.log("trashed..", id, img_id);
        $.ajax({
                method: "POST",
                url: "actions/remove_image.php",
                data: {
                    id: img_id,
                    action: "from_bin"
                }
            })
            .done((data) => {
                console.log(data);
                $('#code' + id).remove();
            });
    }

    function restore(img_id, id) {
        console.log("trashed..", id, img_id);
        $.ajax({
                method: "POST",
                url: "actions/restore_image.php",
                data: {
                    id: img_id,
                    action: "from_bin"
                }
            })
            .done((data) => {
                console.log(data);
                $('#code' + id).remove();
            });
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
        loadrest();
    });
</script>