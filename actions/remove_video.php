<?php
include_once("../common/api_include.php");

$user_id = $_SESSION['user_id'];

$id = $_POST['id'];

$sql = "SELECT * FROM `scheduled` WHERE `id` = '$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['row'] = $row;
$path = $row['path'];
$filename = $row['name'];

$response[1] = file_exists("../$path");

if (copy("../$path", "../removed/videos/$filename")) {
    $sql = "UPDATE `scheduled` SET `path` = 'removed/videos/$filename', `action` = 'removed', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
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
