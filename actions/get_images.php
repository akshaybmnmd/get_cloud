<?php
include_once("../common/api_include.php");

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
