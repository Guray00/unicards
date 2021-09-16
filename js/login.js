function slideBackground() {

	var background = document.getElementById("login_container");
	alert(JSON.stringify(background));
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}
	
window.addEventListener('load',slideBackground,false);