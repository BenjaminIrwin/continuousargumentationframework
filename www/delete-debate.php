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




$id = $_POST['id'];


$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM debates WHERE id=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM nodes WHERE debateid=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM edges WHERE debateid=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sql5 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM mapping WHERE debateid='$id'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
