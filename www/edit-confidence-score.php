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

$username = $_SESSION['username'];

$id = $_POST['id'];
$confidencescore = $_POST['c'];

if(isset($_POST['uid']) && !empty($_POST['uid']) && !is_null($_POST['uid'])) {
    $userid = $_POST['uid'];
} else {
    $userid = $_SESSION['id'];
}

$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM user_debate_scores WHERE debateid = '$id' and userid='$userid'");

try {

    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM user_debate_scores WHERE debateid = '$id' and userid='$userid'");

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE user_debate_scores SET confidence_score='$confidencescore' WHERE debateId=$id and userId=$userid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
            if($sql1 === false) {
                throw new Exception("Failed.");
            }
        } else {
            $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into user_debate_scores (userid, debateid, confidence_score) Values ($userid, '$id', '$confidencescore')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
            if($sql2 === false) {
                throw new Exception("Failed.");
            }
        }
    } else {
        echo 'Error: ' . mysqli_error($GLOBALS["___mysqli_ston"]);
        throw new Exception("Failed.");
    }
} catch(Exception $e) {
    echo mysqli_error($GLOBALS["___mysqli_ston"]);
    http_response_code(400);
}


echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

//$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$id'");


((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);