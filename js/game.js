



// viene utilizzata per segnare le risposte giuste nelle scelte multiple
function chooseAnswer(obj){

	if (obj.parentNode.getElementsByClassName("answer-content-true").length > 0 || obj.parentNode.getElementsByClassName("answer-content-false").length > 0 || obj.parentNode.getElementsByClassName("answer-content-wrong").length > 0){
		return;
	}

	if (obj.className.indexOf("selected") == -1){

		let type = obj.parentNode.getElementsByClassName("warning").length;

		if (type == 0){
			for (let x of obj.parentNode.getElementsByClassName("answer-content-selected")) {
				x.className = "content-box answer-content";
			}
		}
			
		obj.className = "content-box answer-content-selected";
	}

	else { obj.className = "content-box answer-content";}	
}


// mostra la risposta per le domande singole
function spShowAnswer(obj, answer){

	// mostra il pannello a destra per selezionare se la domanda è corretta o meno
	document.getElementsByClassName("card-enabled")[0].getElementsByClassName("single-right")[0].style.display = "block";

	obj.className = obj.className.replace("play", "").replace("next", "") + " play next";


	/*var press = document.createElement("div");
	press.className="suggestion press-this-true";
	document.getElementsByClassName("card-enabled")[0].appendChild(press);*/

	let parent = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	
	let h1 = parent.getElementsByTagName("h1")[0];
	let p  = parent.getElementsByTagName("p")[0];

	// aggiorna il titolo
	h1.innerText = h1.innerText.replace("Domanda", "Risposta");
	
	// aggiorna il contenuto della domanda con la risposta
	p.innerText = answer;	

	// se si ripreme sul pulsante, scorre alla prossima domanda
	obj.onclick = ( ()=>{nextQuestion(0);});
}

// aggiorna il navigatore delle pagine
function updateNavigation(id, type){
	for(let x of document.getElementsByClassName("navigator")){
		for (let y of x.getElementsByClassName("navigator-item")){
			if(y.id == ("navigator"+(id+1))){
				y.className = y.className.replace(type, "") + (" "+type);
			}
		}
	}
}

// imposta la risposta singola come corretta
function setSpCorrect(obj){

	// tutte le carte hanno il prefisso card prima dell'id della carta
	let card_id = obj.parentNode.parentNode.id.replace("card", "");
	let card = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	document.getElementsByClassName("card-enabled")[0].className+=" answered";
	card.style.backgroundColor = "var(--true)";

	$.ajax({
		type: "POST",
		url: "../php/game/set_sp_answer.php",
		data: {card_id:card_id, correct:true},

		// in caso di successo
		success: function (data) {	
			//alert(data);
		},

		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
		}
	});


	let i;
	for (i=0; i < document.getElementById("cards-container").children.length; i++){
		
		if(document.getElementById("cards-container").children[i].id == ("card"+card_id)){
			break;
		}
	}

	updateNavigation(i, "correct");
	nextQuestion(0);
}

// imposta la risposta singola come errata
function setSpWrong(){

	let card = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	card.style.backgroundColor = "var(--false)";
	document.getElementsByClassName("card-enabled")[0].className+=" answered";

	let card_id = card.parentElement.parentElement.id;
	$.ajax({
		type: "POST",
		url: "../php/game/set_sp_answer.php",
		data: {card_id:card_id.replace("card", ""), correct:false},

		// in caso di successo
		success: function (data) {	
			alert(data);
		},

		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
		}
	});


	let i;
	for (i=0; i < document.getElementById("cards-container").children.length; i++){
		
		if(document.getElementById("cards-container").children[i].id == (card_id)){
			break;
		}
	}

	updateNavigation(i, "wrong");
	nextQuestion(0);
}

// porta alla prossima domanda
function nextQuestion(type){
	// se nel classname è presente answered, allora abbiamo già risposto

	let answered = document.getElementsByClassName("card-enabled")[0].className.indexOf("answered") != -1;

	// nascondo il menu per l'invio della risposta (tipo 0)
	if (type == 0 && answered)
		document.getElementsByClassName("card-enabled")[0].getElementsByClassName("single-right")[0].style.display="none";
	

	// recupero l'id della carta
	let card_id = document.getElementsByClassName("card-enabled")[0].id;

	let next = false;
	let count = 1;
	for (let x of document.getElementById("cards-container").children){

		// trova la prossima domanda non risposta
		if(next && x.className.indexOf("answered") == -1){
			goToAnswer(count);
			return;
		}

		// se abbiamo trovato la carta, la prossima non risposta è quella giusta
		if (card_id == x.id){
			next = true;
		}

		count++;
	}

	// se siamo qua e next=true siamo all'ultima domanda e dovremmo 
	// mostrare la classifica
	if(next){
		count=1;

		// consente di trovare la prossima domanda non risposta
		for (let x of document.getElementById("cards-container").children){
			if(x.className.indexOf("answered") == -1){
				goToAnswer(count);
				return; // se la trova, esce
			}

			count++;	
		}

		// se non la trova abbiamo risposta a tutto, perciò parte la consegna
		submit();
	}
}

