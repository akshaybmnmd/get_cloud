<?php
include_once("../common/api_include.php");

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];
$action = $_POST['action'];

switch ($action) {
    case 'change_image':
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "UPDATE `images` SET `privacy` = CASE WHEN `privacy` = 'public' THEN 'private' ELSE 'public' END WHERE `images`.`id` = '$id'";
        $result = $conn->query($sql);
        $response['query_1'] = $result;
        break;
    case 'change_video':
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "UPDATE `videos` SET `privacy` = CASE WHEN `privacy` = 'public' THEN 'private' ELSE 'public' END WHERE `videos`.`id` = '$id'";
        $result = $conn->query($sql);
        $response['query_1'] = $result;
        break;
    default:
        $response['error'] = "invalid action.";
        break;
}

$conn->close();
exit(json_encode($response));
