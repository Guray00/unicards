function slideBackground() {

	var background = document.getElementById("login_container");

	if (background == undefined)
		background = document.getElementById("signin-form");

	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}


function submit(){
	let psw1 = document.getElementById("psw").value;
	let psw2 = document.getElementById("psw-confirm").value;

	
}

	
window.addEventListener('load',slideBackground,false);