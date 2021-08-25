var decimals=6;
var CONFIDENCE_SCORE;

function checkIfTree(){

	for(n in nodeList){
		if(nodeList[n].type=='proposal'){
			var visited=[];
			visited[n]=nodeList[n];
			return traversal(nodeList[n],visited);
		}
	}

}

function traversal(node,visited){
	var sourceList=node.sourceList;

	for(n in sourceList){
		if(typeof visited[n]!='undefined'){
			return false;
		}
		else{
			visited[n]=node;
			return traversal(sourceList[n],visited);
		}
	}
	return true;
}

function fatt1(v0,v){
	v0 = parseFloat(v0);
	v = parseFloat(v);
	return v0*(1-v);
}

function fsupp1(v0,v){
	v0 = parseFloat(v0);
	v = parseFloat(v);
	return v0+v-v*v0;
}

function g(v0,va,vs){

	if (typeof vs === 'undefined' && !(typeof va === 'undefined')){
		return va;
	}
	else if (typeof va === 'undefined' && !(typeof vs === 'undefined')){
		return vs;
	}
	else if(typeof vs === 'undefined' && typeof va === 'undefined'){
		return v0;
	}
	else {
		return (va+vs)/2;
	}

}

function CS(nodeList){

	var increaseCount = 0;
	var increaseTotal = 0;
	var decreaseCount = 0;
	var decreaseTotal= 0;

	for(var n in nodeList){
		if(nodeList[n].type==='increase'){
			++increaseCount;
			increaseTotal += parseFloat(nodeList[n].computedValueDFQuad);
		}

		if(nodeList[n].type==='decrease'){
			++decreaseCount;
			decreaseTotal += parseFloat(nodeList[n].computedValueDFQuad);
		}
	}

	var increaseAverage;
	var decreaseAverage;

	if(increaseCount === 0) {
		increaseAverage = 0;
	} else {
		increaseAverage = increaseTotal/increaseCount;
	}

	if(decreaseCount === 0) {
		decreaseAverage = 0;
	} else {
		decreaseAverage = decreaseTotal/decreaseCount;
	}

	return increaseAverage - decreaseAverage;
}

async function submitForecast(forecast) {
	await computeAllValues(false);
	var pForecast = await getProposedForecast();

	console.log(forecast + ' ' + CONFIDENCE_SCORE + ' ' + pForecast);

	if(CONFIDENCE_SCORE < 0 && forecast >= pForecast) {
		bootbox.alert('Irrational forecast. You have a negative confidence score so must provide a forecast lower than the proposed one.');
	} else if(CONFIDENCE_SCORE > 0 && forecast <= pForecast) {
		bootbox.alert('Irrational forecast. You have a positive confidence score so must provide a forecast higher than the proposed one.');
	} else if((Math.abs(pForecast - forecast)/pForecast) > Math.abs(CONFIDENCE_SCORE)) {
		bootbox.alert('Irrational forecast. The scale of your change to the proposed forecast is not reflected in your confidence score.');
	} else {
		editForecast(CONFIDENCE_SCORE, forecast)
		bootbox.alert('Successful forecast of ' + forecast + '%.');

	}

}

