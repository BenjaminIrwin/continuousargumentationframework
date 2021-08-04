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

$userid = $_POST['uid'];
$questionid = $_POST['qid'];
$right = $_POST['r'];



$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM rights WHERE userid='$userid' AND questionid='$questionid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
if($right!=''){
    if (mysqli_num_rows($sql1)>0){
            $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE rights SET accessright='$right',modified=CURRENT_TIMESTAMP WHERE userid='$userid' AND questionid='$questionid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    }
    else {
            $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO rights (userid,questionid,accessright,modified) VALUES ('$userid','$questionid','$right',CURRENT_TIMESTAMP)") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    }
}
else {
    $sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM rights WHERE userid='$userid' AND questionid='$questionid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
}

echo 'OK!';

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);