<?php

include 'dbUtilities.php';

/*
    This PHP script was implemented for Arg&Forecast.
*/

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
	echo "Your session expired...";
	die();
}

$userid = $_SESSION['id'];
$questionid = $_POST['qid'];

$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "select id, close, debates.finalforecast as groupforecast, user_debate_scores.forecast as userforecast from debates join user_debate_scores on debates.id = user_debate_scores.debateId and userId = '$userid' and questionId = '$questionid' and close < current_timestamp order by close") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
  $rows[] = $r;
}

$json_encoded_string = json_encode($rows);

echo $json_encoded_string;


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);