// This file represents the JavaScript Debate object.
/*
    This function was implemented for Arg&Forecast.
*/
function Question(id, ownerid, name, accessRight){

	// Attributes.
	this.id = id;
    this.ownerid = ownerid;
	this.name = name;


	// Methods
	this.displayInfo = displayInfo;
        this.editInfo = editInfo;
}

/*
    This function was implemented for Arg&Forecast.
*/
function displayInfo(){

  var msg = "<h3> Info </h3>";
    msg += "<ul id='info' style='list-style-type: none;'>";
    msg += "<li>Id: &nbsp; <b>"+this.id+"</b></li>";
    msg += "<li>ownerid: &nbsp; <b>"+this.ownerid+"</b></li>";
    msg += "<li>Name: &nbsp; <b>"+this.name+"</b></li>";
    msg += "</ul>";
  bootbox.alert(msg);

}
/*
    This function was implemented for Arg&Forecast.
*/
function editInfo(name){
  this.name = name;
}
