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

$did = $_SESSION['debate'];
$uid = $_SESSION['id'];

$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "select * from user_debate_scores where debateId = $did and userId = $uid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
  $rows[] = $r;
}

$json_encoded_string = json_encode($rows);

echo $json_encoded_string;


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);