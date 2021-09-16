

function slideBackground() {

	var background = document.body;
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}

//window.addEventListener('load',slideBackground,false);

function updateProgressBar(user, correct, wrong, skip){
	let total = correct + wrong + skip;

	let sc = (correct/total)*100;
	let sw = (wrong/total  )*100;
	let ss = (skip/total   )*100;

	document.getElementById(user).getElementsByClassName("true")[0].style.width  = sc+"%";
	document.getElementById(user).getElementsByClassName("false")[0].style.width = sw+"%";
	document.getElementById(user).getElementsByClassName("skip")[0].style.width  = ss+"%";
}


function updateSize(user, position){

	let size = 15;
	for (let i = 0; i < position; i++){
		size*=5/6;
	}

	document.getElementById(user).getElementsByClassName("progress-container")[0].style.height= size+"vh";
}


window.addEventListener('load', ()=>{
	setInterval(()=>{window.location = window.location; console.log("fatto");}, 5000);

});