<?php

include 'dbUtilities.php';
require('Pusher.php');

/*
    This PHP script was significantly edited for Arg&Forecast.
*/

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id']) || !isset($_SESSION['debate'])) {
	echo "Your session expired...";
	die();
}



$userid = $_SESSION['id'];
$username = $_SESSION['username'];
$debateid = $_POST['did'];




$sourceid = $_POST['s'];
$targetid = $_POST['t'];


$sql = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into edges (debateid,sourceid,targetid) Values ('$debateid','$sourceid','$targetid')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$edgeid=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
echo $edgeid;

// update lastmodified(by) in debates
$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$debateid'");

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);