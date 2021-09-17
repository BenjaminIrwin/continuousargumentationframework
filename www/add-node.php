<?php

include 'dbUtilities.php';

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
    $createdby = $_SESSION['username'];

    $debateid = $_POST['did'];

    $name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['n']);
    $basevalue = $_POST['bv'];

    $type = $_POST['t'];
    $typevalue = $_POST['tv'];
    if($typevalue == 'undefined') {
        $typevalue = "0";
    }
    $state = 'basic';
    $attachment = $_POST['a'];
    $x = $_POST['x'];
    $y = $_POST['y'];



    $sql = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into nodes (debateid, name, type, typevalue, state, attachment, x, y, createdby) Values ($debateid, '$name', '$type', '$typevalue', '$state', '$attachment', '$x', '$y', '$createdby')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

    $nodeid=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

    $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "Insert Into user_node_score (user_id, node_id, base_score) Values ($userid, '$nodeid', '$basevalue')") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

// update lastmodified(by) in debates
    $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$createdby'  WHERE id='$debateid'");

    echo json_encode(array("nodeid"=>$nodeid,"createdby"=>$createdby));

    ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);