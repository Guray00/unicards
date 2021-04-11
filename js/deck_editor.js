
// evito di dare lo stesso id a carte differenti quando vengono aggiunte o rimosse
MAX_CARD = 1;

// rimuove dal database il deck
function deleteDeck(id, user){
	if (confirm('Sei sicuro di voler eliminare il deck')) {
		let deck   = post_request_deck_maker(id, user);

		$.ajax({
			type: "POST",
			url: "../php/deck_delete.php",
			data: {deck:deck},
			success: function (data) {
				window.location.replace("../pages/dashboard.php");	
			},

			error: function (xhr, ajaxOptions, thrownError) {
				postError();
			}
		});
	} 
}

// rimuove una tab della schermo
function removeTab(el){
	// prendo l'id della tab da eliminare
	let  tab = el.parentNode.parentNode.id;

	// scorro le tab carcando quella da eliminare
	for (i of document.getElementsByClassName("tab")){
		if(i.getAttribute("for") == tab){
			i.parentNode.removeChild(document.getElementById(tab));
			i.parentNode.removeChild(i);
		}	
	}

	// scorro i contenitori cercando quello da eliminare
	for (i of document.getElementsByClassName("tab-container")){
		if (i.id == tab){
			i.parentNode.removeChild(i);
		}
	}

	// se ho solamente una tab impedisco che vengano rimosse
	if (document.getElementsByClassName("tab-container").length >= 2){
		document.getElementById("btn-remove-tab").style.display = "block";
	}

	else 
		document.getElementById("btn-remove-tab").style.display = "none";

	// una volta eliminate una tab faccio in modo di selezionare l'ultima
	let activate = null;
	for (i of document.getElementsByClassName("tab")){
		if (i.nodeName == "INPUT"){
			activate = i;
		}
	}
	activate.click();
}

// aggiorna il colore
function colorChange(el){
	document.getElementById("preview").style.backgroundColor = el.value;
}


// quando finisce la modifica eseguo il trim del contenuto del nome
function deckNameChange(el){
	el.value= el.value.trim().charAt(0).toUpperCase() + el.value.trim().slice(1);
	document.getElementById("lbl_preview").innerText= el.value;
}

// durante la modifica aggiorno in tempo reale l'anteprima
function deckNameInput(el){
	el.value= el.value.charAt(0).toUpperCase() + el.value.slice(1);
	document.getElementById("lbl_preview").innerText= el.value;
}

// creo l'oggetto json per la post request del mazzo/deck
function post_request_deck_maker(id, user){
	var unindexed_array = $("#deck_form").serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

	// recupero la preferenza sul pubblico
	let public =  indexed_array["public"] != undefined ? indexed_array["public"].toUpperCase().trim() : "FALSE";

	// creo l'oggetto contenente tutte le informazioni
	let deck = {
		"id": id,
		"user": user,
		"name": indexed_array["name"].trim(),
		"school": indexed_array["school"].trim(),
		"color": indexed_array["color"].trim(),
		"public": public
	}

	return deck;
}

function post_request_cards_maker(){
	var unindexed_array = $("#deck_form").serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });


	let result = {};

	// carico nel resultset le domande per ogni sezione eseguendo
	// il parsing dei nomi secondo il formato: [section]_[question][number_id]
	for (key in indexed_array){
		if(key.includes("question")){
			// recupero la sezione/tab
			let section = "tab"+key.substr(0, key.indexOf("_")).toString();

			// recupero l'identificativo della domanda
			let pos = key.substr(key.lastIndexOf("_"), key.length -1).replace("_question", "");
			
			// se la sezione non è stata ancora creata, la creo
			if(result[section] == null) result[section] = {};

			// inserisco la domanda
			result[section][indexed_array[key]] = pos;
		}
	}

	// inserisco le risposte per ogni domanda inserita precedentemente
	for (key in indexed_array){
		if(key.includes("answer")){
			let section = "tab"+key.substr(0, key.indexOf("_")).toString();
			let pos = key.substr(key.lastIndexOf("_"), key.length -1).replace("_answer", "");

			for (k in result[section]){
				if (result[section][k] == pos){
					result[section][k] = indexed_array[key];
				}
			}
		}
	}

	// le sezioni sono state caricate con i loro identificativi, adesso
	// li sostituisco inserendovi i nomi reali
	for(tab of document.getElementsByClassName("tab")){
		if (tab.nodeName == "LABEL"){
			// aggiungo la nuova key con i valori precedenti e rimuovo la vecchia
			result[tab.innerText] = result[tab.getAttribute("for")];
			delete result[tab.getAttribute("for")];
		}
	}
	return result;
}

