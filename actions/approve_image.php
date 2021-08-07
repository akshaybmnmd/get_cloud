<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = "only accept post requests.";
    exit(json_encode($response));
}

session_start();

require "../common/db.php";

$user_id = $_SESSION['user_id'];

$id = $_POST['id'];

$sql = "SELECT * FROM `scheduled` WHERE `id` = '$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['row'] = $row;
$u_id = $row['user_id'];
$path = $row['path'];
$filename = $row['name'];
$size = $row['size'];
$dimension = $row['dimension'];
$ip = $row['ip'];
$category = 'image';
$location = "local";
$priority = 1;
$privacy = $row['privacy'];
$time = $row['time'];

if (copy("../$path", "../images/$filename")) {
    $sql = "UPDATE `scheduled` SET `path` = 'images/$filename', `action` = 'approved', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
    $result = $conn->query($sql);
    $response['update'] = $result;
    if (unlink("../$path")) {
        $timenow = time();
        $sql = "INSERT INTO `images` (`user_id`, `approver_id`, `approver_on`, `time`, `size`, `Name`, `path`, `dimension`, `ip`, `category`, `location`, `priority`, `privacy`, `likes`) VALUES ('$u_id', '$user_id', '$timenow', '$time', '$size', '$filename', 'images/$filename', '$dimension', '$ip', '$category', '$location', '$priority', '$privacy', '0')";
        $result = $conn->query($sql);
        $response['insert'] = $result;
    } else {
        $response['error'] = "file unlink failed";
    }
} else {
    $response['error'] = "file copy failed";
    $sql = "UPDATE `scheduled` SET `path` = '$path', `action` = 'failed', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
    $result = $conn->query($sql);
    $response['update'] = $result;
}

$conn->close();
exit(json_encode($response));
