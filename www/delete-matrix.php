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

$userid = $_SESSION['id'];



$matrixid = $_POST['id'];

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM matrices WHERE id='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM cells WHERE matrixid='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM mapping WHERE matrixid='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$sql4 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM nodesfreeze WHERE matrixid='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$sql5 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM edgesfreeze WHERE matrixid='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

echo "ok";


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

?>