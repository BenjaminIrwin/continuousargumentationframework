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




$debateid = $_POST['did'];
$matrixid = $_POST['mid'];
$returntype = $_POST['rt'];

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO mapping (userid,debateid,matrixid) VALUES ('$userid','$debateid','$matrixid')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

if($returntype=="matrix"){
	$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM debates WHERE id='$debateid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

	while($r=mysqli_fetch_array($sql2)){

		echo "<li><a href='diagram.php?id=".$debateid."'><b>".$r['name']."</b></a></li>";

	}
}
else if($returntype=="debate"){
	$sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM matrices WHERE id='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

	while($r=mysqli_fetch_array($sql3)){

		echo "<li><a href='scores.php?id=".$matrixid."'><b>".$r['name']."</b></a></li>";

	}
}

$username = $_SESSION['username'];
$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$debateid'");


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

?>