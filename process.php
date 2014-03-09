<?php 
include 'config.php';

$conn = new mysqli($server, $user, $pass, $database);
if ($conn->connect_error) {
  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}
$conn->set_charset("utf8");

if (isset($_GET['add'])) {
	$r = '';
	foreach ($_POST as $row) {
		$r .= $row.',';
	}
	$r = rtrim($r, ",");
	$sql = "update tunnid set processed=1 where id in (".$r.");";
	$conn->query($sql);
}
if (isset($_GET['remove'])) {
	$r = '';
	foreach ($_POST as $row) {
		$r .= $row.',';
	}
	$r = rtrim($r, ",");
	$sql = "update tunnid set processed=0 where id in (".$r.");";
	$conn->query($sql);
}


?>