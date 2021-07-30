<?php

include 'dbUtilities.php';
require('Pusher.php');

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

$userid = $_SESSION['id'];
$questionid = $_POST['qid'];
$user=$_POST['searchQuery']; $user=trim($user);
    
    
// pick all the usernames of the users
$sql="SELECT username FROM users ORDER BY id";
$query=mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die ("Query non valida: " . mysqli_error($GLOBALS["___mysqli_ston"]));
$users = array();
$i=0;
while($row=mysqli_fetch_array($query)) {

    $users[$i] = $row['username']; 

    $i++;  
}

$result=array();
$num_res=1;
if(strlen($user)>0) {
    for ($i=0; $i<count($users); $i++) {
            if(strtoupper($user) == strtoupper(substr($users[$i],0,strlen($user)))) { 

                $result[$num_res-1] = $users[$i];

                $num_res++;

            }
    }
}


$result=array_unique($result,SORT_REGULAR); // ELIMINO GLI ELEMENTI DUPLICATI NELL'ARRAY RISULTATO
$dim = count($result);
// Costruisco un array della stessa lunghezza di $risultato per fare un marge tra i due ed eliminare i "buchi" che si creerebbero applicando array_filter
$array = array('0');
$array = array_pad($array,$dim, '0'); 
// var_dump($array);
$result = array_merge($result,$array);

$result = array_filter($result);

if(empty($result[0])) {
    $result['success']=0;
    echo json_encode($result);
}
else {

    $sql1 = "SELECT users.id, users.username, rights.accessright FROM users LEFT JOIN rights "
            . "ON users.id=rights.userid AND rights.questionid='$questionid' WHERE users.username='$result[0]'";

    $str = "";
    $i=1;
    $size = count($result);
    while($i<$size){
         $str .= " OR users.username='$result[$i]'";
         $i++;
     }

    $str .= " AND users.id!='$userid'";

    $sql_real = $sql1.$str;

    $query=mysqli_query($GLOBALS["___mysqli_ston"], $sql_real) or die ($sql1 . $str. mysqli_error($GLOBALS["___mysqli_ston"]));
            
    $rows = array();
    while($r = mysqli_fetch_assoc($query)) {
      $rows[] = $r;
    }
     
    
    $toSend = array();
    $toSend['success']=1;
    $toSend['users']=$rows;
    echo json_encode($toSend);

}


    
    
((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

?>