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


$id = $_POST['id'];
$name = $_POST['n'];
$basevalue = $_POST['bv'];
$computedvalue = $_POST['cv'];
$typevalue = $_POST['tv'];
$state = $_POST['s'];
$attachment = $_POST['a'];

$sqldebateid=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debateid FROM nodes WHERE id=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

while($r=mysqli_fetch_array($sqldebateid)){
	$debateid=$r['debateid'];
}

$sql = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE nodes SET name='$name', basevalue='$basevalue', computedvalue='$computedvalue', typevalue='$typevalue', state='$state', attachment='$attachment' WHERE id=$id") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

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
$data['computedvalue']=$computedvalue;
$data['typevalue']=$typevalue;
$data['state']=$state;
$data['attachment']=$attachment;

$pusher->trigger('test_channel', 'my_event', $data);

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);