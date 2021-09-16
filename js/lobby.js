

function copyUrl(){
	var copyText = document.getElementById("text-url");

	/* Select the text field */
	//copyText.select();
	//copyText.setSelectionRange(0, 99999); /* For mobile devices */

	/* Copy the text inside the text field */
  	navigator.clipboard.writeText(copyText.innerText);

	  alert("Testo copiato!");
}


// funzione che si occupa di avviare il match alla pressione del pulsante start
function startMatch(){

	let id = document.body.id;

	$.ajax({
		type: "POST",
		url: "../php/game/start_match.php",
		data: {id:id},

		
		// in caso di successo
		success: function (data) {
			//alert(data);
		},


		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
			//alert(xhr.status);
			//alert(thrownError);
		}
	});
}



// viene notifica all'uscita dell'utente dalla pagina al database
window.onbeforeunload = function(event) {
	let id = document.body.id;

	$.ajax({
		type: "POST",
		url: "../php/game/quit_game.php",
		data: {id:id},

		
		// in caso di successo
		success: function (data) {},


		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {}
	});
}


// rende in movimento il background
function slideBackground() {
	var background = document.body;
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}


// recupera le informazioni sui giocatori
function updatePlayers(data){
	let users = document.getElementsByClassName("user");

				
	// rimuove gli utenti che si sono scollegati
	for (let x of users){
		let found = false;

		for (let y of data){
			if (y["id"] == x.id ) found = true;
		}

		if (!found){
			document.getElementById(x.id).remove();
		}
	}

	// aggiorna gli utenti
	for (let x of data){

		let found = false;
		for (let y of users){
			if (x["id"] == y.id){ found = true;}
		}

		if (!found){
			let z = document.createElement("div");
			z.innerHTML = `<div class = 'user content-box' id='`+x.id+`'>
							<img src='../assets/users/default.png'/>
							<p>`+x["username"]+`</p>
						</div>`;

			document.getElementById("start").parentNode.insertBefore(z, document.getElementById("start"));
			
		}
	}
}
	
window.addEventListener('load',slideBackground,false);



window.addEventListener('load', ()=>{
	let id = document.body.id;

	// recupera lo stato dei giocatori
	let a = setInterval(()=>{
		$.ajax({
			type: "POST",
			url: "../php/game/get_players.php",
			data: {id:id},
	
			
			// in caso di successo
			success: function (data) {	
				data = JSON.parse(data);
				updatePlayers(data);
			},
	
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(xhr.status);
				//alert(thrownError);
			}
		});


		// recupera lo stato della partita
		$.ajax({
			type: "POST",
			url: "../php/game/get_status.php",
			data: {id:id},
	
			
			// in caso di successo
			success: function (data) {	

				// controlla se la partita è iniziata, nel caso reindirizza alla schermata di gioco
				if (data == "1"){
					window.location.href='./game.php';
				}

				// se lo stato è null, allora il master si è disconnesso e la partita viene conclusa
				else if (data == ""){

					// elimino l'intervallo per evitare di generare messaggi infiniti
					clearInterval(a);
					okbox({
						title: "Master disconnesso dalla partita", 
						content:"Il master della lobby si è disconnesso, la partita è stata annullata e verrai reindirizzato alla dashboard.",
			
						ok: function(){window.location.href='./dashboard.php';}
					});	
				}
			},
	
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				//alert(xhr.status);
				//alert(thrownError);
			}
		});

	}, 1000);
});