function submit(){
	alertbox({
		title: "Vuoi consegnare?",
		content: "Le domande a tua disposizione sono finite, vuoi consegnare? Le domande senza una risposta verranno considerate sbagliate.",
		
		yes: function(){
			let id = document.body.id.replace("match", "");
			window.location.href="../pages/ranking.php?id="+id;
		},

		no: function(){
			alert("not");
		}
	});
}

// serve per mostrare il pulsante di fine
function checkShowFinish(){
	let all_seen 		= document.getElementsByClassName("card-enabled")[0].getElementsByClassName("seen").length;
	let all_elements 	= document.getElementById("cards-container").children.length;
	let selected_seen 	= document.getElementsByClassName("card-enabled")[0].getElementsByClassName("selected seen").length;

	if((all_elements) == (all_seen - selected_seen + 1) ){
		for (let x of document.getElementsByClassName("finish")){
			x.style.display = "block";
		}
	}
}


// serve a muoversi tra le domande
function goToAnswer(id){

	let answered = 0;
	for (let i = 0; i < document.getElementById("cards-container").children.length; i++){
		let x = document.getElementById("cards-container").children[i];

		if (i == (id-1)){
			x.className = x.className.replace("card-enabled", "") + " card-enabled";
		}

		else {

			if (x.className.indexOf("card-enabled") != -1){
				updateNavigation(i, "seen");
				x.className = x.className.replace("card-enabled", "");
			}
				
		}

		if (x.className.indexOf("answered") != -1) {answered++;}
	}


	checkShowFinish();
}

function getActiveCardId(){
	return document.getElementsByClassName("card-enabled")[0].id.replace("card", "");
}


// serve per inviare la risposta per le risposte multiple
function sendAnswer(obj){

	// imposta il pulsante come premuto
	obj.className = obj.className.replace("play", "").replace("next", "") + " play next";

	// imposta la carta come "a cui è stata risposta"
	document.getElementsByClassName("card-enabled")[0].className+=" answered";


	// controlla se ci sono carte selezionate
	let carte_selezionate = false;
	obj.onclick = (()=>{
		nextQuestion(1);
	});


	// serva a contare dove dobbiamo aggiorare
	let card_id = document.getElementsByClassName("card-enabled")[0].id;
	let i=0;
	for (i=0; i < document.getElementById("cards-container").children.length; i++){
		if(document.getElementById("cards-container").children[i].id == (card_id)){
			break;
		}
	}

	updateNavigation(i, "correct");


	card_number = getActiveCardId();
	for (let x of document.getElementsByClassName("card-enabled")[0].getElementsByClassName("answer-content-selected")){
		carte_selezionate = true;


		// invia le risposte al database
		$.ajax({
			type: "POST",
			url: "../php/game/set_answer.php",
			data: {card_id:card_number, id:x.id},
	
			// in caso di successo
			success: function (data) {	
				alert(data);
				if (data == 1){
					x.className = "content-box answer-content-true";
				}

				else if(data==-1){
					/* TODO: segnalare che esiste già una risposta alla domanda */
					alert("errore, esiste già una risposta a questa domanda");
				}

				else {
					x.className = "content-box answer-content-false";
					updateNavigation(i, "wrong");
				}

			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				alert("ops");
			}
		});
	}

	

	// serve per segnare quali erano le giuste totali se non selezionate tutte
	$.ajax({
		type: "POST",
		url: "../php/game/get_answer.php",
		data: {id:document.getElementsByClassName("card-enabled")[0].id.replace("card", "")},
	
		// in caso di successo
		success: function (data) {	
			data = JSON.parse(data);
			alert(JSON.stringify(data));

			let da_aggiornare = [];

			// oer ogni risposta nella pagina attiva
			for (let x of document.getElementsByClassName("card-enabled")[0].getElementsByClassName("answer-content")){

				// scorro tutte le chiavi di data
				for (let y of data){
					

					if (y["id"]== x.id && y["correct"]){

						// salvo che deve essere segnata come che in realà era vera
						da_aggiornare.push(x);
						updateNavigation(i, "wrong");


					}
				}
			}

			// le aggiorno
			for (let z of da_aggiornare){
				z.className = "content-box answer-content-wrong";
			}
			
		},
	
		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
		}
	});

	// devo segnare come null solo se ho selezionato qualche carta ma non tutte, in modo da evideniare il fatto che ho sbagliato
	if (carte_selezionate){
		$.ajax({
			type: "POST",
			url: "../php/game/set_answer.php",
			data: {card_id: card_number, id:-1}, // -1 specifica che non è stato scelta nessuna alternativa
		
				// in caso di successo
			success: function (data) {	
					//alert(data);
			},
		
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
			}
		});
	}

	/* faccio avvenire la chiamata se non ho risposto nulla segnado come vuoto */
}


// mette in movimento lo sfondo
function slideBackground() {

	var background = document.body;
	var x = 0;
	
	setInterval(function(){
		background.style.backgroundPosition = x + 'px ' + x+"px";
		x-=0.25;
	}, 10);
	
}
	
window.addEventListener('load',slideBackground,false);

window.addEventListener('load', ()=>{
	if(document.getElementById("cards-container").children.length == 0){
		okbox({
			title: "Non è possibile accedere alla partita", 
			content:"La partita alla quale stai cercando di accedere non esiste o è già iniziata, siamo spiacenti.",

			ok: function(){window.location.href="../pages/dashboard.php";}
		});
	}
});