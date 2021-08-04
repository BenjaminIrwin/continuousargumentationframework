#!/usr/bin/php<?php

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

//id=8&n=Yes&bv=0.5&cvq=0.95&tv=&s=Basic&a=

$id = $_POST['id'];
$name = $_POST['n'];
//$name = escape_apex($name);
$basevalue = $_POST['bv'];
//$computedvaluequad = $_POST['cvq'];
$computedvaluedfquad = $_POST['cvdfq'];
$state = $_POST['s'];
$attachment = $_POST['a'];

$sqldebateid=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debateid, modifiedby FROM nodes WHERE id=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

while($r=mysqli_fetch_array($sqldebateid)){
	$debateid=$r['debateid'];
        $modifiedby=$r['modifiedby'];
}

$pos = strpos($modifiedby, $username.' ');
$modifiedby_str = '';
// don't repeat the name of the user if it has already modified the node at least once
if($pos === false) {
    
    $modifiedby_str = $username." ".$modifiedby;
    
} else  $modifiedby_str = $modifiedby;

$sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE nodes SET name='$name', state='$state', attachment='$attachment', modifiedby='$modifiedby_str' WHERE id='$id'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$nodeid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

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

echo json_encode(array("nodeid"=>$nodeid,"modifiedby"=>$modifiedby_str));

$app_id = '104765';
$app_key = '4a093e77bfac049910cf';
$app_secret = '3525036469c1d8f547c8';

$pusher = new Pusher($app_key, $app_secret, $app_id);

$data['push'] = 'edit-node';
$data['userid']=$userid;
$data['debateid']=$debateid;
$data['nodeid']=$id;
$data['name']=$name;
$data['basevalue']=$basevalue;
$data['computedvaluedfquad']=$computedvaluedfquad;
$data['state']=$state;
$data['attachment']=$attachment;
$data['createdby']=$username;
$data['modifiedby']=$modifiedby_str;

$pusher->trigger('test_channel', 'my_event', $data);


// update the date of the last modified (by) of the current debate
$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$debateid'");

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);