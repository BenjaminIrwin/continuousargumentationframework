<?php
//
//include 'dbUtilities.php';
//
//$session_expiration = time() + 3600 * 24 * 2; // +2 days
//session_set_cookie_params($session_expiration);
//session_start();
//
//$connection=dbConnect();
//
//if (!isset($_SESSION['id'])) {
//	echo "Your session expired...";
//	die();
//}
//
//$userid = $_SESSION['id'];
//$questionid = $_SESSION['questionid'];
//
//$sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM debates WHERE questionId ORDER BY close DESC limit 1") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
//
//$rows = array();
//while($r = mysqli_fetch_assoc($sqldata)) {
//  $rows[] = $r;
//}
//
////if (mysqli_num_rows($r) == 0) {
////    $sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM debates where id = $debateid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));
////
////}
//
//$json_encoded_string = json_encode($rows);
//
//echo $json_encoded_string;
//
//
//((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);