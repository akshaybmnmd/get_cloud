<?php
include_once("../common/api_include.php");

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];
$action = $_POST['action'];

switch ($action) {
    case 'like_image':
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "SELECT * FROM `likes` WHERE `user_id` = '$user_id' AND `image_id` = '$id'";
        $result = $conn->query($sql);
        $response['query_1'] = $result;

        if ($result->num_rows > 0) {
            $response['error'] = "already liked";
        } else {
            $sql = "INSERT INTO `likes` (`user_id`, `image_id`, `ip`) VALUES ('$user_id','$id', '$ip')";
            $result = $conn->query($sql);
            $response['query_2'] = $result;
            $sql = "UPDATE `images` SET `likes`=`likes` + 1 WHERE `id` = '$id'";
            $result = $conn->query($sql);
            $response['query_3'] = $result;
        }
        break;
    case 'unlike_image':
        $sql = "DELETE FROM `likes` WHERE `user_id` = '$user_id' AND `image_id` = '$id'";
        $result = $conn->query($sql);
        $response['query_1'] = $result;
        $sql = "UPDATE `images` SET `likes`=`likes` - 1 WHERE `id` = '$id'";
        $result = $conn->query($sql);
        $response['query_2'] = $result;
        break;
    default:
        $response['error'] = "invalid action.";
        break;
}

$conn->close();
exit(json_encode($response));
