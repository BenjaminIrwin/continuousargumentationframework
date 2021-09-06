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

if(isset($_POST['did']) && !empty($_POST['did']) && !is_null($_POST['did'])) {
    $debateid = $_POST['did'];
} else {
    $debateid = $_SESSION['debate'];
}

//$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM nodes n LEFT JOIN user_node_score uns on n.id = uns.node_id and uns.user_id = $userid WHERE debateid = $debateid ORDER BY type ASC") or die();
$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM nodes WHERE debateid = $debateid limit 1") or die();

$rows = array();
while($r = mysqli_fetch_assoc($sqldata)) {
    $rows[] = $r;
}

$json_encoded_string = json_encode($rows);

// manage percent symbol for json encoding
//$json_encoded_string = escape_percentage_for_json($json_encoded_string);

echo $json_encoded_string;

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);