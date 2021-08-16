<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = "only accept post requests.";
    exit(json_encode($response));
}

if (!isset($_POST['action'])) {
    $response['error'] = "no action specified!";
    exit(json_encode($response));
}

session_start();


require "../common/db.php";

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
    case 'to_bin':
        if (file_exists($path)) {
            $r = '1';
            if (copy("$path", "/var/www/html/mydisk/$user_id/bin/image/$filename")) {
                $r = '2';
                if (file_exists("/var/www/html/mydisk/$user_id/bin/image/$filename")) {
                    $r = '3';
                    if (unlink($path)) {
                        $r = '4';
                        $sql = "UPDATE `images` SET `path` = '../mydisk/$user_id/bin/image/$filename', `location` = 'local_hdd_U_i_r' WHERE `images`.`id` = $id";

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
    case 'from_bin':
        $response['removed'] = true;
        $response['status'] = "removed from bin";
        break;
    default:
        $response['error'] = "undefined action!";
        exit(json_encode($response));
}

$response['error_code'] = $r;
$conn->close();
exit(json_encode($response));
