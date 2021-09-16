
function setFavourite(el, owner){
	let deck_id = el.value;

	if (el.checked){
		$.ajax({
			type: "POST",
			url: "../php/add_favourite.php",
			data: {deck: deck_id, owner:owner},
	
			// in caso di successo
			success: function (data) {
				// evitiamo un aggiornamento completo della pagina, carichiamo soltanto le carte
				$('#favourites-list').load("../pages/dashboard.php"  + ' #favourites-list');
				$('#your-deck-list') .load("../pages/dashboard.php"  + ' #your-deck-list');	
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {		
				alert(xhr.status);
				alert(thrownError);
			}
		})
	}

	else {
		$.ajax({
			type: "POST",
			url: "../php/remove_favourite.php",
			data: {deck: deck_id, owner:owner},
	
			// in caso di successo
			success: function (data) {	
				// evitiamo un aggiornamento completo della pagina, carichiamo soltanto le carte
				$('#favourites-list').load("../pages/dashboard.php"  + ' #favourites-list');
				$('#your-deck-list') .load("../pages/dashboard.php"  + ' #your-deck-list');
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		})
	}

	
}

function openMenu(id, user, name, color, mail){

	let preview = document.getElementById("menu-deck-preview");
	preview.innerText = name;
	preview.style.backgroundColor = color;

	document.getElementById("opacity").style.display = "block";
	document.getElementById("choose-mod").style.display = "block";


	document.getElementById("edit-btn").addEventListener("click", ()=> {
		edit_deck(id, user);
	})

	if (user != mail){
		document.getElementById("edit-btn").disabled = true;
	}

	
	// gestisco il pulsante per il singleplayer
	document.getElementById("sp").onclick = ()=> {
		closeMenu();
		

		$.ajax({
			type: "POST",
			url: "../php/game/create_lobby.php",
			data: {id:id, owner:user, mode:"0", status:"1"},
	
			
			// in caso di successo
			success: function (data) {	
				//alert(data); /* da attivare in caso di debug */

				if (data == "-1") {
					okbox({
						title: "Modalità non disponibile", 
						content:"Per poter avviare una partita devono essere presenti almeno 3 carte della modalità selezionata.",
			
						ok: function(){}
					});	
				}
				else {window.location.replace("../pages/game.php");}
					
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				postError();
				//alert(xhr.status);
				//alert(thrownError);
			}
		});
	};


	// gestisco il pulsante per il multiplayer
	document.getElementById("mp").onclick = ()=> {
		closeMenu();
		$.ajax({
			type: "POST",
			url: "../php/game/create_lobby.php",
			data: {id:id, owner:user, mode:"1", status:"0"},
	
			
			// in caso di successo
			success: function (data) {	
				//alert(data); /* da attivare in caso di debug */

				// controllo di aver un numero sufficiente di carte per poter giocare
				if (data == "-1") {
					okbox({
						title: "Modalità non disponibile", 
						content:"Per poter avviare una partita devono essere presenti almeno 3 carte della modalità selezionata.",
			
						ok: function(){}
					});	
					
				}

				else{window.location.replace("../pages/lobby.php?id="+data);}
					
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				postError();
				//alert(xhr.status);
				//alert(thrownError);
			}
		});
	};

}

function closeMenu(){
	document.getElementById("choose-mod").style.display = "none";
	document.getElementById("opacity").style.display    = "none";
}

function edit_deck(id, user){
	window.location.replace("../pages/deck_editor.php?id="+id+"&user="+user);
}