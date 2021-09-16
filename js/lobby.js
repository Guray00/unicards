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



function slideBackground() {
	var background = document.body;
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}



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

	setInterval(()=>{
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



		$.ajax({
			type: "POST",
			url: "../php/game/get_status.php",
			data: {id:id},
	
			
			// in caso di successo
			success: function (data) {	
				console.log(data);

				if (data == "1"){
					window.location.href='./game.php';
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