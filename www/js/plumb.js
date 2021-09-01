var instance;
var sourceEndpoint;
var targetEndpoint;

// Colors variables for node.
var color;
var button;

var nodeList = {};
var edgeList = {};

var x;
var y;

var isDragging = false;
var connectionSourceId = "n", connectionTargetId = "n";

jsPlumb.ready(function() {

// Settings user rights.
setRights();

instance = jsPlumb.getInstance({
    Endpoint : ["Dot", {radius:2}],
    HoverPaintStyle : {strokeStyle:"#1e8151", lineWidth:3, outlineWidth:2 },
    DragOptions : { cursor: 'pointer', zIndex:2000 },
    ConnectionOverlays : [
      [ "Arrow", {
        location:1,
        id:"arrow",
                width: 10,
                length:10,
                foldback:0.8
      } ]
    ],
    Container: "diagram"
  });

instance.draggable($(".item"));

// Allow edge deleting only if user has appropriate right.
if (thisRight=='o' || thisRight=='w'){
  instance.bind("click", function(connection, originalEvent) {
    bootbox.confirm("<h3 style='word-wrap: break-word;'>Delete connection from " + edgeList[connection.scope].source.name + " to " + edgeList[connection.scope].target.name + "?</h3>", function(result){
        if(result){
          instance.detach(connection);
          deleteEdge(connection.scope);
        }
      });

    });
}
  // Function called on the drop on an endpoint.
  instance.bind("beforeDrop", function(connection, originalEvent) {

    if (connectionSourceId != connection.sourceId){
      connectionSourceId = connection.sourceId;
      addEdge(connection.sourceId, connection.targetId);
    }
});

loadNodes();


});

function addNode(attachment_path){

    //console.log('here we are');

// Modal initialized in modal.js file.
  var id = $("#node-modal").find(".id-modal > b").html();
  var name = encodeURIComponent($("#node-modal").find(".name-modal > input").val());
  var baseValue = encodeURIComponent($("#node-modal").find(".basevalue-modal > input").val());
  var computedValueQuad = encodeURIComponent($("#node-modal").find(".computedvalue-quad-modal > input").val());
  var computedValueDFQuad = encodeURIComponent($("#node-modal").find(".computedvalue-dfquad-modal > input").val());
  var type = $("#node-modal").find(".type-modal > b").html();
  var typeValue = encodeURIComponent($("#node-modal").find(".typevalue-modal > input").val());
  var state = $("#node-modal").find(".state-modal > select").val();
  var attachment = encodeURIComponent($("#node-modal").find(".attachment-modal > input#url_attachment").val());
  if(attachment_path !== '') {
      attachment += ", "+encodeURIComponent(attachment_path);
  }

    //console.log('HERE WE ARE' + typeValue);


    if(type === 'proposal' && typeValue === '') {
      bootbox.alert('Proposal invalid. You must provide a proposed forecast');
      return;
  }

    //Check here if proposal already exists

    //console.log(1111111 + nodeList);


    if (name==""){
    name = 'Node '+type;
  }

    //console.log(2);

    if(type === 'proposal') {
        //console.log(2.5 + type);

        baseValue = "0.5";
    }
        //console.log(3);


    var result = getDefaultBaseValue(thisDebateId);
    var defaultBaseValue = result.success(function(data){

        var result = JSON.parse(data);
        baseValue = result.basevalue;

        if (computedValueQuad==""){
            computedValueQuad = '0';
          }

          if (computedValueDFQuad==""){
            computedValueDFQuad = '0';
          }

          if (typeValue==""){
            typeValue="";
          }

          // Experimantal x and y values.
          x = 20;
          y = 471;

          checkOverlap();

          //console.log(x+" "+y);

        let data1 = "did="+thisDebateId+"&n="+name+"&bv="+baseValue+"&cvq="+computedValueQuad+"&cvdfq="+computedValueDFQuad+"&t="+type+"&tv="+typeValue+"&s="+state+"&a="+attachment+"&x="+x+"&y="+y;

        //console.log(data1);

        $.ajax({
                    type: "POST",
                    url: "add-node.php",
                    data: data1,
                    cache: false,
                    success: function(dat) {
                        //console.log('hello');

                        //console.log(dat);
                      var obj = JSON.parse(dat);
                      var id = obj["nodeid"];
                      var createdBy = obj["createdby"];
                      var modifiedBy = '';

                      var node = new Node(id,decodeURIComponent(name),decodeURIComponent(baseValue),decodeURIComponent(computedValueQuad),
                                            decodeURIComponent(computedValueDFQuad),decodeURIComponent(type),decodeURIComponent(typeValue),
                                            decodeURIComponent(state),decodeURIComponent(attachment),{},{},x,y,createdBy,modifiedBy);
                      node.initializeNode();

                      //console.log(node);
                      nodeList[id] = node;

                      // Automatic show of the help popover for edges creation.

                      var element_count = 0;

                      for(var e in nodeList)
                        element_count++;

                      if (element_count == 2){
                        $('#advice').popover({content: $("#advice").parent().html()}).popover('show');
                      }

                    }
                });

            });

    //console.log("def base value "+baseValue);
/*
  if (computedValueQuad==""){
    computedValueQuad = '0';
  }
  
  if (computedValueDFQuad==""){
    computedValueDFQuad = '0';
  }

  if (typeValue==""){
    typeValue="";
  }

  // Experimantal x and y values.
  x = 20;
  y = 471;

  checkOverlap();

  //console.log(x+" "+y);

  $.ajax({
            type: "POST",
            url: "add-node.php",
            data: "did="+thisDebateId+"&n="+name+"&bv="+baseValue+"&cvq="+computedValueQuad+"&cvdfq="+computedValueDFQuad+"&t="+type+"&tv="+typeValue+"&s="+state+"&a="+attachment+"&x="+x+"&y="+y,
            cache: false,
            success: function(dat) {
              console.log(dat);
              var obj = JSON.parse(dat);
              var id = obj["nodeid"];
              var createdBy = obj["createdby"];
              var modifiedBy = '';
              
              var node = new Node(id,decodeURIComponent(name),decodeURIComponent(baseValue),decodeURIComponent(computedValueQuad),
                                    decodeURIComponent(computedValueDFQuad),decodeURIComponent(type),decodeURIComponent(typeValue),
                                    decodeURIComponent(state),decodeURIComponent(attachment),{},{},x,y,createdBy,modifiedBy);
              node.initializeNode();
              
              console.log(node);
              nodeList[id] = node;

              // Automatic show of the help popover for edges creation.

              var element_count = 0;

              for(var e in nodeList)
                element_count++;

              if (element_count == 2){
                $('#advice').popover({content: $("#advice").parent().html()}).popover('show');
              }

            }
        });*/



}