async function computeAllValues(message){

	if(!checkIfTree()){
		bootbox.alert('<h3>This is not a decision tree.</h3>');
		return;
	
        }

	console.log(nodeList);

	// await loadNodes()
	await refreshNodeList()

	console.log(nodeList);

	if(message){
		var amendmentList1=[];

		for(var n in nodeList){
			if(nodeList[n].type==='increase'||nodeList[n].type==='decrease'){
				amendmentList1.push(nodeList[n]);
			}
		}

                var nAlg = 1;
		var msg1 = rankNodes(amendmentList1, nAlg);
                
                msg1+='</ul>';
                
        }

        for (var n in nodeList){

			if(nodeList[n].type === 'increase' || nodeList[n].type === 'decrease' || nodeList[n].type === 'proposal') {
				nodeList[n].baseValue = '0.5';
			}

			if(nodeList[n].baseValue === 'null') {
				console.log('ITS NULL!');
				var alert = '<h3>ALL arguments must be voted on to proceed. Please vote on the following argument:\n' +
					 + nodeList[n].name + '</h3>';
				bootbox.alert(alert);
				return;
			}
			var result = SF2(nodeList[n]);
            var nresult = Math.round(result * Math.pow(10,decimals)) / Math.pow(10,decimals);
			await editComputedValueDFQuad(nodeList[n], nresult);
        }

        const confidenceScore = CS(nodeList);
        console.log('CONFIDENCE SCORE: ' + confidenceScore);
		CONFIDENCE_SCORE = confidenceScore;
        await editConfidenceScore(thisDebateId, confidenceScore);
        
        if(message){

		var amendmentList2=[];

		for(var n in nodeList){
			if(nodeList[n].type=='increase'||nodeList[n].type=='decrease'){
				amendmentList2.push(nodeList[n]);
			}
		}

                var nAlg = 2;
		var msg2 = rankNodes(amendmentList2, nAlg);
	
        
                msg2+='</ul>';

                $('#compute-values-modal').find('.modal-title').html("Amendment ranking");

                $('#compute-values-modal').find('.modal-body').html(msg1+msg2);

                makeItDraggable('#compute-values-modal');
                $('#compute-values-modal').modal('show');
        }   

}

function Fatt1(v0,S){

	if (S.length==0){
		var nil;
		return nil;
	}
	// Extreme value behavior.
	else if (v0==0){
		return 0;
	}
	else if (S[S.length]==0){
		S.pop();
		return Fatt1(v0,S);
	}
	else if (S[S.length]==1){
		return 0;
	}
	else if (S.length==1){
//		alert("S[0]: "+S[0]);
		var fat = fatt1(v0, S[0]);
//		alert("Recursion foot here: " + fat);
		return fat;
	}
	else {
		var vn = S.pop();
//		alert(vn);
		var fat = fatt1(Fatt1(v0,S),vn);
//		alert("fatt: " + fat);
		return fat;
	}

}

function Fsupp1(v0,S){


	if (S.length==0){
		var nil;
		return nil;
	}
	// Extreme value behavior.
	else if (v0==1){
		return 1;
	}
	else if (S[S.length-1]==0){
		S.pop();
                // c'era scritto Fatt!!!!!!!!
		return Fsupp1(v0,S);
	}
	else if (S[S.length-1]==1){
		return 1;
	}
	else if (S.length==1){
//		alert("S[0]: "+S[0]);
		var fsu = fsupp1(v0, S[0]);
//		alert("Recursion foot here: " + fat);
		return fsu;
	}
	else {
		var vn = S.pop();
//		alert(vn);
		var fsu = fsupp1(Fsupp1(v0,S),vn);
//		alert("fatt: " + fat);
		return fsu;
	}

}

function SF1(a){

        //console.log("iniziamo");
        
	var Rs = a.getSupporters();
	var Ra = a.getAttackers();

	var msg = "";

	for (var i = 0; i<Rs.length; i++){
		msg += Rs[i].name + "\n";
	}

	//console.log("Supporters: " + msg);

	var msg = "";

	for (var i = 0; i<Ra.length; i++){
		msg += Ra[i].name[i];
	}

	//console.log("Attackers: " + msg);

	var FSseqSupp = [];

	for (var i = 0; i<Rs.length; i++){
		FSseqSupp.push(SF1(Rs[i]));
	}

	var FSseqAtt = [];

	for (var i = 0; i<Ra.length; i++){
		FSseqAtt.push(SF1(Ra[i]));
	}

	//console.log("FSseqSupp for " + a.name + " : " + FSseqSupp.toString());
	//console.log("FSseqAtt for " + a.name + " : " + FSseqAtt.toString());

	return g(a.baseValue, Fatt1(a.baseValue, FSseqAtt), Fsupp1(a.baseValue, FSseqSupp));


}

