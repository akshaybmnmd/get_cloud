<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['error'] = "only accept post requests.";
    exit(json_encode($response));
}

$response['server'] = "ok";

if (!isset($_POST['action'])) {
    $response['error'] = "no action specified!";
    exit(json_encode($response));
}

session_start();

require "../common/db.php";
$response['DB'] = "ok";
