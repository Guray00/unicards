
// evito di dare lo stesso id a carte differenti quando vengono aggiunte o rimosse
MAX_CARD = 1;
REFRESH = false;
DEBUG = false;

// rimuove dal database il deck
function deleteDeck(id, user){


	alertbox({
		title: "Sei sicuro di cancellare il mazzo?",
		content: "L'eliminazione è irrimediabile, tutte le carte e le statistiche andranno perse.",
		
		yes: function(){
			let deck   = post_request_deck_maker(id, user);

			$.ajax({
				type: "POST",
				url: "../php/deck_delete.php",
				data: {deck:deck},
				success: function (data) {
					REFRESH=false;
					window.location.replace("../pages/dashboard.php");	
				},

				error: function (xhr, ajaxOptions, thrownError) {
					postError();
					alert(xhr.status);
					alert(thrownError);
				}
			});
		},

		no: function(){
			return false;
		}
	});


	
		
}

// rimuove una tab della schermo
function removeTab(el){
	// prendo l'id della tab da eliminare
	let  tab = el.parentNode.parentNode.parentNode.id;

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

	//console.log(JSON.stringify(indexed_array))

	//console.log(JSON.stringify(unindexed_array));

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
			//result[section][indexed_array[key]] = [pos];

			let type = key.replace("question", "type");


			result[section][indexed_array[key]] = [];
			result[section][indexed_array[key]][0] = indexed_array[type];
			result[section][indexed_array[key]][1] = [[pos, -1]];
		}
	}



	// inserisco le risposte per ogni domanda inserita precedentemente

	//console.log(JSON.stringify(result));
	for (key in indexed_array){
		
		if(key.includes("answer")){


			//1_answer3_A

			let section = "tab"+key.substr(0, key.indexOf("_")).toString();
			//let pos = key.substr(key.lastIndexOf("_"), key.length -1).replace("_answer", "");

			let pos = key.replace(section.replace("tab", "") + "_answer", "");
			let letter = pos.substr( (pos.indexOf("_") + 1), pos.length-1);

			//pos = pos.indexOf("_") != -1 ? pos.substr(0, pos.indexOf("_")) : pos;
			pos = pos.replace("_", "").replace(letter, "");

			//console.log(letter=="");
			//console.log("continuo");

			//console.log(JSON.stringify(result[section]));
			for (k in result[section]){
				
				//alert(result[section][k][1][0][0]);
				if (result[section][k][1][0][0] == pos){
					//console.log("k vale: "+ k);
					//console.log(JSON.stringify(result));
					//console.log(indexed_array[key]);

					//TODO
					//console.log(key);
					result[section][k][1].push([indexed_array[key], indexed_array[key.replace("answer", "correct")]]);
					
					//result[section][k] = indexed_array[key];
					
				}
			}
		}
	}

	//console.log(JSON.stringify(result));
	//console.log("\n\n=====\n\n");

	// per ogni tab del risultato
	for (key in result){

		// per ogni domanda della tab
		for (x in result[key]){
			// rimuovo il primo elemento del risultato, era l'identificativo della domanda
			result[key][x][1].splice(0,1);

		}
	}

	//console.log(JSON.stringify(result));


	// le sezioni sono state caricate con i loro identificativi, adesso
	// li sostituisco inserendovi i nomi reali
	for(tab of document.getElementsByClassName("tab")){
		if (tab.nodeName == "LABEL"){
			// aggiungo la nuova key con i valori precedenti e rimuovo la vecchia
			result[tab.innerText] = result[tab.getAttribute("for")];
			delete result[tab.getAttribute("for")];
		}
	}

		console.log(JSON.stringify(result));
	return result;
}

