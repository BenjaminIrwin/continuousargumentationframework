<?php
  ob_start();
  include "dbUtilities.php";
  $session_expiration = time() + 3600 * 24 * 2; // +2 days
  session_set_cookie_params($session_expiration);
  session_start();
?>
<html>
  <head>
    <title>Arg & Forecast</title>
        <script src="js/jquery-3.6.0.min.js"></script>

        <script src="js/go-debug.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
        <script src="jsPlumb-master/dist/js/dom.jsPlumb-1.6.4.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootbox.min.js"></script>

        <script src="js/pong.js"></script>
  </head>
  <style type="text/css">

  #login_container{
  width: 200px;
  height: 200px;
  margin: auto;
}

  </style>

  <script>

    function inviaCredenziali(){

      var username = $("input[name='username']").val();
      var password = $("input[name='password']").val();

      $.ajax({
        type: "POST",
        url: "check-credentials.php",
        data: "username="+username+"&password="+password,
        cache: false,
        success: function(dat) {
          if(dat=="OK") location.reload();
          else
            $("#login-error").html(dat);
            $("#login-error").slideDown();
              }
              });
    }

  </script>

  <div id="pong-modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">PONG!</h4>
      </div>
      <div class="modal-body">
              <canvas id="myCanvas" width="500" height="300">
                <!-- Insert fallback content here -->
            </canvas>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

  <body>

<div class="jumbotron">
  <div class="container"><h1 style="text-align:center;">Arg & Forecast</h1>
    <h4 style="text-align:center;">Argue and Forecast</h4>
    <div style="text-align: center; padding-top:10px;">

</div>
  </div>
</div>
<div style="text-align:center;">
  <div style="display:inline-block; vertical-align:top;">
  <img src="gallery/A&D-icon.png" style="width:150px;"/>
  </div>
  <?php

    if(isset($_SESSION['id'])){
      header("Location: questions.php");
    }
    else{
      echo "
      <div id=\"login_container\" style=\"display:inline-block; vertical-align:top;\">

      <label class=\"username\">Username: </label><br>
      <input type=\"text\" class=\"username\" name=\"username\"/><br>
      <label class=\"password\">Password: </label><br>
      <input type=\"password\" class=\"password\" name=\"password\"/><br><br>
      <input class=\"btn btn-default\" type=\"submit\" class=\"submit\" name=\"invio\" value=\"Login\" onclick=\"inviaCredenziali()\">
      <br><div id=\"login-error\" class=\"errore\" hidden=true></div><br>
      <a href=\"signup.php\">Sign up</a>
      </div>
      ";
    }


  ?>
<div style="display:inline-block; vertical-align:top;">
    <img src="gallery/A&D-icon2.png" style="width:150px;"/>
  </div>
</div>
  <br><br><br>

  <div style="position:absolute; bottom:5px; right:5px; z-index:6">
<a href="#" onMouseOver="$('#footer').fadeIn(100);">About</a>
</div>

  <div id="footer" style="display: none; z-index:10;">
    <hr class="divider">
    <footer style="padding-left:5px;">
  <p>Posted by: Benjamin Irwin</p>
  <p>Contact information: <a href="mailto:benjamintdirwin@gmail.com">
  benjamintdirwin@gmail.com</a>.</p>
  <p>Code available on Github <a href="https://github.com/dariopellegrini/arganddec" target="_blank">here</a>.</p>
</footer>

<script>

$(document).mousemove(function(event){
    if(event.pageY<$(document).height()-300){
      $('#footer').fadeOut(100);
    }
});

</script>

  </div>
  </body>
</html>
