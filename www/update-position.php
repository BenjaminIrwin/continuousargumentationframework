<?php

include 'dbUtilities.php';
require('Pusher.php');

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
	echo "Your session expired...";
	die();
}



$userid = $_SESSION['id'];
$username = $_SESSION['username'];
$debateid = $_SESSION['debate'];

$nodeid = $_POST['id'];
$x = $_POST['x'];
$y = $_POST['y'];

$sqldebateid=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debateid FROM nodes WHERE id=$nodeid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

while($r=mysqli_fetch_array($sqldebateid)){
	$debateid=$r['debateid'];
}

$sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE nodes SET x='$x', y='$y' WHERE id='$nodeid' ") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$debateid'");


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);