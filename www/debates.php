<?php
$session_expiration = time() + 3600 * 24 * 2; // +2 days
session_set_cookie_params($session_expiration);
session_start();

include "dbUtilities.php";

$connection=dbConnect();

if (!isset($_SESSION['id'])) {
//  echo "Your session expired...";
  header("Location: index.php");
  die();
}


$qid = $_GET["id"];
$_SESSION['questionid'] = $qid;


$sql = mysqli_query($GLOBALS["___mysqli_ston"], "Select * From questions Where id=$qid") or die(mysqli_error($GLOBALS["___mysqli_ston"]));

$s = mysqli_fetch_array($sql);

$questionname = $s["name"];
$open = $s["open"];
$close = $s["close"];
$initialForecast = $s["initialForecast"];

((is_null($___mysqli_res = mysqli_close($connection))) ? false : $___mysqli_res);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo $questionname; ?></title>
        <script src="js/jquery-3.6.0.min.js"></script>

        <script src="js/go-debug.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
        <script src="jsPlumb-master/dist/js/dom.jsPlumb-1.6.4.js"></script>
      <!-- Load Chart.js -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
      <script src="https://unpkg.com/chartjs-plugin-colorschemes@0.4.0/dist/chartjs-plugin-colorschemes.min.js"></script>
<!--      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->

      <!-- Load PapaParse to read csv files -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.1.0/papaparse.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootbox.min.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
      <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script src="js/debate-manager.js"></script>
        <script src="js/modals.js"></script>

        <script src="js/debate.js"></script>

        <script src="js/access-manager.js"></script>

        <style type="text/css">
        .item {
  border: 1px solid black;
  background-color: #ddddff;
  width: 200px;
  height: 100px;
  position: absolute;
  left: 300px;
  top: 300px;
}

#issue {
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
  bottom: 37%;
  left: 7px;
  width:1.8em;
  height:1.8em;
  background-color:orange;
  cursor:pointer;
  box-shadow: 0px 0px 1px black;
  -webkit-transition:-webkit-box-shadow 0.25s ease-in;
  -moz-transition:-moz-box-shadow 0.25s ease-in;
  transition:box-shadow 0.25s ease-in;
}

#dropdown-button {
  position:absolute;
  bottom: 43%;
  right: 20px;
  width:1.8em;
  height:1.8em;
  cursor:pointer;
}
.nav-tabs{
  width:100%;
}

.tab-pane{
  min-height: 500px;
}
    </style>


  <div id="debate-modal" class="modal fade">
  <div class="modal-dialog">
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

  <div id="access-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 class="modal-title" style="color: white;"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

$(document).ready(function(){
    //console.log('QUESTION ID' + <?php echo $qid; ?>);
    loadDebates(<?php echo $qid; ?>);
    getDebateScoreChart(<?php echo $qid; ?>, '<?php echo $open; ?>', '<?php echo $close; ?>', <?php echo $initialForecast; ?>);
});

</script>
  </head>

  <body>

  <nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header container">
      <a class="navbar-brand" href="questions.php">Questions</a>
      <a class="navbar-brand" href="logout.php">Logout</a>
    </div>
  </div>
    <div class="jumbotron">
  <div class="container"><h1><?php echo $questionname; ?></h1></div>
</div>
</nav>




  <div class="container">
      <div>
          <canvas id="chart-container" style="height: 300px; width: 100%"></canvas>
      </div>

<!--      <script src="js/chart.js"></script>-->


<button class="addIssue btn btn-info" onClick="modalInitDebate('<?php echo $qid; ?>');">Add debate</button><br><br><br>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#debate-list" role="tab" data-toggle="tab">Debates</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="debate-list"><br></div>
</div>

</div>




  <br><br><br>
  <hr class="divider">

  <div class="container">
    <footer>
  <p>Posted by: Benjamin Irwin</p>
  <p>Contact information: <a href="mailto:benjamintdirwin@gmail.com">
  benjamintdirwin@gmail.com</a>.</p>
</footer>

  </div>

  <script>
/*  $('#owned-tab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
});

  $('#write-tab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
});

  $('#read-tab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
});*/
  </script>


  <!--  <div id="myDiagramDiv"
     style="width:800px; height:600px; background-color: #DAE4E4;"></div> -->
  </body>
</html>