function getDefaultBaseValue(debateId){
    
    return $.ajax({
            type: "POST",
            url: "get-default-basevalue.php",
            data: "did="+debateId,
            cache: false,
            success: function(dat) {
                
            }
        });
 
 }

async function getProposedForecast() {

    var pForecast = 0;
    var data = await $.ajax({
        type: "POST",
        url: "get-proposal-node.php",
        cache: false,
    });

    var obj = JSON.parse(data);
    pForecast = obj[0].typevalue;

    return parseFloat(pForecast);
}


function editForecast(conscore, forecast){

    $.ajax({
        type: "POST",
        url: "edit-user-forecast.php",
        data: "cs="+conscore+"&f="+forecast,
        cache: false,
        success: function(data) {
            bootbox.alert('Successful forecast of ' + forecast + '%.');
        }
    });

}

async function refreshNodeList(){

    nodeList=[]

    var dat = await $.ajax({
        type: "POST",
        url: "load-nodes.php",
        data: "did="+thisDebateId,
        cache: false
    });

    var obj = JSON.parse(dat);

    for (var i = 0; i < obj.length; i++) {

        var id = obj[i].id;
        var name = obj[i].name;
        var baseValue = obj[i].basevalue;
        var type = obj[i].type;
        var typeValue = obj[i].typevalue;
        var state = obj[i].state;
        var attachment = obj[i].attachment;
        var x = obj[i].x;
        var y = obj[i].y;
        var createdby = obj[i].createdby;
        var modifiedby = obj[i].modifiedby;

        setNodeColor(type);

        var node = new Node(id,decodeURIComponent(name),decodeURIComponent(baseValue),decodeURIComponent(type),decodeURIComponent(typeValue),decodeURIComponent(state),decodeURIComponent(attachment),{},{},x,y,createdby,modifiedby);

        nodeList[id] = node;

    }

}
 
