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
$username = $_SESSION['username'];

$id = $_POST['id'];
$confidencescore = $_POST['c'];

$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM user_debate_scores WHERE debateid = '$id' and userid='$userid'");

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE user_debate_scores SET confidence_score='$confidencescore' WHERE debateId=$id and userId=$userid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    } else {
        $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into user_debate_scores (userid, debateid, confidence_score) Values ($userid, '$id', '$confidencescore')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    }
} else {
    echo 'Error: '.mysqli_error($GLOBALS["___mysqli_ston"]);
}

echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$id'");


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);