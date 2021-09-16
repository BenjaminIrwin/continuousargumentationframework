var questionList = {};
var idquestion;

/*
    This function was implemented for Arg&Forecast.
*/
function addQuestion(){

    // Modal initialized in modal.js file.
    //  var id = $("#question-modal").find(".id-modal > b").html();
  var ownerId = $("#question-modal").find(".ownerId-modal > b").html();
  var name = $("#question-modal").find(".name-modal > input").val();
  var participants = $("#question-modal").find(".participants-modal > input").val();
  var baseRate = $("#question-modal").find(".intial-base-rate-modal > input").val();
  var typeValue = $("#question-modal").find(".typevalue-modal > input").val();
  var openDate = $("#openDate").val();
  var closeDate = $("#closeDate").val();



    if (baseRate=="") {
        bootbox.alert('You must specify a base rate when you create a new question!')
        return;
    };

    if (closeDate=="") {
        bootbox.alert('You must specify a closing date when you create a new question!')
        return;
    };

    if (openDate=="") {
        bootbox.alert('You must specify an opening date when you create a new question!')
        return;
    };

    if(isNaN(baseRate)) {
        bootbox.alert('Base rate must be a number!')
        return;
    }

    if(baseRate < 0 || baseRate > 100) {
        bootbox.alert('Base rate must be a percentage (between 0 and 100)!')
        return;
    }

  if (name=="") {
    name = "Unknown Question";
  };
  
  var defaultBaseValue = 0.5;

$.ajax({
            type: "POST",
            url: "add-question.php",
            data: "on="+ownerId+"&n="+name+"&dbv="+defaultBaseValue+"&p="+participants+"&tv="+typeValue+"&br="+baseRate+"&od="+openDate+"&cd="+closeDate,
            cache: false,
            success: function(dat) {
              var id = dat;

              var question = new Question(id,ownerId,name,defaultBaseValue,participants,typeValue);

              var msg = '<div id="question'+id+'"><li class="btn-group question">';
                  msg += '<button type="button" class="btn btn-info" onClick="parent.location=\'debates.php?id='+id+'\'">'+name+'</button>';
                  msg += '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">';
                  msg += '<span class="caret"></span>';
                  msg += '<span class="sr-only">Toggle Dropdown</span>';
                  msg += '</button>';
                  msg += '<ul class="dropdown-menu" role="menu">';
                  msg += '<li><a href="#" onClick="questionList['+id+'].displayInfo();">Info</a></li>';
                  msg += '<li><a href="#" onClick="modalEditQuestion(questionList['+id+'])">Edit</a></li>';
                  msg += '<li><a href="#" onClick="modalAccess('+id+',questionList['+id+'].name)">Access control</a></li>';
                  msg += '<li class="divider"></li>';
                  msg += '<li><a href="#" onClick="deleteQuestion(questionList['+id+'])">Delete</a></li>';
                  msg += '</ul>';
                msg += '</li><br><br></div>';

                // The end 'o' means in owner tab, because is been created by this user and owned by him or her.
              $("#question-list-o").append(msg);
              questionList[id] = question;
                        }
            });

}

/*
    This function was implemented for Arg&Forecast.
*/
function addSubQuestion(){

  // Modal initialized in modal.js file.
//  var id = $("#question-modal").find(".id-modal > b").html();
  var ownerId = $("#question-modal").find(".ownerId-modal > b").html();
  var name = $("#question-modal").find(".name-modal > input").val();
  var defaultBaseValue = $("#question-modal").find(".defaultbasevalue-modal > input").val();
  var participants = $("#question-modal").find(".participants-modal > input").val();
  var typeValue = $("#question-modal").find(".typevalue-modal > input").val();




if (name=="") {
    name = "Unknown Question";
  };

$.ajax({
            type: "POST",
            url: "add-question.php",
            data: "on="+ownerId+"&n="+name+"&dbv="+defaultBaseValue+"&p="+participants+"&tv="+typeValue,
            cache: false,
            success: function(dat) {
                var id = dat;
              var question = new Question(id,ownerId,name,defaultBaseValue,participants,typeValue);
              questionList[id] = question;
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
                      node.initializeQuestionNode();
                      nodeList[id] = node;

            }
            });

            }
            });


}

