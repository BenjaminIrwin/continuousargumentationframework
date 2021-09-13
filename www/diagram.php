<?php

include 'dbUtilities.php';

$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
  die();
}

$userid = $_SESSION['id'];
$debateid = $_GET['id'];
$qid = $_SESSION['questionid'];
$_SESSION['debate']=$debateid;
if (isset($_GET['nid'])){
  $nodeid = $_GET['nid'];
}
else {
  $nodeid='';
}

$sqldata1 = mysqli_query($GLOBALS["___mysqli_ston"], "select * from debates left join user_debate_scores on id = debateId and userId = $userid where id = $debateid;") or die();

while ($r1 = mysqli_fetch_array($sqldata1)) {
  $name = $r1['name'];
  $defaultBaseValue = $r1['defaultbasevalue'];
  $participants = $r1['participants'];
  $questionid = $r1['questionId'];
  $typeValue = $r1['typevalue'];
  $close = $r1['close'];
  $currForecast = $r1['forecast'];
  if($currForecast == null) {
      $currForecast = "-";
  }

}

$sqldata2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM rights WHERE userid='$userid' AND questionid=$qid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

if (mysqli_num_rows($sqldata2)<=0){
  echo "Forbidden.";
  die();
}

while($r2 = mysqli_fetch_array($sqldata2)){
  $right = $r2['accessright'];
  $_SESSION['right'] = $right;
}

if ($right=='n' | $right==""){
  echo "Forbidden.";
  die();
}

$sqldata3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT username FROM users WHERE id='$userid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

while($r3=mysqli_fetch_array($sqldata3)){
  $username=$r3['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo $name; ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="js/jquery-3.6.0.min.js"></script>

        <script src="js/go-debug.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
        <script src="jsPlumb-master/dist/js/dom.jsPlumb-1.6.4.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <!-- Site custom css -->
        <link rel="stylesheet" href="css/site.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootbox.min.js"></script>
        <script src="js/bootstrap-select.js"></script>
        <script src="js/speech-synthesis-master/polyfill.min.js"></script>
        <script src="//js.pusher.com/2.2/pusher.min.js"></script>



        <script src="js/plumb.js"></script>
        <script src="js/label.js"></script>
        <script src="js/modals.js"></script>

        <script src="js/debate-manager.js"></script>
        <script src="js/debate.js"></script>
        <script src="js/countdown.js"></script>

        <script src="js/node.js"></script>
        <script src="js/edge.js"></script>
        <script src="js/order-graph.js"></script>

        <script src="js/wormholes.js"></script>

        <script src="js/graph.js"></script>

        <script src="js/graph-mapping.js"></script>

        <script src="js/mapping-manager.js"></script>

        <script src="js/natural-text.js"></script>

        <script src="js/natural-text.js"></script>

        <script src="js/push-manager.js"></script>

          <script type="text/javascript">
    // Enable pusher logging - don't include this in production
 /*   Pusher.log = function(message) {
      if (window.console && window.console.log) {
        window.console.log(message);
      }
    };*/

    var pusher = new Pusher('4a093e77bfac049910cf');
    var channel = pusher.subscribe('test_channel');
    channel.bind('my_event', function(data) {
      managePush(data);
    });
  </script>

        <style type="text/css">

.item {
  border: 1px solid black;
  border-radius: 5px;
  background-color: #ffffff;
  color: #333333;
  font-family: 'Oxygen', sans-serif;
  box-shadow: 0px 0px 5px;
  width: 200px;
  height: 100px;
  position: absolute;
  left: 300px;
  top: 300px;
  z-index: 3;
}

.ep{ box-shadow: none !important; }

.item span, .item textarea {
  color: black;
}

#proposal {
  position: absolute;
  left: 100px;
  top: 100px;
}

#state2 {
  position: absolute;
  left: 250px;
  top: 100px;
}

#state3 {
  position: absolute;
  left: 100px;
  top: 250px;
}

.ep {
  position:absolute;
  bottom: 36%;
  left: 3%;
  width:20%;
  /*height:41%;//REMOVED THIS*/
  cursor:pointer;
  padding: 2px 2px 2px 2px;
  box-shadow: 0px 0px 3px black;
  -webkit-transition:-webkit-box-shadow 0.25s ease-in;
  -moz-transition:-moz-box-shadow 0.25s ease-in;
  transition:box-shadow 0.25s ease-in;
}