// richiamato in caso di successo della richiesta post
function postSuccess(){
	//document.getElementById("error_msg").innerText = "Salvato con successo!";
	//document.getElementById("error_msg").style.color = "#4fc46e";

	dialogbox({title: "Salvataggio avvenuto", content:"Complimenti! Le tue modifiche sono andate a buon fine."});

	setTimeout( ()=>{
		//document.getElementById("error_msg").innerText = "";
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
	
	alert(JSON.stringify(deck));

	// eseguo la chiamata ajax passandovi le informazioni di sopra
	$.ajax({
		type: "POST",
		url: "../php/deck_updater.php",
		data: {cards:cards, deck:deck},

		
		// in caso di successo
		success: function (data) {	
			alert(data); /* da attivare in caso di debug */

			// ricarichiamo la pagina una volta creata
			if (data >= 0){
				REFRESH = false;
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
			alert(xhr.status);
			alert(thrownError);
		}
	});
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

// caso estremo, se non è già gestito chiediamo se siamo sicuri di voler uscire
window.onbeforeunload = function(event){
	if(REFRESH) return confirm("Confirm refresh");
};

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
			number = number.indexOf("_") != -1 ? number.substr(0, number.lastIndexOf("_")) : number;

			textAreaHandler2(i.parentNode);
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
					
					let inp 		= document.createElement("input");
					inp.className 	= "section_name";
					inp.id 			= MAX_TAB + "_section_name";
					inp.type 		= "text";
					inp.required 	= true;
					inp.oninvalid 	= function(){onInvalidText();}


					// mi assicuro che il nome sia un nome non ancora utilizzato
					let t = MAX_TAB;
					while (tabNameCheck("Untitled"+t)){t++;}
					
					inp.value="Untitled"+t;
					//rinomino per essere sicuro che sia corretto.
					document.getElementById(MAX_TAB+"_section").innerText = inp.value; 

					inp.onchange = function(){tabRename(this);}

					j.appendChild(cards_container);

					/*aggiunto per fix*/
					let section_div = document.createElement("div");
					section_div.className="section-info-div";
					section_div.id = MAX_TAB+"_section-info";
					section_div.appendChild(lbl);
					section_div.appendChild(inp);

					cards_container.appendChild(section_div);

					// creo il div che contiene i bottoni
					let buttons_div = document.createElement("div");
					buttons_div.className="buttons-last-div";
					buttons_div.id = MAX_TAB+"buttons-div";

					let btn_remove_tab = document.createElement("button");
					btn_remove_tab.type="button";
					btn_remove_tab.id="btn-remove-tab";
					btn_remove_tab.className="btn-remove";
					document.getElementById("btn-remove-tab").style.display = "block";
					btn_remove_tab.onclick = function(){removeTab(this);};


					let btn_add = document.createElement("button");
					btn_add.innerText="Aggiungi carta";
					btn_add.type="button";
					btn_add.id="btn-add";
					btn_add.onclick = function(){addCard(this, 1);};

					let btn_radio = document.createElement("button");
					btn_radio.type="button";
					btn_radio.id="radio-add";
					btn_radio.innerText="Aggiungi scelta multipla";
					btn_radio.onclick = function(){addCard(this, 4);};

					buttons_div.appendChild(btn_remove_tab);
					buttons_div.appendChild(btn_radio);
					buttons_div.appendChild(btn_add);
					cards_container.appendChild(buttons_div);

					addCard(btn_add, 1);
				}
			}
		})
	}

	for (let x of this.document.getElementsByTagName("textarea")){
		x.addEventListener("input", ()=>{REFRESH=true;});
	}

	for (let x of this.document.getElementsByTagName("input")){
		x.addEventListener("input", ()=>{REFRESH=true;});
	}

})


/* serve per gestire la pressione del toggle radio-check*/
function ctr_radio(obj){
	obj.className = "ctr-radio-selected";

	for (x of obj.parentNode.parentNode.getElementsByClassName("question-type")){
		x.value="1";
	}

	for (x of obj.parentNode.getElementsByClassName("ctr-check-selected")){
		x.className="ctr-check";
	}
	
	let true_list = [];
	for (let x of obj.parentNode.parentNode.getElementsByClassName("set-true-selected")){
		true_list.push(x);
	}

	for (y of true_list){
		y.className = "set-true";
		y.value="0";
	}

	for (x of obj.parentNode.parentNode.getElementsByClassName("set-true")){
		x.style.borderRadius = "50pt";
	}

}

function ctr_check(obj){
	obj.className = "ctr-check-selected";


	for (x of obj.parentNode.parentNode.getElementsByClassName("question-type")){
		x.value="2";
		//alert("aggiornato tipo");	
	}

	for (x of obj.parentNode.getElementsByClassName("ctr-radio-selected")){
		x.className="ctr-radio";
	}

	let true_list = [];
	for (let x of obj.parentNode.parentNode.getElementsByClassName("set-true-selected")){
		true_list.push(x);
	}

	for (y of true_list){
		y.className = "set-true";
		y.value="0";
	}

	for (x of obj.parentNode.parentNode.getElementsByClassName("set-true")){
		x.style.borderRadius = "2pt";
	}
}