// richiamato in caso di successo della richiesta post
function postSuccess(){
	document.getElementById("error_msg").innerText = "Salvato con successo!";
	//document.getElementById("error_msg").style.color = "#4fc46e";

	setTimeout( ()=>{
		document.getElementById("error_msg").innerText = "";
	}, 5000);
}

// richiamato in caso di errore della richiesta post
function postError(){
	document.getElementById("error_msg").innerText = "Errore, controlla di aver messo un nome diverso rispetto ai mazzi già esistenti e aver compilato correttamente tutti i dati";
	//document.getElementById("error_msg").style.color = "#4fc46e";

	setTimeout( ()=>{
		document.getElementById("error_msg").innerText = "";
	}, 5000);
}

// richiamato nel momento del submit della form
function submitHandler(id, user){	

	// ricavo le informazioni necessarie per la chiamata post
	let cards  = post_request_cards_maker();
	let deck   = post_request_deck_maker(id, user);

	// eseguo la chiamata ajax passandovi le informazioni di sopra
	$.ajax({
		type: "POST",
		url: "../php/deck_updater.php",
		data: {cards:cards, deck:deck},

		// in caso di successo
		success: function (data) {	
			//alert(data);
			// ricarichiamo la pagina una volta creata
			if (data >= 0){
				postSuccess();
				if (data > 0){
					// nel caso in cui è restituito un valore intero maggiore 
					// di zero significa che è stato eseguito un inserimento
					// ed è necessario ricaricare la pagina passandovi come 
					// parametro get l'id del mazzo inserito
					window.location.replace("../pages/deck_editor.php?id="+data);
				}
			}

			else postError();
		},

		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
			postError();
			//alert(xhr.status);
			//alert(thrownError);
		}
	});
}


// elimina in automatico le caselle di testo vuote se quelle sotto sono riempite,
// devono essere passati come riferimenti le label e le question e answer associate
// in modo da eliminarle tutte insieme
function textAreaHandler(q, a, lq, la){
	// quando modifico una domanda, se è vuota e anche la risposta è vuota, cancello
	q.onchange = function() {

		//rimuovo gli spazi bianchi prima e dopo
		this.value = this.value.trim();

		if(this.value == "" && a.value == "" && a.parentNode.getElementsByTagName("textarea").length > 2){
			let p = this.parentNode;
			p.removeChild(this);
			p.removeChild(a);
			p.removeChild(lq);
			p.removeChild(la);
			updateLabels(p);
		}
	}

	// quando modifico una domanda, se è vuota e anche la risposta è vuota, cancello
	a.onchange = function() {

		//rimuovo gli spazi bianchi prima e dopo
		this.value = this.value.trim();

		if(this.value == "" && q.value == ""  && q.parentNode.getElementsByTagName("textarea").length > 2){
			let p = this.parentNode;
			p.removeChild(this);
			p.removeChild(q);
			p.removeChild(lq);
			p.removeChild(la);
			updateLabels(p);
		}
	}
}

// richiamato nel caso di testo invalido
function onInvalidText(){
	document.getElementById("error_msg").innerText = "Qualcosa è andato storto, controlla se hai compilato tutti i campi!";
	setTimeout( ()=>{
		document.getElementById("error_msg").innerText = "";
	}, 5000);
}


// cerca una string tra i nomi delle tag, restituisce true se viene trovata
function tabNameCheck(value){
	let found = false;
	for (i of document.getElementsByClassName("tab")){
		// per motivi di DB, la label è case insensitive, trim rimuove gli spazi prima e dopo
		if(i.nodeName == "LABEL" && i.innerText.toUpperCase().trim() == value.toUpperCase().trim()){
			found = true;
		}
	}
	return found;
}


// si assicura che il nome delle tag sia corretto
function tabRename(element){
	number = element.id.substr(0, element.id.indexOf("_"));
	let found = tabNameCheck(element.value);

	// due tab non possono avere lo stesso nome o essere vuoto
	if (!found && element.value.trim() != "") document.getElementById(number+"_section").innerText = element.value;
	else element.value = document.getElementById(number+"_section").innerText;
}


