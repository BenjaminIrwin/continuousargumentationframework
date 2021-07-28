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


$srcdebate = $_POST['did'];


$srcnode = $_POST['sn'];

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM `wormholes` WHERE userid='$userid' AND dstdebate IS NULL AND dstnode IS NULL") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO `wormholes` (`userid`, `srcdebate`, `dstdebate`, `srcnode`, `dstnode`) VALUES ('$userid', '$srcdebate', NULL, '$srcnode', NULL);") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

if ($sql2){
	echo "Wormhole node copied.";
}
else {
	echo "Something went wrong.";
}

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);