function rankNodes(nodes, nAlg){
        
        if(nAlg==1){
            var sorted = nodes.sort(function(a,b){return b.computedValueQuad-a.computedValueQuad});
            
            var msg='<h4>QUAD Algorithm</h4><ul>';
            
            var counter=0;
            var rank=[];

            for(var n in nodes){
                    if(typeof rank[nodes[n].computedValueQuad]==='undefined'){
                            counter++;
                            rank[nodes[n].computedValueQuad]=nodes[n].name;
                    }
                    msg+='<li><b>'+counter+'. '+nodes[n].name+': '+nodes[n].computedValueQuad+'</b></li>';

            }
        }
        else if(nAlg==2){
            var sorted = nodes.sort(function(a,b){return b.computedValueDFQuad-a.computedValueDFQuad});
            
            var msg = '<h4>DF-QUAD Algorithm</h4><ul>';
            
            var counter=0;
            var rank=[];

            for(var n in nodes){
                    if(typeof rank[nodes[n].computedValueDFQuad]==='undefined'){
                            counter++;
                            rank[nodes[n].computedValueDFQuad]=nodes[n].name;
                    }
                    msg+='<li><b>'+counter+'. '+nodes[n].name+': '+nodes[n].computedValueDFQuad+'</b></li>';

            }
        }
        
	
        
        return msg;
}

////////////////////////////////////////
// ******* DF-QUAD ******** //
// SF2, F, SEQ (c'è già?), c

function c(v0,va,vs){
        
        v0=parseFloat(v0);
        va=parseFloat(va);
        vs=parseFloat(vs);
        
        //console.log("v0: "+v0+" va: "+va+" and vs: "+vs);
        
	if (vs == va){
		return v0;
	}
	else {
                var res = v0+(0.5 + (vs-va)/(2*Math.abs(vs-va))-v0)*Math.abs(vs-va);
                //console.log("Res c= "+ res + " temp: "+temp);
		return res;
	}
}

function f2(v1,v2) {
    v1 = parseFloat(v1);
    v2 = parseFloat(v2);
    
    return v1+v2-v1*v2;
}

function F2(S) {
    if (S.length==0){
        //console.log("S.length==0");
	return 0;
    }
    else if (S.length==1) {
        //console.log("S.length==1");
        return S[0];
    }
     // If last attacker/supporter = 0, shorten array
    else if (S[S.length-1]==0){
        S.pop();
        return F2(S);
    }
    // If last attacker/supporter = 1, return 1
    else if (S[S.length-1]==1){
        return 1;
    }
    else if (S.length==2) {
        var res = f2(S[0],S[1]);
        //console.log("S.length==2 S0="+S[0]+" S1="+S[1]+" res= " + res);
        return f2(S[0],S[1]);
    }
    else{    
        
        //console.log("S.length>2 "+S.length);

        var vn = S.pop();
        //console.log("vn: "+ vn);
        
        var fat = f2(F2(S),vn);
        //console.log("fat: " + fat);
        return fat;
        
    }
}

function SF2(a){
        //console.log("iniziamo");
        
	var Rs = a.getSupporters();
	var Ra = a.getAttackers();

	var msg = "";

	for (var i = 0; i<Rs.length; i++){
		msg += Rs[i].name + "\n";
	}
	//console.log("Supporters: " + msg);

	var msg = "";

	for (var i = 0; i<Ra.length; i++){
		msg += Ra[i].name[i];
	}

	//console.log("Attackers: " + msg);

	var FSseqSupp = [];

	for (var i = 0; i<Rs.length; i++){
		FSseqSupp.push(SF2(Rs[i]));
	}

	var FSseqAtt = [];

	for (var i = 0; i<Ra.length; i++){
		FSseqAtt.push(SF2(Ra[i]));
	}

	//console.log("FSseqSupp for " + a.name + " : " + FSseqSupp.toString());
	//console.log("FSseqAtt for " + a.name + " : " + FSseqAtt.toString());

	return c(a.baseValue, F2(FSseqAtt), F2(FSseqSupp));
        
}

