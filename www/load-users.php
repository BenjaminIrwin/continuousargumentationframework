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
$questionid = $_POST['qid'];


$sqldata1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT users.id, users.username, rights.accessright FROM users LEFT JOIN rights "
        . "ON users.id=rights.userid AND rights.questionid='$questionid' WHERE users.id!='$userid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$rows = array();
while($r = mysqli_fetch_assoc($sqldata1)) {
  $rows[] = $r;
}


echo json_encode($rows);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);