<?php

include 'dbUtilities.php';

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
	echo "Your session expired...";
	die();
}

$userid = $_SESSION['id'];
$matrixid = $_POST['mid'];



$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM cells WHERE matrixid='$matrixid' ORDER BY 'row','column' ASC") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
  $rows[] = $r;
}

echo json_encode($rows);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);