function set_answer_true(obj){

	if(obj.className=="set-true-selected"){
		obj.className="set-true";
		obj.value="0";
		return;
	}

	if(obj.parentNode.getElementsByClassName("ctr-radio-selected").length == 1){
		for (x of obj.parentNode.getElementsByClassName("set-true-selected")){
			x.className="set-true";
			x.value="0";
		}

		obj.className="set-true-selected";
		obj.value="1";
	}

	else {
		obj.className = "set-true-selected";
		obj.value="1";
	}
}



// richiamato quando si richiede di aggiungere una nuova carta
function addCard(btn, d){

	// number serve per sapere il numero di domande già presenti
	
	for (i of btn.parentNode.parentNode.parentNode.getElementsByTagName("textarea")){
		// se la casella è vuota allora non aggiunge
		if(i.value === ""){
			return;
		}
	}

	let number = 0;
	for (i of btn.parentNode.parentNode.parentNode.getElementsByClassName("card-div")){
		number++;
	}

	for (i of btn.parentNode.parentNode.parentNode.getElementsByClassName("multichoice-4-div")){
		number++;
	}
	number++;

	section = btn.parentNode.parentNode.parentNode.id.replace("tab", "");
	MAX_CARD++;
	

	// creo la label per la domanda
	let lq = document.createElement("label");
	lq.id  = section+"_lbl_question"+MAX_CARD;
	lq.innerText = "Domanda "+ number;

	// creo la textarea per la domanda
	let q = document.createElement("textarea");
	q.id   = section+"_question"+MAX_CARD;
	q.name = section+"_question"+MAX_CARD;
	q.required = true;
	q.oninvalid = function(){onInvalidText();}

	//<input id='{$question_type}' class='question-type' name='{$question_type}' value='{$c["type"]}' style='display:none;' form='deck_form'/>



	if (d == 1){

		// creo la label per la risposta
		let la = document.createElement("label");
		la.id  = section+"_lbl_answer"+MAX_CARD;
		la.innerText = "Risposta "+number;

		// creo la textarea per la risposta
		let a  = document.createElement("textarea");
		a.id   = section+"_answer"+MAX_CARD;
		a.name = section+"_answer"+MAX_CARD;
		a.required = true;
		a.oninvalid = function(){onInvalidText();}

		let fake_input = document.createElement("input");
		fake_input.id = section+"_type"+MAX_CARD;
		fake_input.name = section+"_type"+MAX_CARD;
		fake_input.value = 0;
		fake_input.style.display= "none";
		fake_input.form = "deck_form";

		let fake_correct = document.createElement("input");
		fake_correct.id = section+"_correct"+MAX_CARD;
		fake_correct.name = section+"_correct"+MAX_CARD;
		fake_correct.value = 1;
		fake_correct.style.display= "none";
		fake_correct.form = "deck_form";

		// parte aggiunta per il fix
		let container = document.createElement("div");
		container.className = "card-div";
		container.id = "card-div-"+MAX_CARD;


		container.appendChild(lq);
		container.appendChild(la);
		container.appendChild(q);
		container.appendChild(a);
		container.appendChild(fake_input);
		container.appendChild(fake_correct);


		/*<input id='{$correct}' 
		name='{$correct}' 
		value='1' 
		style='display:none;'  
		form='deck_form' />
		*/

		

		//textAreaHandler(q, a, lq, la);
		textAreaHandler2(container);
		btn.parentNode.parentNode.insertBefore(container, btn.parentNode); // serve per fare l'inserimento prima del bottone
	}
	
	else if (d == 4){

		let container = document.createElement("div");
		container.className = "multichoice-4-div";
		container.id = "card-div-"+MAX_CARD;

		let fake_input = document.createElement("input");
		fake_input.id = section+"_type"+MAX_CARD;
		fake_input.name = section+"_type"+MAX_CARD;
		fake_input.className = "question-type";
		fake_input.value = "1";
		fake_input.style.display= "none";
		fake_input.form = "deck_form";

		container.appendChild(fake_input);

		let letters = ["A", "B", "C", "D"];

		//q.setAttribute("grid-column-start", "span 2");

		let sw = document.createElement("div");
		sw.className = "check-to-radio";
		sw.title = "Clicca sul cerchio per scegliere solo una risposta giusta,\nsul quadrato per più risposte giuste";

		container.appendChild(lq);
		container.appendChild(q);
		container.appendChild(sw);

		let radio = document.createElement("input");
		radio.type="button";
		radio.className = "ctr-radio-selected";

		let check = document.createElement("input");
		check.type="button";
		check.className = "ctr-check";

		sw.appendChild(radio);
		sw.appendChild(check);	

		check.onclick = function(){
			ctr_check(this);
		}

		radio.onclick = function(){
			ctr_radio(this);
		}


		let i;
		for (i=1; i <= 4; i++){
			// creo la label per la risposta
			let la = document.createElement("label");
			la.id  = section+"_lbl_answer"+MAX_CARD+"_"+letters[i-1];
			la.innerText = letters[i-1];

			// creo la textarea per la risposta
			let a  = document.createElement("textarea");
			a.id   = section+"_answer"+MAX_CARD+"_"+letters[i-1];
			a.name = section+"_answer"+MAX_CARD+"_"+letters[i-1];
			a.required = true;
			a.oninvalid = function(){onInvalidText();}

			let set  = document.createElement("input");
			set.type = "label";
			set.className = "set-true";
			set.name = section+"_correct"+MAX_CARD+"_"+letters[i-1];
			set.id   = section+"_correct"+MAX_CARD+"_"+letters[i-1];
			set.value = "0";
			set.form = "deck_form";

			set.onclick = function(){set_answer_true(this);}

			container.appendChild(la);
			container.appendChild(a);
			container.appendChild(set);
			textAreaHandler2(container);

			btn.parentNode.parentNode.insertBefore(container, btn.parentNode); // serve per fare l'inserimento prima del bottone
		}
	}
		

	

	// aggiungo alla tab di riferimento gli elementi generati
	/*btn.parentNode.appendChild(lq);
	btn.parentNode.appendChild(la);
	btn.parentNode.appendChild(q);
	btn.parentNode.appendChild(a);*/

	//btn.parentNode.appendChildBefore(container);
	

	// gesisco la variazione del testo nelle textarea per renderle dinamiche
}

