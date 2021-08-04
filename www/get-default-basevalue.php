#!/usr/bin/php<?php

include 'dbUtilities.php';

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
	echo "Your session expired...";
	die();
}


$debateid = $_POST['did'];

$sql = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT defaultbasevalue FROM debates WHERE id='$debateid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$row = mysqli_fetch_array($sql);
$result['basevalue']=$row['defaultbasevalue'];

echo json_encode($result);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);