#dropdown-button {
  position:absolute;
  top: 1px;
  right: 3px;
  width:1em;
  height:1em;
  cursor:pointer;
  z-index: 1;
}


.wormhole-label {
  position:absolute;
  bottom: 7%;
  left: 10px;
  cursor:pointer;
  font-size: 10px;
  display: none;
}

#name {
  position:absolute;
  top: 18%;
  left: 25%;
  width:75%;
  padding: 0px 5px 5px 5px;
  word-wrap: break-word;
  text-align: left;
  overflow: auto;
  height: 78%;
}

#name_entry {
  position:absolute;
  top: 19%;
  left: 26%;
  width:10em;
  height:5em;
  text-align: left;
}

.dropdown-submenu{position:relative;}
.dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
.dropdown-submenu:hover>.dropdown-menu{display:block;}
.dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
.dropdown-submenu:hover>a:after{border-left-color:#ffffff;}
.dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}

</style>

  <div id="debate-modal" class="modal fade">
  <div id="draggable" class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 class="modal-title" style="color: white;"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

  <div id="node-modal" class="modal fade">
  <div id="draggable" class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 class="modal-title" style="color: white;"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="focusami" type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="compute-values-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

  <div id="natural-text-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info">Speak</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

      <script>

          $(document).ready(function(){
              getDebateCountdownTimer('<?php echo $close; ?>');
              getMyForecast();
              debateModifiedCheck();
              $('#forecast').on('keyup', function(e) {
                  if (e.keyCode === 13) {
                      $('#forecastSubmit').click();
                  }
              });

              $('#forecastSubmit').click(function() {
                  var input = document.getElementById('forecast').value;
                  if(isNaN(parseFloat(input)) && !isFinite(input)) {
                      bootbox.alert('Only numeric values are accepted as forecast.');
                      return;
                  }

                  let forecast = parseFloat(input);
                  if(!(forecast >= 0 && forecast <= 100)) {
                      bootbox.alert('Forecast must be a valid percentage (i.e. number between 0 and 100).');
                      return;
                  }

                  submitForecast(forecast);
                  return false;
              });
          });

      </script>

  </head>


  <body>

<nav class="navbar navbar-default" role="navigation">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header container">
      <a class="navbar-brand" href="questions.php">Questions</a>
      <a class="navbar-brand" href="logout.php">Logout</a>
    </div>
  </div>
<div class="jumbotron">
  <div id="header-slider" class="container">
      <div class="slider-nav">
        <img src="gallery/left-arrow.png" onclick="getPreviousDebate()" height="42" style="margin-top:10px;float:left; margin-right:50px; margin-left;10px;">
        <img src="gallery/right-arrow.png" onclick="getNextDebate()" height="42" style="margin-top:10px;float:right; margin-left;50px;text-align:right;">
      </div>
      <h1 style="float:left; text-align:center; margin-top: 0px; width: 89%;"><?php echo $name.'</br>';?></h1>
      <br style="clear:both;">
      <h5 class="text-center">
          <p>My Forecast: <p id="currentForecast" style="font-weight: bold;"></p></p>
      </h5>
      <h5 class="text-center">
          <p>Status: <p id="demo"></p></p>
      </h5>
  </div>
</div>
</nav>

<div class="container">
  <a href="debates.php?id=<?php echo $questionid?>" style="float: right;"> Back </a>
</div>
<br>
<div class="container">
<div class="dropdown" style="float: left; display: inline;">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
    Debates
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
    <?php

    $sqldata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT debates.id, debates.name, rights.accessright FROM debates LEFT JOIN rights ON debates.id=rights.questionid WHERE rights.userid = '$userid' ORDER BY accessright ASC") or die();

    while($r = mysqli_fetch_assoc($sqldata)) {

      echo "<li role='presentation'><a role='menuitem' tabindex='-1' href='diagram.php?id=".$r['id']."'>".$r['name']." (".$r['accessright'].")</a></li>";

    }

?>
  </ul>
  </div>

  <div class="dropdown" style="float: left; margin: 0px 10px; display: inline;">
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownSizes" data-toggle="dropdown">
    Size
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownSizes">
    <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" onClick="resizeNodes(2)">200%</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" onClick="resizeNodes(1.5)">150%</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:void(0)" onClick="resizeNodes(1)">100%</a></li>
  </ul>
