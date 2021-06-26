<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../");
    exit;
}

require_once "../common/db.php";

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username =  mysqli_real_escape_string($conn, $_POST['username']);
    $password =  sha1($_POST['password']);

    $sql = "SELECT * FROM `users` WHERE `name` = '$username' AND `sha` = '$password' ";
    echo $sql;
    $result = $conn->query($sql);
    $conn->close();

    if (!$row = $result->fetch_assoc()) {
        $_SESSION['error'] = 'incorrect username or password';
        header("Location: ./");
    } else {
        $_SESSION["username"] = $row['name'];
        $_SESSION["role"] = $row['role'];
        $_SESSION["user_id"] = $row['id'];
        $_SESSION["loggedin"] = true;
        $_SESSION['error'] = "Server is facing some issue while logging you in !";
        header("Location: ./");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 25px sans-serif;
        }

        .wrapper {
            width: 100%;
            padding: 20%;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>

</html>