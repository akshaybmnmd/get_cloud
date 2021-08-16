<?php
include_once("../common/api_include.php");

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];
$sql = "SELECT * FROM `images` WHERE `id` = '$id' AND `user_id` = '$user_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$response['image_data'] = $row;
$path = "../" . $row['path'];
$filename = $row['Name'];
$r = false;

switch ($_POST['action']) {
    case 'from_bin':
        if (file_exists($path)) {
            $r = '1';
            if (copy("$path", "/var/www/html/mydisk/$user_id/images/$filename")) {
                $r = '2';
                if (file_exists("/var/www/html/mydisk/$user_id/images/$filename")) {
                    $r = '3';
                    if (unlink($path)) {
                        $r = '4';
                        $sql = "UPDATE `images` SET `path` = '../mydisk/$user_id/images/$filename', `location` = 'local_hdd_U_i' WHERE `images`.`id` = $id";

                        if ($conn->query($sql) === TRUE) {
                            $r = false;
                            $response['restored'] = true;
                            $response['status'] = "moved to images";
                        } else $response['error'] = "Error updating record: " . $conn->error . " $id\n";
                    } else $response['error'] = "can't unlink file!! $id\n";
                } else $response['error'] = "can't get to copied file!! $id\n";
            } else $response['error'] = "can't copy file!! $id\n";
        } else $response['error'] = "file doesn't exist!! $id\n";
        break;
    case 'from_archive':
        $response['restored'] = true;
        $response['status'] = "moved to images";
        break;
    default:
        $response['error'] = "undefined action!";
        exit(json_encode($response));
}

$response['error_code'] = $r;
$conn->close();
exit(json_encode($response));
