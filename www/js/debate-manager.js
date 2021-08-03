var debateList = {};
var iddebate;

function addDebate(){

	// Modal initialized in modal.js file.
//  var id = $("#debate-modal").find(".id-modal > b").html();
  var questionId = $("#debate-modal").find(".questionid-modal > b").html();
  var name = $("#debate-modal").find(".name-modal > input").val();
  var defaultBaseValue = $("#debate-modal").find(".defaultbasevalue-modal > input").val();
  var participants = $("#debate-modal").find(".participants-modal > input").val();
  var typeValue = $("#debate-modal").find(".typevalue-modal > input").val();


if (name=="") {
    name = "Unknown Debate";
  };
  
if (defaultBaseValue=="") {
    defaultBaseValue = 0.5;
}

$.ajax({
            type: "POST",
            url: "add-debate.php",
            data: "qid="+questionId+"&n="+name+"&dbv="+defaultBaseValue+"&p="+participants+"&tv="+typeValue,
            cache: false,
            success: function(dat) {
              var id = dat;

              var debate = new Debate(id,questionId,name,defaultBaseValue,participants,typeValue);

              var msg = '<div id="debate'+id+'"><li class="btn-group debate">';
                  msg += '<button type="button" class="btn btn-info" onClick="parent.location=\'diagram.php?id='+id+'\'">'+name+'</button>';
                  msg += '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">';
                  msg += '<span class="caret"></span>';
                  msg += '<span class="sr-only">Toggle Dropdown</span>';
                  msg += '</button>';
                  msg += '<ul class="dropdown-menu" role="menu">';
                  msg += '<li><a href="#" onClick="debateList['+id+'].displayInfo();">Info</a></li>';
                  msg += '<li><a href="#" onClick="modalEditDebate(debateList['+id+'])">Edit</a></li>';
                  msg += '<li><a href="#" onClick="modalAccess('+id+',debateList['+id+'].name)">Access control</a></li>';
                  msg += '<li class="divider"></li>';
                  msg += '<li><a href="#" onClick="deleteDebate(debateList['+id+'])">Delete</a></li>';
                  msg += '</ul>';
                msg += '</li><br><br></div>';

                // The end 'o' means in owner tab, because is been created by this user and owned by him or her.
              $("#debate-list-o").append(msg);
              debateList[id] = debate;
                        }
            });

}

function addSubDebate(){

  // Modal initialized in modal.js file.
//  var id = $("#debate-modal").find(".id-modal > b").html();
  var ownerId = $("#debate-modal").find(".ownerId-modal > b").html();
  var name = $("#debate-modal").find(".name-modal > input").val();
  var defaultBaseValue = $("#debate-modal").find(".defaultbasevalue-modal > input").val();
  var participants = $("#debate-modal").find(".participants-modal > input").val();
  var typeValue = $("#debate-modal").find(".typevalue-modal > input").val();




if (name=="") {
    name = "Unknown Debate";
  };

$.ajax({
            type: "POST",
            url: "add-debate.php",
            data: "on="+ownerId+"&n="+name+"&dbv="+defaultBaseValue+"&p="+participants+"&tv="+typeValue,
            cache: false,
            success: function(dat) {
                console.log('hola');
                var id = dat;
              var debate = new Debate(id,ownerId,name,defaultBaseValue,participants,typeValue);
              debateList[id] = debate;
                // Node creation.
                var baseValue = defaultBaseValue;
                var computedValueQuad = 0;
                var computedValueDFQuad = 0;
                var type = 'por';
                var typeValue = '';
                var state='';
                var attachment = '';
                  $.ajax({
                    type: "POST",
                    url: "add-node.php",
                    data: "n="+name+"&bv="+baseValue+"&cvq="+computedValueQuad+"&cvdfq="+computedValueDFQuad+"&t="+type+"&tv="+typeValue+"&s="+state+"&a="+attachment+"&ld"+id,
                    cache: false,
                    success: function(dat) {
                      var nid = dat;
                      var node = new Node(nid,name,baseValue,computedValueQuad,computedValueDFQuad,type,typeValue,state,attachment,{},{},id);
                      node.initializeDebateNode();
                      nodeList[id] = node;

            }
            });

            }
            });


}

function isDebateOpen() {

    var open = false;

    $.ajax({
        type: "POST",
        url: "load-debate.php",
        async: false,
        cache: false,
        success: function(dat) {

            var obj = JSON.parse(dat);
            var msg = "";

            for (var i = 0; i < obj.length; i++) {

                let closingDate = Date.parse(obj[i].close);

                if(closingDate > Date.now()) {
                    open = true;
                    return;
                }
            }
        }

    });
    return open;
}

