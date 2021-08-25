<?php

include 'dbUtilities.php';
require('Pusher.php');

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

$app_id = '104765';
$app_key = '4a093e77bfac049910cf';
$app_secret = '3525036469c1d8f547c8';

$pusher = new Pusher($app_key, $app_secret, $app_id);

$data['push'] = 'edit-node';
$data['userid']=$userid;
$data['basevalue']=$basevalue;
$data['createdby']=$username;

$pusher->trigger('test_channel', 'my_event', $data);
