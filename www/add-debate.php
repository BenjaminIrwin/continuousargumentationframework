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

$name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['n']); 
$defaultbasevalue = $_POST['dbv'];
$participants =  $_POST['p'];
$typevalue = $_POST['tv'];
$questionid = $_POST['qid'];

//echo "'<script>console.log(\"$userid\")</script>'";

$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into debates (questionId,name,defaultbasevalue,participants,typevalue) Values ('$questionid','$name','$defaultbasevalue','$participants','$typevalue')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$debateid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
$_SESSION['debate']=$debateid;
echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

$sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO rights (userid,questionid,accessright,modified) VALUES ('$userid','$debateid','o',CURRENT_TIMESTAMP) ") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

?>