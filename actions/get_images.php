<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = "only accept post requests.";
    exit(json_encode($response));
}

$response['server'] = "ok";

if (!isset($_POST['action'])) {
    $response['error'] = "no action specified!";
    exit(json_encode($response));
}

session_start();

require "../common/db.php";
$response['DB'] = "ok";

$user_id = $_SESSION['user_id'];
$response['user'] = $user_id;

if (@$_POST['token'] == "sdlhg3567$#^fhgE%yuY?34") {
    $user_id = $_POST['user_id'];
}

$page = $_POST['page'];
$limit = ($page * 500) + 1;
$response['user_id'] = $user_id;
$response['limit'] = $page * 500;

switch ($_POST['action']) {
    case 'images':
        $sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' AND `location` = 'local_hdd_U_i' ORDER BY `time` DESC LIMIT $limit,500";
        // $sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' ORDER BY `time` DESC LIMIT $limit,500";
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
        break;
    case 'images_bin':
        $sql = "SELECT * FROM `images` WHERE `user_id` = '$user_id' AND `location` = 'local_hdd_U_i_r' ORDER BY `time` DESC LIMIT $limit,500";
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
        break;
    default:
        $response['error'] = "undefined action!";
        exit(json_encode($response));
}
$conn->close();
$response['data'] = $images;
echo json_encode($response);