async function loadNodes(){

  nodeList=[]
  $('.diagramm').html('');

    $.ajax({
            type: "POST",
            url: "load-nodes.php",
            data: "did="+thisDebateId,
            cache: false,
            success: function(dat) {
              var obj = JSON.parse(dat);
              var msg = "";

              for (var i = 0; i < obj.length; i++) {

                  //console.log(obj[i]);

                var id = obj[i].id;
//                var debateId = obj[i].debateId;
                var name = obj[i].name;
                var baseValue = obj[i].basevalue;
                var type = obj[i].type;
                var typeValue = obj[i].typevalue;
                var state = obj[i].state;
                var attachment = obj[i].attachment;
                var x = obj[i].x;
                var y = obj[i].y;
                var createdby = obj[i].createdby;
                var modifiedby = obj[i].modifiedby;

                setNodeColor(type);

                console.log(obj[i]);

                var node = new Node(id,decodeURIComponent(name),decodeURIComponent(baseValue),decodeURIComponent(type),decodeURIComponent(typeValue),decodeURIComponent(state),decodeURIComponent(attachment),{},{},x,y,createdby,modifiedby);

                //console.log(node);
                
                node.initializeNode();

                
                nodeList[id] = node;

                resizeContainer(id);

              }

              // Order the graph on the GUI for a better visualization on page load.
            //  orderGraph();

            loadEdges();
            loadWormholes();

            }
            });

}

async function deleteNode(element){

    const open = await isDebateOpen();
    if(open === true) {
        bootbox.alert('This update framework is closed and therefore now immutable.');
        return;
    }

  var node = element.parentNode.parentNode.parentNode.parentNode;
  bootbox.confirm("<h3>Delete node " + nodeList[node.id].name + "?</h3>", function(result){
        if(result){

          $.ajax({
            type: "POST",
            url: "delete-node.php",
            data: "id="+node.id,
            cache: false,
            success: function(dat) {
              instance.detachAllConnections($("#"+node.id));
              $("#"+node.id).fadeOut(300,function(){$(this).remove()});

              // Delete the node from every source and target list in the graph (inefficient).
              for (var n in nodeList){
                delete nodeList[n].sourceList[node.id];
                delete nodeList[n].targetList[node.id];
              }

              delete nodeList[node.id];

              // Delete from every edge.
              for (var n in edgeList){
                if(edgeList[n].source.id==node.id || edgeList[n].target.id==node.id){

                  delete edgeList[n];

                }
              }
            }
            });

            }
            });

}

async function editNode(node, new_attachment_path){

    var id = node.id;
    var newBaseValue = encodeURIComponent($(".basevalue-modal > input").val());

    $.ajax({
        type: "POST",
        url: "edit-node-score.php",
        data: "id="+node.id+"&bv="+newBaseValue,
        cache: false,
        success: function(dat) {
            var obj = JSON.parse(dat);
            //
            // var modifiedBy = obj["modifiedby"];
            // $("#"+id+" > #name").html(text);
            // $("#"+id+" > #name").attr('title',newName);
            // node.editInfo(decodeURIComponent(newName), decodeURIComponent(newBaseValue), decodeURIComponent(newComputedValueQuad), decodeURIComponent(newComputedValueDFQuad), decodeURIComponent(newTypeValue), decodeURIComponent(newState), decodeURIComponent(newAttachment),decodeURIComponent(modifiedBy));
            $('#' + id).find("#edit-button").attr('onclick', 'modalEditNode(nodeList["' + id + '"], "' + newBaseValue + '")');

            // node.editUserBaseValue(decodeURIComponent(newBaseValue))
            // Modifying node image.
            // $('#' + id).find('img').attr('src','gallery/'+nodeList[id].type+'-basic.png');
        }
    });
}

function editNodeOld(node, new_attachment_path){
    
    //console.log("new att: "+new_attachment_path);
    var id = node.id;
    // var newName = $(".name-modal > input").val();
    var newBaseValue = encodeURIComponent($(".basevalue-modal > input").val());
    var newComputedValueQuad = $(".computedvalue-quad-modal > input").val();
    var newComputedValueDFQuad = $(".computedvalue-dfquad-modal > input").val();
    var newTypeValue = encodeURIComponent($(".typevalue-modal > input").val());

    var newState = nodeList[id].state;
    var newAttachment = encodeURIComponent($("#node-modal").find(".attachment-modal > input#url_attachment").val());
    if(newAttachment.search(new_attachment_path) !== '-1') {
        newAttachment += ", " + encodeURIComponent(new_attachment_path);
    }

    var text = newName;
    if (text.length>labelLength) {
        text = text.substring(0,labelLength-1)+"...";
    };

    $.ajax({
            type: "POST",
            url: "edit-node.php",
            data: "id="+node.id+"&bv="+newBaseValue+"&cvq="+newComputedValueQuad+"&cvdfq="+newComputedValueDFQuad+"&tv="+newTypeValue+"&s="+newState+"&a="+newAttachment,
            cache: false,
            success: function(dat) {
              var obj = JSON.parse(dat);

              var modifiedBy = obj["modifiedby"];
              $("#"+id+" > #name").html(text);
              $("#"+id+" > #name").attr('title',newName);
              node.editInfo(decodeURIComponent(newBaseValue), decodeURIComponent(newComputedValueQuad), decodeURIComponent(newComputedValueDFQuad), decodeURIComponent(newTypeValue), decodeURIComponent(newState), decodeURIComponent(newAttachment),decodeURIComponent(modifiedBy));

              // Modifying node image.
              $('#' + id).find('img').attr('src','gallery/'+nodeList[id].type+'-basic.png');
            }
            });
}