function isADebateOpen(questionId) {

    var open = false;

    $.ajax({
        type: "POST",
        url: "load-debates.php",
        data: "qid="+questionId,
        async: false,
        cache: false,
        success: function(dat) {

            var obj = JSON.parse(dat);
            var msg = "";

            for (var i = 0; i < obj.length; i++) {

                let closingDate = Date.parse(obj[i].close);

                if(closingDate > Date.now()) {
                    open = true;
                    return;
                }
            }
        }

    });
    return open;
}

function debateModifiedCheck() {

    $.ajax({
        type: "POST",
        url: "load-just-modified-debate.php",
        async: false,
        cache: false,
        success: function(dat) {

            if (!$.trim(dat)) {
                return;
            } else {
                window.location.reload(false);
                // window.location.reload(true);
            }
        }

    });
}

function getPreviousDebate() {

    $.ajax({
        type: "POST",
        url: "load-last-debate.php",
        async: false,
        cache: false,
        success: function(dat) {

            if (!$.trim(dat)) {
                return;
            } else {
                var obj = JSON.parse(dat);
                var msg = "";
                window.location.href = "diagram.php?id=" + obj[0].id;
            }
        }

    });
}

function getNextDebate() {

    $.ajax({
        type: "POST",
        url: "load-next-debate.php",
        async: false,
        cache: false,
        success: function(dat) {

            if (!$.trim(dat)) {
                return;
            } else {
                var obj = JSON.parse(dat);
                var msg = "";
                window.location.href = "diagram.php?id=" + obj[0].id;
            }
        }

    });
}

function loadDebates(questionid){
  $.ajax({
            type: "POST",
            url: "load-debates.php",
            data: "qid="+questionid,
            cache: false,
            success: function(dat) {

                var obj = JSON.parse(dat);
                var msg = "";

              for (var i = 0; i < obj.length; i++) {

                var id = obj[i].id;
                var questionid = obj[i].questionid;
                var name = obj[i].name;
                var defaultBaseValue = obj[i].defaultbasevalue;
                var participants = obj[i].participants;
                var typeValue = obj[i].typevalue;
                var open = obj[i].open;
                var close = obj[i].close;

                var debate = new Debate(id,questionid,name,defaultBaseValue,participants,typeValue);


              //      <button class="addIncrease btn btn-success" onClick="modalInitNode('increase');">Add increase argument</button>

              var msg = '<div id="debate'+id+'"><li class="btn-group debate">';

              let now = new Date();

              if(Date.parse(close) > now) {
                  console.log('ID:'+id+' is in the future');
                  msg += '<button type="button" class="btn btn-success" onClick="parent.location=\'diagram.php?id='+id+'\'">' + ' ' + name+'</button>';
                  msg += '<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">';
                  msg += '<span class="caret"></span>';
                  msg += '<span class="sr-only">Toggle Dropdown</span>';
                  msg += '</button>';
                  msg += '<ul class="dropdown-menu" role="menu">';
                  msg += '<li><a href="#" onClick="debateList['+id+'].displayInfo();">Info</a></li>';
                  msg += '<li class="divider"></li>';
                  msg += '<li><a href="#" id="delete-debate-button" onClick="deleteDebate(debateList['+id+'])">Delete</a></li>';
                  msg += '</ul>';
                  msg += '</li><br><br></div>';

              } else {
                  console.log('ID:'+id+' is not in the future');
                  msg += '<button type="button" class="btn btn-info" onClick="parent.location=\'diagram.php?id='+id+'\'">'+name+'</button>';
                  msg += '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">';
                  msg += '<span class="caret"></span>';
                  msg += '<span class="sr-only">Toggle Dropdown</span>';
                  msg += '</button>';
                  msg += '<ul class="dropdown-menu" role="menu">';
                  msg += '<li><a href="#" onClick="debateList['+id+'].displayInfo();">Info</a></li>';
                  msg += '<li class="divider"></li>';
                  msg += '<li><a href="#" id="delete-debate-button" onClick="deleteDebate(debateList['+id+'])">Delete</a></li>';
                  msg += '</ul>';
                  msg += '</li><br><br></div>';
              }

              $("#debate-list").append(msg);

              debateList[id] = debate;

              }


            }

  });

}

function deleteDebate(debate){

  bootbox.confirm("<h3>Delete debate " + debate.name + "?</h3>", function(result){
        if(result){

          $.ajax({
            type: "POST",
            url: "delete-debate.php",
            data: "id="+debate.id,
            cache: false,
            success: function(dat) {
              $("#debate"+debate.id).fadeOut(300, function(){

                delete debateList['debate.id'];

              });

            }
            });

        }
      });

}

function editDebate(debate){

    var newName = $('#debate-modal').find(".name-modal > input").val();
    var newDefaultBaseValue = $('#debate-modal').find(".defaultbasevalue-modal > input").val();
    var newParticipants = $('#debate-modal').find(".participants-modal > input").val();
    var newTypeValue = $('#debate-modal').find(".typevalue-modal > input").val();

    $.ajax({
            type: "POST",
            url: "edit-debate.php",
            data: "id="+debate.id+"&n="+newName+"&dbv="+newDefaultBaseValue+"&p="+newParticipants+"&tv="+newTypeValue,
            cache: false,
            success: function(dat) {

              $("#debate"+debate.id+" > .debate > .btn:eq(0)").html(newName);
              debate.editInfo(newName, newDefaultBaseValue, newParticipants, newTypeValue);

            }
            });
}