// elimina in automatico le caselle di testo vuote se quelle sotto sono riempite,
// devono essere passati come riferimenti le label e le question e answer associate
// in modo da eliminarle tutte insieme
function textAreaHandler2(container){
	// quando modifico una domanda, se è vuota e anche la risposta è vuota, cancello
	for (x of container.getElementsByTagName("textarea")){
		
		x.onchange = function() {
			//rimuovo gli spazi bianchi prima e dopo
			this.value = this.value.trim();
	
			if(this.value == "" && (container.parentNode.getElementsByClassName("card-div").length + container.parentNode.getElementsByClassName("multichoice-4-div").length) > 1){
				
				let remove = true;
				for (y of container.getElementsByTagName("textarea")){
					if (y.value != ""){
						remove = false;
					}
				}
				
				if (remove){
					let p = container.parentNode;
					p.removeChild(container);
					updateLabels(p);
				}
			}
		}
	}
}


/*sfruttiamo l'arrotondamento per rinominare
correttamente le etichette delle domande
e delle risposte*/
function updateLabels(p){

	// sto passando il padre del container
	let n = 1;
	for (i of p.children){

		// ignoro nel conteggio le cose che non sono carte
		if (i.className=="section-info-div" || i.className=="buttons-last-div"){continue;}

		// se è una carta con risposta singola
		if(i.className=="card-div" || i.className=="multichoice-4-div"){
			for (j of i.getElementsByTagName("label")){
				if(j.innerText.includes("Domanda")) 		j.innerText = "Domanda " + n;
				else if(j.innerText.includes("Risposta")) 	j.innerText = "Risposta " + n;
			}
		}

		n++;
	}
}

// mostra l'alert alternativo per tornare indietro
function goBack(){

	if(REFRESH)
		alertbox({
			title: "Vuoi annullare le modifiche?",
			content: "Tornando indietro tutte le modifiche effettuate non saranno salvate.",
			
			yes: function(){
				//alert("yes");
				REFRESH=false;
				window.location.href='./dashboard.php';
			},

			no: function(){
				//alert("not");
			}
		});

	else window.location.href='./dashboard.php';
}


/* serve a rendere in movimento il background 
	ispirazione: https://stackoverflow.com/questions/34364330/make-background-image-of-div-move-continuously
*/
function slideBackground() {

	var background = document.body;
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}
	
window.addEventListener('load',slideBackground,false);
