
function setFavourite(el, owner){
	let deck_id = el.value;

	if (el.checked){
		$.ajax({
			type: "POST",
			url: "../php/add_favourite.php",
			data: {deck: deck_id, owner:owner},
	
			// in caso di successo
			success: function (data) {	
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
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		})
	}

	// evitiamo un aggiornamento completo della pagina, carichiamo soltanto le carte
	$('#favourites-list').load("../pages/dashboard.php"  + ' #favourites-list');
	$('#your-deck-list') .load("../pages/dashboard.php"  + ' #your-deck-list');
}

function openMenu(id, user, name, color){
	let preview = document.getElementById("menu-deck-preview");
	preview.innerText = name;
	preview.style.backgroundColor = color;

	document.getElementById("opacity").style.display = "block";
	document.getElementById("choose-mod").style.display = "block";
	document.getElementById("edit-btn").addEventListener("click", ()=> {
		edit_deck(id, user);
	})
}

function closeMenu(){
	document.getElementById("choose-mod").style.display = "none";
	document.getElementById("opacity").style.display = "none";
}

function edit_deck(id, user){
	window.location.replace("../pages/deck_editor.php?id="+id+"&user="+user);
}