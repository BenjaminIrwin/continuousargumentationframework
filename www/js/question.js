// This file represents the JavaScript Debate object.

function Question(id, ownerid, name, accessRight){

	// Attributes.
	this.id = id;
    this.ownerid = ownerid;
	this.name = name;


	// Methods
	this.displayInfo = displayInfo;
        this.editInfo = editInfo;
}


function displayInfo(){

  var msg = "<h3> Info </h3>";
    msg += "<ul id='info' style='list-style-type: none;'>";
    msg += "<li>Id: &nbsp; <b>"+this.id+"</b></li>";
    msg += "<li>ownerid: &nbsp; <b>"+this.ownerid+"</b></li>";
    msg += "<li>Name: &nbsp; <b>"+this.name+"</b></li>";
    msg += "</ul>";
  bootbox.alert(msg);

}

function editInfo(name){
  this.name = name;
}
