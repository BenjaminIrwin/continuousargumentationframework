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

//$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM debates WHERE ownerid=$userid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
//$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debates.id,debates.ownerid,debates.name,debates.defaultbasevalue,debates.participants,debates.typevalue,rights.accessright FROM debates LEFT JOIN rights ON debates.id=rights.debateid AND rights.userid=$userid ORDER BY debates.name ASC") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debates.id,debates.ownerid,debates.name,debates.defaultbasevalue,debates.participants,debates.typevalue,rights.accessright, confidence_score, forecast FROM (debates JOIN rights ON debates.id=rights.debateid AND rights.userid=312) LEFT JOIN user_debate_scores uds on debates.id = uds.debateId and uds.userid = 312 ORDER BY debates.name ASC") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
  $rows[] = $r;
}

echo json_encode($rows);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);