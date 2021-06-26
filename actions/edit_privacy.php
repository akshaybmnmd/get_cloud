<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = "only accept post requests.";
    exit(json_encode($response));
}

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $response['error'] = "We don't have your data.";
    exit(json_encode($response));
}

if (isset($_POST['action']))
    $action = $_POST['action'];
else {
    $response['error'] = "action not specified.";
    exit(json_encode($response));
}

require "../common/db.php";

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];

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
