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
$username = $_SESSION['username'];

$debateid = $_POST['did'];
$conscore = $_POST['cs'];
$forecast = $_POST['f'];

$sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE user_debate_scores SET confidence_score='$conscore' and forecast='$forecast' WHERE debateId='$debateid' and userId='$userid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

// update lastmodified(by) in debates
$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$id'");

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);