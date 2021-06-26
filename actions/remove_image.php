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
$path = $row['path'];
$filename = $row['name'];

if (copy("../$path", "../removed/images/$filename")) {
    $sql = "UPDATE `scheduled` SET `path` = 'removed/images/$filename', `action` = 'removed', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
    $result = $conn->query($sql);
    $response['update'] = $result;
    if (!unlink("../$path")) {
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
