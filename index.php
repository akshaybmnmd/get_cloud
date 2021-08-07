<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ./login");
    exit;
}

require "common/db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT `name`,`id` FROM `users`";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[$row["id"]] = $row;
    }
} else {
    $users = [];
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>!!loota!!</title>
    <script>
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        if (isMobile) {
            var head = document.getElementsByTagName('HEAD')[0];
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = './css/mobile/Main.css';
            head.appendChild(link);
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = './css/mobile/notyf.min.css';
            head.appendChild(link);
        } else {
            var head = document.getElementsByTagName('HEAD')[0];
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = './css/computer/Main.css';
            head.appendChild(link);
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = './css/computer/notyf.min.css';
            head.appendChild(link);
        }
        var user_id = <?php echo $user_id; ?>;
        var users = <?php echo json_encode($users); ?>;

        function get_html(view_link, id) {
            localStorage.setItem("location", view_link);

            $.ajax({
                cache: false,
                url: "views/" + view_link,
                data: {
                    source: "admin",
                },
                method: "POST",
            }).done(function(result) {
                $(".main_container").empty();
                $(".main_container").html(result);
            });

            // $.ajax({
            //     cache: false,
            //     url: "../../common/websitepoll.php",
            //     data: {
            //         key: sessionStorage.id ? sessionStorage.id : "",
            //         site: "camrent",
            //         page: view_link,
            //     },
            //     method: "POST"
            // }).done((result) => {
            //     sessionStorage.id = result;
            // });
        }
    </script>
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src="js/main.js"></script>

</head>

<body>

    <div class="wrapper collapse">
        <div class="top_navbar">
            <div class="hamburger">
                <div class="one"></div>
                <div class="two"></div>
                <div class="three"></div>
            </div>

            <div class="top_menu">
                <div>
                    <i class="fas fa-photo-video logo"></i>
                </div>

                <ul>
                    <li class="search">
                        <a href="#">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>

                    <li class="bell">
                        <a href="#">
                            <i class="fas fa-bell"></i>
                        </a>
                    </li>

                    <li class="user">
                        <a href="#">
                            <i class="fas fa-user"></i>
                        </a>
                    </li>

                    <li class="images">
                        <a href="./logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </li>

                    <?php if (isset($_SESSION['role'])) if ($_SESSION['role'] == 'admin') { ?>
                        <!-- <li class="file-approve">
                            <a href="#">
                                <i class="fas fa-edit"></i>
                            </a>
                        </li> -->
                    <?php } ?>

                </ul>
            </div>
        </div>

        <div class="sidebar">
            <ul>
                <li class="images">
                    <a href="#" class="active">
                        <span class="icon">
                            <i class="fas fa-images"></i>
                        </span>
                        <span class="title">Images</span>
                    </a>
                </li>

                <li class="videos">
                    <a href="#">
                        <span class="icon">
                            <i class="fas fa-video"></i>
                        </span>
                        <span class="title">Videos</span>
                    </a>
                </li>

                <li class="file-upload">
                    <a href="#">
                        <span class="icon">
                            <i class="fas fa-file-upload"></i>
                        </span>
                        <span class="title">Upload</span>
                    </a>
                </li>

                <li class="global_images">
                    <a href="#">
                        <span class="icon">
                            <i class="fas fa-globe-asia"></i>
                        </span>
                        <span class="title">Global</span>
                    </a>
                </li>

                <?php if (isset($_SESSION['role'])) if ($_SESSION['role'] == 'admin') { ?>
                <?php } ?>

            </ul>
        </div>

        <div class="main_container">
        </div>

    </div>

</body>

</html>