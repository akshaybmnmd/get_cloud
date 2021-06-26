<?php
session_start();

require "../common/db.php";

$_SESSION['server'] = "ok";
$_SESSION['status'] = false;
$user_id = $_SESSION['user_id'];
$ip = $_SERVER['REMOTE_ADDR'];
$time = time();

if (isset($_POST['submitted'])) {
    if (isset($_FILES['upload'])) {
        $allowed = array('image/pjpeg', 'image/jpeg', 'image/JPG', 'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png');

        if (in_array($_FILES['upload']['type'], $allowed)) {
            $filename = preg_replace('/[^a-zA-Z0-9_. ]/', '', $_FILES['upload']['name']);

            if (strlen($filename) > 45) {
                $x = strlen($filename) - 45;
                $filename = substr($filename, $x);
            }

            if (move_uploaded_file($_FILES['upload']['tmp_name'], "../uploades/images/{$filename}")) {
                $_SESSION['status'] = true;
                $_SESSION['message'] = "The files has been uploaded!";
                $size = filesize("../uploades/images/{$filename}");
                $sql = "INSERT INTO `scheduled` (`user_id`, `path`, `name`, `size`, `dimension`, `time`, `ip`, `privacy`, `type`) VALUES ('$user_id', 'uploades/images/$filename', '$filename', '$size', 'x', '$time', '$ip', 'private', 'image')";
                $result = $conn->query($sql);
                $_SESSION['sql'] = $sql;
                $_SESSION['result'] = $result;
                $conn->close();
            }
        } else {
            $_SESSION['message'] = "Please upload a JPEG or PNG image.";
        }
    }

    if ($_FILES['upload']['error'] > 0) {
        switch ($_FILES['upload']['error']) {
            case 1:
                $_SESSION['message'] = "The file exceeds server upload_max_filesize.";
                break;
            case 2:
                $_SESSION['message'] = "The file exceeds the MAX_FILE_SIZE allowed.";
                break;
            case 3:
                $_SESSION['message'] = "The file was only partially uploaded.";
                break;
            case 4:
                $_SESSION['message'] = "No file was uploaded.";
                break;
            case 6:
                $_SESSION['message'] = "No temporary folder was available.";
                break;
            case 7:
                $_SESSION['message'] = "Unable to write to the disk.";
                break;
            case 8:
                $_SESSION['message'] = "File upload stopped.";
                break;
            default:
                $_SESSION['message'] = "A system error occurred.";
                break;
        }
    }

    if (file_exists($_FILES['upload']['tmp_name']) && is_file($_FILES['upload']['tmp_name'])) {
        unlink($_FILES['upload']['tmp_name']);
    }
}

header("Location: ../");