/*
    This function was implemented for Arg&Forecast.
*/
function loadQuestions(){
  $.ajax({
            type: "POST",
            url: "load-questions.php",
            data: "",
            cache: false,
            success: function(dat) {

                var obj = JSON.parse(dat);
                var msg = "";

              for (var i = 0; i < obj.length; i++) {

                var id = obj[i].id;
                var ownerId = obj[i].ownerid;
                var name = obj[i].name;
                var right = obj[i].accessright;

                var question = new Question(id,ownerId,name);

              var msg = '<div id="question'+id+'"><li class="btn-group question">';
                  msg += '<button type="button" class="btn btn-info" onClick="parent.location=\'debates.php?id='+id+'\'">'+name+'</button>';
                  msg += '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">';
                  msg += '<span class="caret"></span>';
                  msg += '<span class="sr-only">Toggle Dropdown</span>';
                  msg += '</button>';
                  msg += '<ul class="dropdown-menu" role="menu">';
                  msg += '<li><a href="#" onClick="questionList['+id+'].displayInfo();">Info</a></li>';
                  msg += '<li><a id="modal-edit-question" href="#" onClick="modalEditQuestion(questionList['+id+'])">Edit</a></li>';
                  msg += '<li><a id="modal-access-button" href="#" onClick="modalAccess('+id+',questionList['+id+'].name)">Access control</a></li>';
                  msg += '<li><a id="unsubscribe-question" href="#" onClick="unsubscribeQuestion(questionList['+id+'])">Unsubscribe</a></li>';
                  msg += '<li class="divider"></li>';
                  msg += '<li><a href="#" id="delete-question-button" onClick="deleteQuestion(questionList['+id+'])">Delete</a></li>';
                  msg += '</ul>';
                msg += '</li><br><br></div>';

              $("#question-list-"+right).append(msg);

              questionList[id] = question;

              }


            }

  });

}

/*
    This function was implemented for Arg&Forecast.
*/
function deleteQuestion(question){

  bootbox.confirm("<h3>Delete question " + question.name + "?</h3>", function(result){
        if(result){

          $.ajax({
            type: "POST",
            url: "delete-question.php",
            data: "id="+question.id,
            cache: false,
            success: function(dat) {
              $("#question"+question.id).fadeOut(300, function(){

                delete questionList['question.id'];

              });

            }
            });

        }
      });

}

/*
    This function was implemented for Arg&Forecast.
*/
function editQuestion(question){

    var newName = $('#question-modal').find(".name-modal > input").val();
    var newDefaultBaseValue = $('#question-modal').find(".defaultbasevalue-modal > input").val();
    var newParticipants = $('#question-modal').find(".participants-modal > input").val();
    var newTypeValue = $('#question-modal').find(".typevalue-modal > input").val();

    $.ajax({
            type: "POST",
            url: "edit-question.php",
            data: "id="+question.id+"&n="+newName,
            cache: false,
            success: function(dat) {

              $("#question"+question.id+" > .question > .btn:eq(0)").html(newName);
              question.editInfo(newName, newDefaultBaseValue, newParticipants, newTypeValue);

            }
            });
}

/*
    This function was implemented for Arg&Forecast.
*/
function unsubscribeQuestion(question) {
  
    bootbox.confirm("<h3>Do you want to unsubscribe from " + question.name + "?</h3>", function(result){
        if(result){
          $.ajax({
            type: "POST",
            url: "unsubscribe-question.php",
            data: "did="+question.id,
            cache: false,
            success: function(dat) {
                
                    $("#question"+question.id).fadeOut(300, function(){

                        delete questionList['question.id'];

              });

            }
            });

        }
      });
   
}