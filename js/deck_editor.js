
// evito di dare lo stesso id a carte differenti quando vengono aggiunte o rimosse
MAX_CARD = 1;


//------->oninvalid="alert('You must fill out the form!');" <--------

// elimina in automatico le caselle di testo vuote se quelle sotto sono riempite
function textAreaHandler(q, a, lq, la){
	// quando modifico una domanda, se è vuota e anche la risposta è vuota, cancello
	q.onchange = function() {
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
	console.log("chiamato");
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

	// aggiunge alla chiamata get il nome delle tab per risalire alla sezione
	document.getElementById("deck_form").onsubmit = function(){
		for(tab of document.getElementsByClassName("tab")){

			if (tab.nodeName == "LABEL"){
				tabs = document.createElement("input");
				tabs.value = tab.innerText;
				tabs.name = tab.getAttribute("for");

				document.getElementById("deck_form").appendChild(tabs);
			}
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
