var decimals=6;
var CONFIDENCE_SCORE;

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

/*
    This function was implemented for Arg&Forecast.
*/
async function CS(nodeList){

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

/*
    This function was significantly edited for Arg&Forecast.
*/
async function submitForecast(forecast) {
	const computed = await computeAllValues();
	if(!computed) {
		return;
	}
	var pForecast = await getProposedForecast(null);

	if(CONFIDENCE_SCORE < 0 && forecast >= pForecast) {
		bootbox.alert('Irrational forecast. You have a negative confidence score so must provide a forecast lower than the proposed one.');
	} else if(CONFIDENCE_SCORE > 0 && forecast <= pForecast) {
		bootbox.alert('Irrational forecast. You have a positive confidence score so must provide a forecast higher than the proposed one.');
	} else if((Math.abs(pForecast - forecast)/pForecast) > Math.abs(CONFIDENCE_SCORE)) {

		var direction;
		var limit;

		if(CONFIDENCE_SCORE < 0) {
			direction = 'minimum';
			limit = (pForecast - (Math.abs(CONFIDENCE_SCORE) * pForecast)).toFixed(0);
		} else {
			direction = 'maximum'
			limit = (pForecast + (Math.abs(CONFIDENCE_SCORE) * pForecast)).toFixed(0);
		}

		var msg = 'Irrational forecast. According to your confidence score (' + CONFIDENCE_SCORE + '), the ' + direction + ' you may forecast is ' + limit + '%.';

		bootbox.alert(msg);

	} else {
		editForecast(CONFIDENCE_SCORE, forecast);
		$("#currentForecast").text(forecast + '%');
	}

}

/*
    This function was implemented for Arg&Forecast Experiment 1
*/
async function experiment1Helper(debateid) {

			var pForecast = await getProposedForecast(debateid);
			var irrationalForecasts = [];

			var data1 = await $.ajax({
				type: "POST",
				url: "load-ghost-forecast.php",
				data: "did="+debateid,
				cache: false,
				tryCount : 0,
				retryLimit : 3,
				error : function(xhr, textStatus, errorThrown ) {
					console.log('BAD GATEWAY')
					this.tryCount++;
					console.log(this.tryCount);
					if (this.tryCount <= this.retryLimit) {
						$.ajax(this);
						return;
					}
					return;
				}
			});

			var obj1 = JSON.parse(data1);

			for (var j = 0; j < obj1.length; j++) {

				var userid = obj1[j].userid;
				var forecast = obj1[j].forecast;

				console.log('FID: ' + obj1[j].id);

				const con_score = await computeAllValuesExp(debateid, userid);


					if (con_score < 0 && forecast >= pForecast) {
						irrationalForecasts.push(" H " + forecast);
					} else if (con_score > 0 && forecast <= pForecast) {
						irrationalForecasts.push(" L " + forecast);
					} else if ((Math.abs(pForecast - forecast) / pForecast) > Math.abs(con_score)) {
						irrationalForecasts.push(" S " + forecast);
					}
				}

			console.log('ID: ' + debateid + '\n' + irrationalForecasts);

			irrationalForecasts = [];

}

/*
    This function was implemented for Arg&Forecast Experiment 1
*/
async function computeAllValuesExp(debateId, userId){

	var nodeList = await getNodes(debateId, userId);
	var edges = await getEdges(nodeList, debateId)

	for (var n in nodeList) {
		if(nodeList[n].type !== 'proposal') {
			var result = await SF2(nodeList[n], nodeList, edges);
			var nresult = Math.round(result * Math.pow(10, decimals)) / Math.pow(10, decimals);
			nodeList[n].computedValueDFQuad = nresult;
		}
	}

	const confidenceScore = await CS(nodeList);
	CONFIDENCE_SCORE = confidenceScore;
	await editConfidenceScore(debateId, confidenceScore, userId);
	return confidenceScore;
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function computeAllValues(){
	// await refreshNodeList(null);

	for (var n in nodeList) {



		if(nodeList[n].type !== 'proposal') {

			if (nodeList[n].baseValue === 'null') {
				if(nodeList[n].type === 'increase' || nodeList[n].type === 'decrease') {
					nodeList[n].baseValue = "0.5";
				} else {
					var alert = '<h3>ALL arguments must be voted on to proceed.</h3>';
					bootbox.alert(alert);
					return false;
				}
			}

			console.log(nodeList[n]);

			var result = await SF2(nodeList[n], null, null);
			var nresult = Math.round(result * Math.pow(10, decimals)) / Math.pow(10, decimals);
			nodeList[n].computedValueDFQuad = nresult;
		}
	}

	const confidenceScore = await CS(nodeList);
	console.log('CONFIDENCE SCORE: ' + confidenceScore);
	CONFIDENCE_SCORE = confidenceScore;
	await editConfidenceScore(thisDebateId, confidenceScore, null);
	return confidenceScore;
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

/*
    This function was significantly edited for Arg&Forecast.
*/
async function c(v0,va,vs){
        
        v0=parseFloat(v0);
        va=parseFloat(va);
        vs=parseFloat(vs);

	if (vs == va){
		return v0;
	}
	else {
		var res = v0+(0.5 + (vs-va)/(2*Math.abs(vs-va))-v0)*Math.abs(vs-va);
		//console.log("Res c= "+ res + " temp: "+temp);
		return res;
	}
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function f2(v1,v2) {
    v1 = parseFloat(v1);
    v2 = parseFloat(v2);
    
    return v1+v2-v1*v2;
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function F2(S) {
    if (S.length==0){
		return 0;
    }
    else if (S.length==1) {
        //console.log("S.length==1");
        return S[0];
    }
     // If last attacker/supporter = 0, shorten array
    else if (S[S.length-1]==0){
        S.pop();
        return await F2(S);
    }
    // If last attacker/supporter = 1, return 1
    else if (S[S.length-1]==1){
        return 1;
    }
    else if (S.length==2) {
        var res = await f2(S[0],S[1]);
        //console.log("S.length==2 S0="+S[0]+" S1="+S[1]+" res= " + res);
        return await f2(S[0],S[1]);
    }
    else{    
        
        //console.log("S.length>2 "+S.length);

        var vn = S.pop();
        //console.log("vn: "+ vn);
        
        var fat = await f2(await F2(S),vn);
        //console.log("fat: " + fat);
        return fat;
        
    }
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function localReplaceRs(nodes, Rs) {
	if (nodes !== null) {
		for (var i = 0; i < Rs.length; i++) {

			Rs[i] = nodes[Rs[i].id]
		}
	}
	return Rs;
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function localReplaceRa(nodes, Ra) {
	if (nodes !== null) {
		for (var i = 0; i < Ra.length; i++) {
			Ra[i] = nodes[Ra[i].id]
		}
	}
	return Ra;
}

/*
    This function was significantly edited for Arg&Forecast.
*/
async function SF2(a, nodes, edges){

	var Rs = await a.getSupporters(edges);
	var Ra = await a.getAttackers(edges);
	Rs = await localReplaceRs(nodes, Rs);
	Ra = await localReplaceRa(nodes, Ra);

	var FSseqSupp = [];

	for (var i = 0; i<Rs.length; i++){
		FSseqSupp.push(await SF2(Rs[i], nodes, edges));
	}

	var FSseqAtt = [];

	for (var i = 0; i<Ra.length; i++){
		FSseqAtt.push(await SF2(Ra[i], nodes, edges));
	}

	var va = await F2(FSseqAtt);
	var vs = await F2(FSseqSupp);
	var res = await c(a.baseValue, va, vs);
	return res;
        
}