</div>


<tag><button class="btn btn-default" onClick="loadNodes()">Refresh</button></tag>


  <a id="advice" href="javascript:void(0)" class="pull-right" tabindex="-1" role="button" data-toggle="popover" data-placement="left" data-trigger="hover"
  data-content="Drag an edge from a node's image and drop it on another node to create connections."><img src="gallery/help.png" style="width:20px;" /></a>
  <script>
  $(function () {
  $('[data-toggle="popover"]').popover();
})
  </script>

</div>

<br>

<div class="container node-buttons">
    <button class="addProposal btn btn-primary" id="bbb" onClick="modalInitNode('proposal', '<?php echo $close; ?>');">Add proposal</button>
    <button class="addIncrease btn btn-success" onClick="modalInitNode('increase', '<?php echo $close; ?>');">Add increase argument</button>
    <button class="addDecrease btn btn-danger" onClick="modalInitNode('decrease', '<?php echo $close; ?>');">Add decrease argument</button>
    <button class="addPro btn btn-success-pale" onClick="modalInitNode('pro', '<?php echo $close; ?>');">Add pro argument</button>
    <button class="addCon btn btn-danger-pale" onClick="modalInitNode('con', '<?php echo $close; ?>');">Add con argument</button>
<!--    <button class="btn btn-default" onClick="computeAllValues(true)">Compute values</button>-->
<!--    <button class="addCon btn btn-warning" onClick="modalInitNode('con');">Add con argument</button>-->

    <div class="pull-left graph-operations">
    <button class="btn btn-default" onClick="computeAllValues(null)">Compute values</button>

        <li class='name-modal'>Forecast: <input type='text' id='forecast' class='form-control' placeholder='Forecast'/><input type="submit" id="forecastSubmit" /></li><br>

        <?php

    // MAPPING //
    // Only the user who create the mapping can see the mapping.
      $sql2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM mapping WHERE userid='$userid' AND debateid='$debateid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      if(mysqli_num_rows($sql2)>0){
        echo "<div class='btn-group mapping-list'>";
      }
      else{
        echo "<div class='btn-group mapping-list' style='display:none'>";
      }
      echo "
      <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
      <span class='glyphicon glyphicon-share'></span>
      </button>
      <ul class='dropdown-menu pull-right' role='menu'>";

      while($s=mysqli_fetch_array($sql2)){

        $matrixid=$s['matrixid'];
        $sql3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM matrices WHERE id='$matrixid'") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

        while($t=mysqli_fetch_array($sql3)){
          $matrixname=$t['name'];
          echo "<li><a href='tables.php?id=".$matrixid."'><b>".$matrixname."</b></a></li>";
        }
      }
      echo "</ul></div>";

      ((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

    ?>

  </div>

</div>

<div class="container diagramm" style="height: 650px;">
</div>
<div class="container diagram" style="height: 650px;">
<div style="display: none;">

  <div id="proposal" class="item" style="text-align: center;"><!-- Single button -->
<div class="btn-group" id="dropdown-button">
  <div type="button" class="dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-cog"></span>
  </div>
  <ul class="dropdown-menu" role="menu">
    <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>


<!--  <li class="dropdown-submenu">-->
<!--    <a tabindex="-1" href="javascript:void(0)">Edit state</a>-->
<!--    <ul class="dropdown-menu">-->
<!--      <li><a tabindex="-1" href="javascript:void(0)" onClick="setState(this)">Basic</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Resolved</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Rejected</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Insoluble</a></li>-->
<!--    </ul>-->
<!--  </li>-->


    <li><a id="wormhole-copy-button" class="wormhole-copy-button" href="javascript:void(0)" onClick="">Copy wormhole</a></li>
        <li><a id="wormhole-paste-button" class="wormhole-paste-button" href="javascript:void(0)" onClick="">Paste wormhole</a></li>
    <li class="divider dropdown-divider"></li>
    <li><a id="delete-node-button" class="delete-node-button" href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
  </ul>
</div>
<div class="ep"></div>
  <div type="button" class="dropdown-toggle wormhole-label" data-toggle="dropdown">
    <span class="glyphicon glyphicon-link"></span>
  </div>
  <ul class="dropdown-menu dropdown-wormholes" role="menu">
  </ul>


    <img class="ep" src="gallery/Question.png" style="display:block;"></img>
    <span id="name" class="name-label" title="Issue" onDblClick="clickLabel(this);">Issue</span>
    <textarea id="name_entry" style="display: none;" onBlur="blurLabel(this);"></textarea>
  </div>

  <div id="increase" class="item" style="text-align: center;"><!-- Single button -->
<div class="btn-group" id="dropdown-button">
  <div type="button" class="dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-cog"></span>
  </div>
  <ul class="dropdown-menu" role="menu">
    <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>

<!--    <li class="dropdown-submenu">-->
<!--    <a tabindex="-1" href="javascript:void(0)">Edit state</a>-->
<!--    <ul class="dropdown-menu">-->
<!--      <li><a tabindex="-1" href="javascript:void(0)" onClick="setState(this)">Basic</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Accepted</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Rejected</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Likely</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Unlikely</a></li>-->
<!--    </ul>-->
<!--  </li>-->

    <li><a id="wormhole-copy-button" class="wormhole-copy-button" href="javascript:void(0)" onClick="">Copy wormhole</a></li>
        <li><a id="wormhole-paste-button" class="wormhole-paste-button" href="javascript:void(0)" onClick="">Paste wormhole</a></li>
    <li class="divider dropdown-divider"></li>
    <li><a id="delete-node-button" class="delete-node-button" href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
  </ul>
</div>
<div class="ep"></div>
  <div type="button" class="dropdown-toggle wormhole-label" data-toggle="dropdown">
    <span class="glyphicon glyphicon-link"></span>
  </div>
  <ul class="dropdown-menu dropdown-wormholes" role="menu">
  </ul>

    <img class="ep" src="gallery/Light.png" style="display:block;"></img>
    <span id="name" class="name-label" title="Issue" onDblClick="clickLabel(this);">Issue</span>
    <textarea id="name_entry" style="display: none;" onBlur="blurLabel(this);"></textarea>
  </div>

    <div id="decrease" class="item" style="text-align: center;"><!-- Single button -->
        <div class="btn-group" id="dropdown-button">
            <div type="button" class="dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-cog"></span>
            </div>
            <ul class="dropdown-menu" role="menu">
                <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>

                <!--    <li class="dropdown-submenu">-->
                <!--    <a tabindex="-1" href="javascript:void(0)">Edit state</a>-->
                <!--    <ul class="dropdown-menu">-->
                <!--      <li><a tabindex="-1" href="javascript:void(0)" onClick="setState(this)">Basic</a></li>-->
                <!--      <li><a href="javascript:void(0)" onClick="setState(this)">Accepted</a></li>-->
                <!--      <li><a href="javascript:void(0)" onClick="setState(this)">Rejected</a></li>-->
                <!--      <li><a href="javascript:void(0)" onClick="setState(this)">Likely</a></li>-->
                <!--      <li><a href="javascript:void(0)" onClick="setState(this)">Unlikely</a></li>-->
                <!--    </ul>-->
                <!--  </li>-->

                <li><a id="wormhole-copy-button" class="wormhole-copy-button" href="javascript:void(0)" onClick="">Copy wormhole</a></li>
                <li><a id="wormhole-paste-button" class="wormhole-paste-button" href="javascript:void(0)" onClick="">Paste wormhole</a></li>
                <li class="divider dropdown-divider"></li>
                <li><a id="delete-node-button" class="delete-node-button" href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
            </ul>
        </div>
        <div class="ep"></div>
        <div type="button" class="dropdown-toggle wormhole-label" data-toggle="dropdown">
            <span class="glyphicon glyphicon-link"></span>
        </div>
        <ul class="dropdown-menu dropdown-wormholes" role="menu">
        </ul>

        <img class="ep" src="gallery/Light.png" style="display:block;"></img>
        <span id="name" class="name-label" title="Issue" onDblClick="clickLabel(this);">Issue</span>
        <textarea id="name_entry" style="display: none;" onBlur="blurLabel(this);"></textarea>
    </div>

  <div id="pro" class="item" style="text-align: center;"><!-- Single button -->
<div class="btn-group" id="dropdown-button">
  <div type="button" class="dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-cog"></span>
  </div>
  <ul class="dropdown-menu" role="menu">
      <li><a id="edit-button" class="edit-button" href="javascript:void(0)" onClick="">Vote</a></li>
      <li class="divider dropdown-divider"></li>
      <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>

<!--    <li class="dropdown-submenu">-->
<!--    <a tabindex="-1" href="javascript:void(0)">Edit state</a>-->
<!--    <ul class="dropdown-menu">-->
<!--      <li><a tabindex="-1" href="javascript:void(0)" onClick="setState(this)">Basic</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Dominant</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Fails</a></li>-->
<!--    </ul>-->
<!--  </li>-->

    <li><a id="wormhole-copy-button" class="wormhole-copy-button" href="javascript:void(0)" onClick="">Copy wormhole</a></li>
        <li><a id="wormhole-paste-button" class="wormhole-paste-button" href="javascript:void(0)" onClick="">Paste wormhole</a></li>
    <li class="divider dropdown-divider"></li>
    <li><a id="delete-node-button" class="delete-node-button" href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
  </ul>
</div>
<div class="ep"></div>
  <div type="button" class="dropdown-toggle wormhole-label" data-toggle="dropdown">
    <span class="glyphicon glyphicon-link"></span>
  </div>
  <ul class="dropdown-menu dropdown-wormholes" role="menu">
  </ul>

    <img class="ep" src="gallery/Plus.png" style="display:block;"></img>
    <span id="name" class="name-label" title="Pro" onDblClick="clickLabel(this);">Issue</span>
    <textarea id="name_entry" style="display: none;" onBlur="blurLabel(this);"></textarea>
  </div>

  <div id="con" class="item" style="text-align: center;"><!-- Single button -->
<div class="btn-group" id="dropdown-button">
  <div type="button" class="dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-cog"></span>
  </div>
  <ul class="dropdown-menu" role="menu">
      <li><a id="edit-button" class="edit-button" href="javascript:void(0)" onClick="">Vote</a></li>
      <li class="divider dropdown-divider"></li>
      <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>

<!--    <li class="dropdown-submenu">-->
<!--    <a tabindex="-1" href="javascript:void(0)">Edit state</a>-->
<!--    <ul class="dropdown-menu">-->
<!--      <li><a tabindex="-1" href="javascript:void(0)" onClick="setState(this)">Basic</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Dominant</a></li>-->
<!--      <li><a href="javascript:void(0)" onClick="setState(this)">Fails</a></li>-->
<!--    </ul>-->
<!--  </li>-->

    <li><a id="wormhole-copy-button" class="wormhole-copy-button" href="javascript:void(0)" onClick="">Copy wormhole</a></li>
        <li><a id="wormhole-paste-button" class="wormhole-paste-button" href="javascript:void(0)" onClick="">Paste wormhole</a></li>
    <li class="divider dropdown-divider"></li>
    <li><a id="delete-node-button" class="delete-node-button" href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
  </ul>
</div>
<div class="ep"></div>
  <div type="button" class="dropdown-toggle wormhole-label" data-toggle="dropdown">
    <span class="glyphicon glyphicon-link"></span>
  </div>
  <ul class="dropdown-menu dropdown-wormholes" role="menu">
  </ul>

    <img class="ep" src="gallery/Minus.png" style="display:block;"></img>
    <span id="name" class="name-label" title="Con" onDblClick="clickLabel(this);">Issue</span>
    <textarea id="name_entry" style="display: none;" onBlur="blurLabel(this);"></textarea>
  </div>










    <div id="por" class="item" style="text-align: center;">
      <!--
<div class="btn-group" id="dropdown-button">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    <span class="glyphicon glyphicon-cog"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a id="info-button" href="javascript:void(0)" onClick="">Information</a></li>
    <li><a id="edit-button" class="edit-button" href="javascript:void(0)" onClick="">Edit</a></li>
    <li class="divider"></li>
    <li><a href="javascript:void(0)" onClick="deleteNode(this)">Delete node</a></li>
  </ul>
</div>
-->

  <!--  <div id="myDiagramDiv"
     style="width:800px; height:600px; background-color: #DAE4E4;"></div> -->

  <script>
  $('.selectpicker').selectpicker({
      style: 'btn-info',
      size: 3
  });

  var thisUserId = <?php echo $userid; ?>;
  var thisDebateId = <?php echo $debateid; ?>;
  var thisUsername = '<?php echo $username; ?>';
  var thisRight = '<?php echo $right; ?>';
  var thisNodeId = '<?php echo $nodeid; ?>';

  </script>
  </body>
</html>
