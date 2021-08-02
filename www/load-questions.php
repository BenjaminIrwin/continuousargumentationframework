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

$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT questions.id,questions.ownerid,questions.name, questions.initialforecast, questions.open, questions.close, rights.accessright FROM questions LEFT JOIN rights ON questions.id=rights.questionid AND rights.userid=$userid ORDER BY questions.name ASC") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
  $rows[] = $r;
}

$json_encoded_string = json_encode($rows);

echo $json_encoded_string;

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);