function editComputedValueQuad(node, newComputedValue){

  // nodeList[node.id].computedValueQuad=newComputedValue;
    let data = "id="+node.id+"&n="+node.name+"&bv="+node.baseValue+"&cvq="+newComputedValue+"&tv="+node.typeValue+"&s="+node.state+"&a="+node.attachment;
    //console.log('data -> ' + data);

    $.ajax({
            type: "POST",
            url: "edit-node.php",
            data: data,
            cache: false,
            success: function(dat) {
            }
            });

}

async function editComputedValueDFQuad(node, newComputedValue){

  nodeList[node.id].computedValueDFQuad=newComputedValue;
    let data = "id="+node.id+"&n="+node.name+"&bv="+node.baseValue+"&cvdfq="+newComputedValue+"&tv="+node.typeValue+"&s="+node.state+"&a="+node.attachment;
    await $.ajax({
        type: "POST",
        url: "edit-node.php",
        data: data,
        cache: false
    });

}

function editPosition(node, x, y){

    // if(isDebateOpen() === true) {
    //     bootbox.alert('This update framework is closed and therefore now immutable.');
    //     return;
    // }


  // If position haven't changed do nothing.
  if (node.x==x && node.y == y){ return; }
      $.ajax({
            type: "POST",
            url: "update-position.php",
            data: "id="+node.id+"&x="+x+"&y="+y,
            cache: false,
            success: function(dat) {
              node.x = x;
              node.y = y;

            }
            });

}

function setNodeColor(type){

  if (type=='proposal') {
        color = '#428BCA';
        button = 'btn-primary';
    }
    else if (type==='decrease') {
        color = '#D24642';
        button = 'btn-success';
    }
      else if (type ==='increase') {
          color = '#53AD54';
          button = 'btn-danger';
      }
    else if (type==='pro') {
        color = 'rgba(83,173,84,0.7)';
        button = 'btn-warning';
    }
    else if (type==='con') {
        color = 'rgba(210,70,66,0.7)';
        button = 'btn-warning';
    }

}

function getNodeColor(type){
      if (type=='proposal') {
        return'#428BCA';
    }
    else if (type=='increase') {
        return '#E3972F';
    }
      else if (type=='decrease') {
          return '#D24642';
      }
    else if (type=='pro') {
        return '#53AD54';
    }
    else if (type=='con') {
        return'#D24642';
    }

}

async function addEdge(sourceId, targetId){

    const open = await isDebateOpen();
    if(open === true) {
        bootbox.alert('This update framework is closed and therefore now immutable.');
        return;
    }


    $.ajax({
            type: "POST",
            url: "add-edge.php",
            data: "did="+thisDebateId+"&s="+sourceId+"&t="+targetId,
            cache: false,
            success: function(dat) {
              var id = dat;
              var edge = new Edge(id, nodeList[sourceId], nodeList[targetId]);
              edgeList[id] = edge;

              // Update node source and target list.
              nodeList[targetId].sourceList[sourceId] = nodeList[sourceId]; // Target node has as source list element the source node.
              nodeList[sourceId].targetList[targetId] = nodeList[targetId]; // Source node has as target list element the target node.

              // JsPlumb connection for the GUI visualization.
              instance.connect({scope:id, source:sourceId, target:targetId});
              connectionSourceId = "n", connectionTargetId = "n";

            }
            });
}

function loadEdges(){
  edgeList=[];
  instance.detachEveryConnection();
      $.ajax({
            type: "POST",
            url: "load-edges.php",
            data: "did="+thisDebateId,
            cache: false,
            success: function(dat) {
              var obj = JSON.parse(dat);
              var msg = "";

              for (var i = 0; i < obj.length; i++) {

                var id = obj[i].id;
                var sourceId = obj[i].sourceid;
                var targetId = obj[i].targetid;

                var edge = new Edge(id, nodeList[sourceId], nodeList[targetId]);
                edgeList[id] = edge;

                // Update node source and target list.
                nodeList[targetId].sourceList[sourceId] = nodeList[sourceId]; // Target node has as source list element the source node.
                nodeList[sourceId].targetList[targetId] = nodeList[targetId]; // Source node has as target list element the target node.

                // Connecting using JsPlumb. The scope is used to associate id in database with id in JsPlumb, because in JsPlumb is impossible to set the id of a connection.
                instance.connect({scope:id, source:sourceId, target:targetId});

              }


            }
            });

}