function editConfidenceScore(debateId, confidenceScore){

    $.ajax({
        type: "POST",
        url: "edit-confidence-score.php",
        data: "id="+ debateId +"&c="+confidenceScore,
        cache: false,
        success: function(dat) {

            // $("#debate"+debate.id+" > .debate > .btn:eq(0)").html(newName);
            // debate.editInfo(newName, newDefaultBaseValue, newParticipants, newTypeValue);

        }
    });
}

function unsubscribeDebate(debate) {
  
    bootbox.confirm("<h3>Do you want to unsubscribe from " + debate.name + "?</h3>", function(result){
        if(result){
          $.ajax({
            type: "POST",
            url: "unsubscribe-debate.php",
            data: "did="+debate.id,
            cache: false,
            success: function(dat) {
                
                    $("#debate"+debate.id).fadeOut(300, function(){

                        delete debateList['debate.id'];

              });

            }
            });

        }
      });
   
}

function getDebateScoreChart(questionId, questionOpen, questionClose, initialForecast){

    // x-axis label and label in tooltip
    var X_AXIS = 'Date';

// y-axis label and label in tooltip
    var Y_AXIS = 'Forecast (%)';

// Should y-axis start from 0? `true` or `false`
    var BEGIN_AT_ZERO = true;

// `true` to show the grid, `false` to hide
    var SHOW_GRID = true;

// `true` to show the legend, `false` to hide
    var SHOW_LEGEND = true;

    $.ajax({
        type: "POST",
        url: "load-debate-scores.php",
        data: "qid="+questionId,
        cache: false,
        success: function(data) {
            var obj = JSON.parse(data);
            var msg = "";

            var forecastDate = [];
            var groupForecast = [];
            var userForecast = [];


            for (var i = 0; i < obj.length; i++) {
                forecastDate[i] = obj[i].close;
                groupForecast[i] = obj[i].groupforecast;
                if(obj[i].userforecast === null) {
                    userForecast[i] = obj[i].groupforecast;
                } else {
                    userForecast[i] = obj[i].userforecast;
                }
            }

            forecastDate.unshift(questionOpen);
            groupForecast.unshift(initialForecast);
            userForecast.unshift(initialForecast);


            var datasets = [];
            datasets.push(
                {
                    label: 'You', // column name
                    data: userForecast, // data in that column
                    fill: false // `true` for area charts, `false` for regular line charts
                }
            )

            datasets.push(
                {
                    label: 'Group', // column name
                    data: groupForecast, // data in that column
                    fill: false // `true` for area charts, `false` for regular line charts
                }
            )

            console.log(datasets);

            // Get container for the chart
            var ctx = document.getElementById('chart-container').getContext('2d');

            new Chart(ctx, {
                type: 'line',

                data: {
                    labels: forecastDate,
                    datasets: datasets,
                },

                options: {
                    // title: {
                    //     display: true,
                    //     text: TITLE,
                    //     fontSize: 14,
                    // },
                    legend: {
                        display: SHOW_LEGEND,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                max: questionClose
                            },
                            distribution: 'series',
                            scaleLabel: {
                                display: X_AXIS !== '',
                                labelString: X_AXIS,
                            },
                            gridLines: {
                                display: SHOW_GRID,
                            },
                            ticks: {
                                maxTicksLimit: 10,
                                callback: function(value, index, values) {
                                    return value.toLocaleString();
                                }
                            }
                        }],
                        yAxes: [{
                            stacked: false, // `true` for stacked area chart, `false` otherwise
                            beginAtZero: true,
                            scaleLabel: {
                                display: Y_AXIS !== '',
                                labelString: Y_AXIS
                            },
                            gridLines: {
                                display: SHOW_GRID,
                            },
                            ticks: {
                                maxTicksLimit: 10,
                                beginAtZero: BEGIN_AT_ZERO,
                                callback: function(value, index, values) {
                                    return value.toLocaleString()
                                }
                            }
                        }]
                    },
                    tooltips: {
                        displayColors: false,
                        callbacks: {
                            label: function(tooltipItem, all) {
                                return all.datasets[tooltipItem.datasetIndex].label
                                    + ': ' + tooltipItem.yLabel.toLocaleString();
                            }
                        }
                    },
                    plugins: {
                        colorschemes: {
                            /*
                              Replace below with any other scheme from
                              https://nagix.github.io/chartjs-plugin-colorschemes/colorchart.html
                            */
                            scheme: 'brewer.DarkTwo5'
                        }
                    }
                }
            });

        }
    });

}