<?php
if (!isset($_REQUEST['action'])) {
    die();
}
$where = '';

@$id = $_REQUEST['id'];

switch ($_REQUEST['action']) {
    case 'scheduled':
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'user_id',  'dt' => 1),
            array('db' => 'path', 'dt' => 2),
            array('db' => 'name', 'dt' => 3),
            array('db' => 'size', 'dt' => 4),
            array('db' => 'dimension', 'dt' => 5),
            array('db' => 'time',  'dt' => 6),
            array('db' => 'ip', 'dt' => 7),
            array('db' => 'privacy', 'dt' => 8),
            array('db' => 'type', 'dt' => 9),
        );
        $where = "action ='pending'";
        $table = "scheduled";
        break;
    default:
        die();
        break;
}

// Table's primary key
$primaryKey = 'id';

// SQL server connection information
$sql_details = array(
    'user' => 'php_user',
    'pass' => 'Z<8$c.hNgR(]<xnn',
    'db'   => 'images',
    'host' => "localhost"
);

require('ssp.class.php');

echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, $where)
);
