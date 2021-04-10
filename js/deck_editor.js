
// evito di dare lo stesso id a carte differenti quando vengono aggiunte o rimosse
MAX_CARD = 1;

function colorChange(el){
	document.getElementById("preview").style.backgroundColor = el.value;
}


function deckNameChange(el){
	el.value= el.value.trim().charAt(0).toUpperCase() + el.value.trim().slice(1);
	document.getElementById("lbl_preview").innerText= el.value;
}

function post_request_deck_maker(id, user){
	var unindexed_array = $("#deck_form").serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

	let public =  indexed_array["public"] != undefined ? indexed_array["public"] : "FALSE";

	let deck = {
		"id": id,
		"user": user,
		"name": indexed_array["name"],
		"school": indexed_array["school"],
		"color": indexed_array["color"],
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

	// carico nel resultset le domande per ogni sezione
	for (key in indexed_array){
		if(key.includes("question")){
			let section = "tab"+key.substr(0, key.indexOf("_")).toString();
			let pos = key.substr(key.lastIndexOf("_"), key.length -1).replace("_question", "");
			
			if(result[section] == null) result[section] = {};
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

	// rinomino le sezioni con i nomi reali
	for(tab of document.getElementsByClassName("tab")){
		if (tab.nodeName == "LABEL"){
			result[tab.innerText] = result[tab.getAttribute("for")];
			delete result[tab.getAttribute("for")];
		}
	}


	return result;
}

function postSuccess(){
	document.getElementById("error_msg").innerText = "Salvato con successo!";
	//document.getElementById("error_msg").style.color = "#4fc46e";

	setTimeout( ()=>{
		document.getElementById("error_msg").innerText = "";
	}, 5000);
}

function postError(){
	document.getElementById("error_msg").innerText = "Errore, controlla di aver messo un nome diverso rispetto ai mazzi già esistenti e aver compilato correttamente tutti i dati";
	//document.getElementById("error_msg").style.color = "#4fc46e";

	setTimeout( ()=>{
		document.getElementById("error_msg").innerText = "";
	}, 5000);
}

function submitHandler(id, user){	
	let result = post_request_cards_maker();
	let deck   = post_request_deck_maker(id, user);

	$.ajax({
		type: "POST",
		url: "../php/deck_updater.php",
		data: {cards:result, deck:deck},
		success: function (data) {	
			// ricarichiamo la pagina una volta creata
			postSuccess();
			if (data > 0){
				window.location.replace("../pages/deck_editor.php?id="+data);
			}
		},

		error: function (xhr, ajaxOptions, thrownError) {
			postError();
			//alert(xhr.status);
			//alert(thrownError);
		}
	});
}


// elimina in automatico le caselle di testo vuote se quelle sotto sono riempite
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


// gestisco la dinamicità degli elementi
window.addEventListener("load",function(){

	/*aggiungo gli handler per le textarea aggiunte via php in automatico*/
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


	// per rinominare le tab
	for (i of document.getElementsByClassName("section_name")){
		i.onchange = function(){
			tabRename(this);
		}
	}

	

	// gestisce il pulsante add-tab
	for (i of document.getElementsByClassName("add-tab")){
		i.addEventListener("click", function(){
			for (j of document.getElementsByClassName("tab-container")){
				if(j.children.length == 0){
				
					let cards_container = document.createElement("div");
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

					inp.onchange = function(){
						tabRename(this);
					}

					j.appendChild(cards_container);
					cards_container.appendChild(lbl);
					cards_container.appendChild(inp);

					addCard(lbl);

					//<button type="button" id="btn-add" onclick="addCard(this)">Aggiungi carta</button>
					let butn = document.createElement("button");
					butn.type="button";
					butn.id="btn-add";
					butn.innerText="Aggiungi carta";
					butn.onclick = function(){addCard(this);};
					cards_container.appendChild(butn);
				}
			}
		})
	}

})

function addCard(btn){

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
	number = (number)/2 + 1;

	

	let lq = document.createElement("label");
	lq.id  = section+"_lbl_question"+MAX_CARD;
	
	let la = document.createElement("label");
	la.id  = section+"_lbl_answer"+MAX_CARD;

	let q = document.createElement("textarea");
	q.id   = section+"_question"+MAX_CARD;
	q.name = section+"_question"+MAX_CARD;
	q.required = true;
	q.oninvalid = function(){onInvalidText();}


	let a  = document.createElement("textarea");
	a.id   = section+"_answer"+MAX_CARD;
	a.name = section+"_answer"+MAX_CARD;
	a.required = true;
	a.oninvalid = function(){onInvalidText();}


	lq.innerText = "Domanda "+ number;
	la.innerText = "Risposta "+number;

	btn.parentNode.appendChild(lq);
	btn.parentNode.appendChild(la);
	btn.parentNode.appendChild(q);
	btn.parentNode.appendChild(a);

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