// gestisco la dinamicità degli elementi inseriti via php nella pagina
window.addEventListener("load",function(){

	// se è presente solo una tab non consente la rimozione della stessa
	if (document.getElementsByClassName("tab-container").length < 2){
		document.getElementById("btn-remove-tab").style.display = "none";
	}


	// aggiungo gli handler per le textarea aggiunte via php in automatico
	for(i of document.getElementsByTagName("textarea")){

		// se l'elemento che sto scorrendo è una domanda o una risposta, devo impostare l'handler
		if(i.id.includes("question") || i.id.includes("answer")){

			// ricavo la tab alla quale faccio riferimento
			let section = i.id.substr(0, i.id.indexOf("_"));

			// ricavo il numero di domanda della tab facente riferimento
			let number = i.id.replace(section+"_question", "").replace(section+"_answer", "");

			let q = document.getElementById(section+"_question"+number);
			let a = document.getElementById(section+"_answer"+number);

			let lq = document.getElementById(section+"_lbl_question"+number);
			let la = document.getElementById(section+"_lbl_answer"+number);
			textAreaHandler(q, a, lq, la);
			MAX_CARD = number;
		}	
	}


	// per rinominare le tab in base al contenuto del form
	for (i of document.getElementsByClassName("section_name")){
		i.onchange = function(){
			tabRename(this);
		}
	}

	

	// gestisce il pulsante add-tab
	for (i of document.getElementsByClassName("add-tab")){
		i.addEventListener("click", function(){
			for (j of document.getElementsByClassName("tab-container")){

				// cerco il container vuoto
				if(j.children.length == 0){
				
					let cards_container       = document.createElement("div");
					cards_container.className = "cards-container";

					let lbl = document.createElement("label");
					lbl.id  = MAX_TAB+"_lbl_section";
					lbl.innerText="Nome sezione:";
					
					let inp = document.createElement("input");
					inp.className = "section_name";
					inp.id = MAX_TAB + "_section_name";
					inp.type = "text";
					inp.required = true;
					inp.oninvalid = function(){onInvalidText();}


					// mi assicuro che il nome sia un nome non ancora utilizzato
					let t = MAX_TAB;
					while (tabNameCheck("Untitled"+t)){t++;}

					inp.value="Untitled"+t;
					//rinomino per essere sicuro che sia corretto.
					document.getElementById(MAX_TAB+"_section").innerText = inp.value; 

					inp.onchange = function(){tabRename(this);}

					j.appendChild(cards_container);
					cards_container.appendChild(lbl);
					cards_container.appendChild(inp);

					addCard(lbl);


					let btn_remove_tab = document.createElement("button");
					btn_remove_tab.type="button";
					btn_remove_tab.id="btn-remove-tab";
					btn_remove_tab.className="btn-remove";
					document.getElementById("btn-remove-tab").style.display = "block";
					btn_remove_tab.onclick = function(){removeTab(this);};
					cards_container.appendChild(btn_remove_tab);

					let btn_add = document.createElement("button");
					btn_add.type="button";
					btn_add.id="btn-add";
					btn_add.innerText="Aggiungi carta";
					btn_add.onclick = function(){addCard(this);};
					cards_container.appendChild(btn_add);
				}
			}
		})
	}

})


// richiamato quando si richiede di aggiungere una nuova carta
function addCard(btn){

	// number serve per sapere il numero di domande già presenti
	let number = 0;
	for (i of btn.parentNode.getElementsByTagName("textarea")){
		// se la casella è vuota allora non aggiunge
		if(i.value === ""){
			return;
		}

		number++;
	}

	section = btn.parentNode.parentNode.id.replace("tab", "");
	
	MAX_CARD++;

	// number conta sia domande che risposte, perciò sarà il doppio
	number = (number)/2 + 1;

	
	// creo la label per la domanda
	let lq = document.createElement("label");
	lq.id  = section+"_lbl_question"+MAX_CARD;
	
	// creo la label per la risposta
	let la = document.createElement("label");
	la.id  = section+"_lbl_answer"+MAX_CARD;

	// creo la textarea per la domanda
	let q = document.createElement("textarea");
	q.id   = section+"_question"+MAX_CARD;
	q.name = section+"_question"+MAX_CARD;
	q.required = true;
	q.oninvalid = function(){onInvalidText();}


	// creo la textarea per la risposta
	let a  = document.createElement("textarea");
	a.id   = section+"_answer"+MAX_CARD;
	a.name = section+"_answer"+MAX_CARD;
	a.required = true;
	a.oninvalid = function(){onInvalidText();}

	// inserisco i valori all'interno delle label
	lq.innerText = "Domanda "+ number;
	la.innerText = "Risposta "+number;

	// aggiungo alla tab di riferimento gli elementi generati
	btn.parentNode.appendChild(lq);
	btn.parentNode.appendChild(la);
	btn.parentNode.appendChild(q);
	btn.parentNode.appendChild(a);

	// gesisco la variazione del testo nelle textarea per renderle dinamiche
	textAreaHandler(q, a, lq, la);
}

/*sfruttiamo l'arrotondamento per rinominare
correttamente le etichette delle domande
e delle risposte*/
function updateLabels(p){
	q = 0; 
	for (i of p.getElementsByTagName("label")){
		if(i.innerText.includes("Domanda")) i.innerText = "Domanda " + Math.round(q);
		else if(i.innerText.includes("Risposta")) i.innerText = "Risposta " +Math.round(q);
		q+=0.5;
	}
}
