
function checkValid(){

}

window.addEventListener('load',function(){

	// recupero il form
	let form = document.getElementById("signin-form");

	// text di conferma
	let psw_confirm = document.getElementById("psw-confirm");
	
	// text della password
	let psw = document.getElementById("psw");

	// button submit
	let btn = this.document.getElementsByTagName("button")[0];
	btn.disabled = true;
	
	// imposto che il form funziona solamente se le password sono inserite
    form.onsubmit = function(){

        if(psw_confirm.value != psw.value){
            return false;
		}
			
        else
            return true;

    }

	form.onclick = function (){

		if(!form.checkValidity()){
            btn.disabled = true;
        }
		else 
			btn.disabled = false;

	}
    

	
	

	psw_confirm.oninput = (event)=>{

		if (psw_confirm.value != psw.value){
			psw_confirm.setCustomValidity("Invalid field.");
		}

		else {
			psw_confirm.setCustomValidity("");
		}
	}

	psw.oninput = (event)=>{


		// controlla che siano inseriti dagli 8 ai 20 caratteri
		if (psw.value.length < 8 || psw.value.length > 20){
			psw.setCustomValidity("Invalid field.");
		}

		else {
			psw.setCustomValidity("");
		}

		
		if (psw.value != psw_confirm.value){
			psw_confirm.setCustomValidity("Invalid field.");
		}

		else {
			psw_confirm.setCustomValidity("");
		}
	}
});



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