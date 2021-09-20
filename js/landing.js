

function slideBackground() {

	var bg1 = document.getElementById("titolo");
	var bg4 = document.getElementById("anteprima");
	var bg2 = document.getElementById("flashcards");
	var bg5 = document.getElementById("tuo-mazzo");
	var bg3 = document.getElementById("amici");

	var x = 0;
	
	setInterval(function(){
		bg1.style.backgroundPosition = x + 'px ' + x+"px";
		bg2.style.backgroundPosition = x + 'px ' + x+"px";
		bg3.style.backgroundPosition = x + 'px ' + x+"px";
		bg4.style.backgroundPosition = (x*(-0.3)) + 'px ' +(x*(-0.3)) +"px";
		bg5.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}


window.addEventListener('load',slideBackground,false);