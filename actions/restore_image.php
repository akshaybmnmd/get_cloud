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
                            $response['removed'] = true;
                            $response['status'] = "moved to bin";
                        } else $response['error'] = "Error updating record: " . $conn->error . " $id\n";
                    } else $response['error'] = "can't unlink file!! $id\n";
                } else $response['error'] = "can't get to copied file!! $id\n";
            } else $response['error'] = "can't copy file!! $id\n";
        } else $response['error'] = "file doesn't exist!! $id\n";
        break;
    default:
        $response['error'] = "undefined action!";
        exit(json_encode($response));
}












// $user_id = $_SESSION['user_id'];

// $id = $_POST['id'];

// $sql = "SELECT * FROM `scheduled` WHERE `id` = '$id'";
// $result = $conn->query($sql);
// $row = $result->fetch_assoc();
// $response['row'] = $row;
// $u_id = $row['user_id'];
// $path = $row['path'];
// $filename = $row['name'];
// $size = $row['size'];
// $dimension = $row['dimension'];
// $ip = $row['ip'];
// $category = 'image';
// $location = "local";
// $priority = 1;
// $privacy = $row['privacy'];
// $time = $row['time'];

// if (copy("../$path", "../images/$filename")) {
//     $sql = "UPDATE `scheduled` SET `path` = 'images/$filename', `action` = 'approved', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
//     $result = $conn->query($sql);
//     $response['update'] = $result;
//     if (unlink("../$path")) {
//         $timenow = time();
//         $sql = "INSERT INTO `images` (`user_id`, `approver_id`, `approver_on`, `time`, `size`, `Name`, `path`, `dimension`, `ip`, `category`, `location`, `priority`, `privacy`, `likes`) VALUES ('$u_id', '$user_id', '$timenow', '$time', '$size', '$filename', 'images/$filename', '$dimension', '$ip', '$category', '$location', '$priority', '$privacy', '0')";
//         $result = $conn->query($sql);
//         $response['insert'] = $result;
//     } else {
//         $response['error'] = "file unlink failed";
//     }
// } else {
//     $response['error'] = "file copy failed";
//     $sql = "UPDATE `scheduled` SET `path` = '$path', `action` = 'failed', `action_by` = '$user_id' WHERE `scheduled`.`id` = '$id'";
//     $result = $conn->query($sql);
//     $response['update'] = $result;
// }

$response['error_code'] = $r;
$conn->close();
exit(json_encode($response));
