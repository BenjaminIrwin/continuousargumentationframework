<?php

include 'dbUtilities.php';
require('Pusher.php');

/*
    This PHP script was significantly edited for Arg&Forecast.
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
$username = $_SESSION['username'];
$id = $_POST['id'];
$basevalue = $_POST['bv'];


$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM user_node_score WHERE node_id = $id and user_id=$userid");

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE user_node_score SET base_score='$basevalue' WHERE user_id='$userid' and node_id='$id'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    } else {
        $sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into user_node_score (user_id, node_id, base_score) Values ($userid, '$id', '$basevalue')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    }
} else {
    echo 'Error: '.mysqli_error($GLOBALS["___mysqli_ston"]);
}

echo json_encode(array("nodeid"=>$id,"modifiedby"=>$username));
