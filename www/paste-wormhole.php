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
$dstdebate = $_POST['did'];
$dstnode = $_POST['dn'];


// Recover srcdebate and src node of the copied wormhole.
$sqluser = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM wormholes WHERE userid='$userid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM wormholes WHERE dstdebate IS NULL AND dstnode IS NULL") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

// Check if copied value exists.
if (mysqli_num_rows($sqluser)<=0 or mysqli_num_rows($sql1)<=0){
	echo "ALERT: trying to paste a non copied wormhole.";
	die();
}

while ($r=mysqli_fetch_array($sql1)){
	$srcdebate = $r['srcdebate'];
	$srcnode = $r['srcnode'];
}

// Check source and destination debate, because wormholes have sense only between differemt debates.

if ($srcdebate == $dstdebate){
	echo "ALERT: the wormhole can't connect two node of the same room.";
	die();
}

// Check the existence of a possible duplication of a wormhole and avoid it.
$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM wormholes WHERE (srcnode='$srcnode' AND dstnode='$dstnode') OR (srcnode='$dstnode' AND dstnode='$srcnode')" )  or die(mysqli_error($GLOBALS["___mysqli_ston"]));

if (mysqli_num_rows($sql2)>0){
	echo "ALERT : this wormole already exists in the database.";
	die();
}

$sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE wormholes SET dstdebate='$dstdebate', dstnode='$dstnode' WHERE userid='$userid' AND dstdebate IS NULL AND dstnode IS NULL") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$sql4 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM debates WHERE id='$srcdebate'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();

while($r = mysqli_fetch_assoc($sql4)) {
	$sql5 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT name FROM nodes WHERE id='$srcnode' ") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
	$noderow = mysqli_fetch_assoc($sql5);
	$srcnodename = $noderow['name'];
  $rows[] = array_merge($r,array_merge(array('srcnode' => $srcnode),array('srcnodename' => $srcnodename)));
}

$encodedrows=json_encode($rows);
echo $encodedrows;

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

//	echo $srcdebate;