async function deleteEdge(id){

    const open = await isDebateOpen();
    if(open === true) {
        bootbox.alert('This update framework is closed and therefore now immutable.');
        return;
    }

    $.ajax({
    type: "POST",
    url: "delete-edge.php",
    data: "id="+id,
    cache: false,
    success: function(dat) {

      // Update node source and target list.
      delete edgeList[id].source.targetList[edgeList[id].target.id]; // Deleting from source node's target list the target node of the edge.
      delete edgeList[id].target.sourceList[edgeList[id].source.id]; // Deleting from target node's source list the source node of the edge.

      delete edgeList[id];

    }
    });


}

/*
The funciton check if the thisRight variable is equal to 'r'. This means that the user can only read the graph. So proceeds with the disabling of tools for graph editing.
*/
async function setRights(){

    thisRight = await $.ajax({
        type: "POST",
        url: "load-right.php",
        cache: false,
    });

    if(thisRight==='r'){
    $('.node-buttons > button').prop('disabled', true);
    $('.name-label').attr('onDblClick','');
    $('.edit-button').hide();
    $('.wormhole-copy-button').hide();
    $('.wormhole-paste-button').hide();
    $('.dropdown-divider').hide();
    $('.delete-node-button').hide();
  }
}


function setState(element){

  var nodeId = $(element).parents('.item').attr('id');
  var nState = $(element).html();

  nodeList[nodeId].state = nState;

      $.ajax({
            type: "POST",
            url: "edit-state.php",
            data: "id="+nodeId+"&s="+nState,
            cache: false,
            success: function(dat) {
              // Modifying node image.
              $('#'+nodeId).find('img').attr('src','gallery/'+nodeList[nodeId].type+'-basic.png');
            }
            });


}

function setType(element) {
    var nodeId = $(element).parents('.item').attr('id');
    var nType = $(element).html();
    
    nType = nType.toLowerCase();
    nodeList[nodeId].type = nType;

    $.ajax({
            type: "POST",
            url: "edit-node-type.php",
            data: "id="+nodeId+"&type="+nType,
            cache: false,
            success: function(dat) {
                var success = JSON.parse(dat);
                
                if(success.success!==0) {
                    // Modifying node image.
                    $('#'+nodeId).find('img').attr('src','gallery/'+nodeList[nodeId].type+'-basic.png');

                    var type = nodeList[nodeId].type;


                    // Giving ep the right color.
                    $('#' + nodeId).css({
                              'box-shadow' : '0px 0px 10px',
                              'color' : getRightColor(type)
                    });
                }
                else{
                    bootbox.alert("Before changing the type of the node, put at 'Basic' its state!");
                }
            }
        });
}

// Used in node.js to check if a string is an url.
function validURL(str) {
  var pattern = new RegExp(/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/);
  if(!pattern.test(str)) {
    return false;
  } else {
    return true;
  }
}

function makeValidURL(str) {
    str = str.trim();
    str = str.replace(/\s+/g, '%20');
    return str;
}

function resizeContainer(id){
    var top = $('#'+id).offset().top;
    var left = $('#'+id).offset().left;

    var w = $('.diagram').width();
    var h = $('.diagram').height();

    if(top>h){
      $('.diagram').height(top);
    }
    
    if(left+$('#'+id).width()>w){
      $('.diagram, nav').width(left+$('#'+id).width()*2);
    }

}

function resizeNodes(percent){

  $('.item').css('width', 200*percent+'px');
  $('.item').css('height', 100*percent+'px');

  // Repaint all connectors.
  instance.repaintEverything();
  instance.repaintEverything();
}

function checkOverlap(){

  for(var n in nodeList){
    if(nodeList[n].x==x && nodeList[n].y==y){
      x+=10;
      y+=10;

      checkOverlap(x,y);
    }
  }

}

function getRightColor(type){

    if (type=='proposal') {
        return '#428BCA';
    }
    else if (type=='increase') {
        return '#E3972F';
    }
    else if (type=='decrease') {
        return '#E3972F';
    }
    else if (type=='pro') {
        return '#53AD54';
    }
    else if (type=='con') {
        return '#D24642';
    }

}
