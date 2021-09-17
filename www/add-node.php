<?php

include 'dbUtilities.php';
require('Pusher.php');

/*
    This PHP script was significantly edited for Arg&Forecast.
*/

try {
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

    $app_id = '104765';
    $app_key = '4a093e77bfac049910cf';
    $app_secret = '3525036469c1d8f547c8';

    $pusher = new Pusher($app_key, $app_secret, $app_id);

    $data['push'] = 'add-node';
    $data['userid']=$userid;
    $data['debateid']=$debateid;
    $data['nodeid']=$nodeid;
    $data['name']=$name;
    $data['basevalue']=$basevalue;
    $data['type']=$type;
    $data['typevalue']=$typevalue;
    $data['state']=$state;
    $data['attachment']=$attachment;
    $data['x']=$x;
    $data['y']=$y;
    $data['createdby']=$createdby;
    $data['modifiedby']='';



    $pusher->trigger('test_channel', 'my_event', $data);

// update lastmodified(by) in debates
    $sql1 = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE debates SET lastmodified=CURRENT_TIMESTAMP, lastmodifiedby='$createdby'  WHERE id='$debateid'");

    echo json_encode(array("nodeid"=>$nodeid,"createdby"=>$createdby));

    ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}