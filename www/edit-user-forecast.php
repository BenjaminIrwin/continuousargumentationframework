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
$username = $_SESSION['username'];

$debateid = $_SESSION['debate'];
$conscore = $_POST['cs'];
$forecast = $_POST['f'];

try {

    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM user_debate_scores WHERE debateid = '$debateid' and userid='$userid'");

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE user_debate_scores SET forecast='$forecast', confidence_score='$conscore' WHERE debateId='$debateid' and userId='$userid'");
            if($sql1 === false) {
                throw new Exception("Failed.");
            }
        } else {
            $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into user_debate_scores (userid, debateid, confidence_score, forecast) Values ($userid, '$debateid', '$conscore', '$forecast')");
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

//echo ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);


// update lastmodified(by) in debates
//$sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$username'  WHERE id='$debateid'");

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);