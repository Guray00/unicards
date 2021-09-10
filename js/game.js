



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

	else {
		obj.className = "content-box answer-content";
	}	

}



function spShowAnswer(obj, answer){

	// mostra il pannello a destra per selezionare se la domanda è corretta o meno
	document.getElementsByClassName("card-enabled")[0].getElementsByClassName("single-right")[0].style.display = "block";

	let parent = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	
	let h1 = parent.getElementsByTagName("h1")[0];
	let p  = parent.getElementsByTagName("p")[0];

	// aggiorna il titolo
	h1.innerText = h1.innerText.replace("Domanda", "Risposta");
	
	// aggiorna il contenuto della domanda con la risposta
	p.innerText = answer;

	// imposta lo sfondo come colore selezionato per segnalare che si ha la risposta
	//parent.style.backgroundColor = "var(--selected)";

	obj.style.backgroundColor = "var(--selected)";


	// se si ripreme sul pulsante, scorre alla prossima domanda
	obj.onclick = ( ()=>{nextQuestion(0);});
}


function updateNavigation(id, type){


	for(let x of document.getElementsByClassName("navigator")){
		for (let y of x.getElementsByClassName("navigator-item")){
			if(y.id == ("navigator"+(id+1))){
				y.className+= (" "+type);
			}
		}
	}
}

function setSpCorrect(obj){

	// tutte le carte hanno il prefisso card prima dell'id della carta
	let card_id = obj.parentNode.parentNode.id.replace("card", "");
	let card = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	card.style.backgroundColor = "var(--true)";

	$.ajax({
		type: "POST",
		url: "../php/game/set_sp_correct.php",
		data: {card_id:card_id},

		// in caso di successo
		success: function (data) {	
			//alert(data);
		},

		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
		}
	});


	let i;
	for (i=0; i < document.body.children.length; i++){
		
		if(document.body.children[i].id == ("card"+card_id)){
			break;
		}
	}

	updateNavigation(i, "correct");
	nextQuestion(0);
}

function setSpWrong(){

	let card = document.getElementsByClassName("card-enabled")[0].getElementsByClassName("card-content")[0];
	card.style.backgroundColor = "var(--false)";

	let card_id = card.parentElement.parentElement.id;


	let i;
	for (i=0; i < document.body.children.length; i++){
		
		if(document.body.children[i].id == (card_id)){
			break;
		}
	}

	updateNavigation(i, "wrong");
	nextQuestion(0);
}

function nextQuestion(type){

	// nascondo il menu per l'invio della risposta (tipo 0)
	if (type == 0)
		document.getElementsByClassName("card-enabled")[0].getElementsByClassName("single-right")[0].style.display="none";
	

	// recupero l'id della carta
	let card_id = document.getElementsByClassName("card-enabled")[0].id;

	let next = false;
	let count = 1;
	for (let x of document.body.children){

		if(next){
			goToAnswer(count);
			return;
		}

		if (card_id == x.id){
			next = true;
		}

		count++;
	}

	// se siamo qua e next=true siamo all'ultima domanda e dovremmo 
	// mostrare la classifica
	if(next){

		alertbox({
			title: "Vuoi consegnare?",
			content: "Le domande a tua disposizione sono finite, vuoi consegnare? Le domande senza una risposta verranno considerate sbagliate.",
			
			yes: function(){
				alert("yes");
			},

			no: function(){
				alert("not");
			}
		});
	}
}

// serve a muoversi tra le domande
function goToAnswer(id){

	for (let i = 0; i < document.body.children.length; i++){
		
		let x = document.body.children[i];

		if (i == (id-1)){
			x.className = x.className.replace("card-enabled", "") + " card-enabled";
		}

		else {
			x.className = x.className.replace("card-enabled", "");			
		}
	}
}

function sendAnswer(obj){

	obj.style.backgroundColor = "var(--selected)";

	let carte_selezionate = false;
	obj.onclick = (()=>{
		nextQuestion(1);
	});


	// serva a contare dove dobbiamo aggiorare
	let card_id = document.getElementsByClassName("card-enabled")[0].id;
	let i=0;
	for (i=0; i < document.body.children.length; i++){
		if(document.body.children[i].id == (card_id)){
			break;
		}
	}

	updateNavigation(i, "correct");


	for (let x of document.getElementsByClassName("card-enabled")[0].getElementsByClassName("answer-content-selected")){
		carte_selezionate = true;

		$.ajax({
			type: "POST",
			url: "../php/game/set_answer.php",
			data: {id:x.id},
	
			// in caso di successo
			success: function (data) {	
				if (data == 1){
					x.className = "content-box answer-content-true";
				}

				else if(data==-1){
					/* TODO: segnalare che esiste già una risposta alla domanda */
				}

				else {
					x.className = "content-box answer-content-false";
					updateNavigation(i, "wrong");
				}

			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
			}
		});
	}

	/* faccio avvenire la chiamata se non ho risposto nulla segnado come vuoto */
	if (!carte_selezionate){
		$.ajax({
			type: "POST",
			url: "../php/game/set_answer.php",
			data: {id:-1}, // -1 specifica che non è stato scelta nessuna alternativa
	
			// in caso di successo
			success: function (data) {	
				//alert(data);
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
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

			for (let x of document.getElementsByClassName("card-enabled")[0].getElementsByClassName("answer-content")){
				for (let y of data){
					if (y["id"]== x.id && y["correct"]){
						x.className = "content-box answer-content-wrong";
						updateNavigation(i, "wrong");
					}
				}
			}
			
		},
	
		// in caso di errore
		error: function (xhr, ajaxOptions, thrownError) {
		}
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
	
window.addEventListener('load',slideBackground,false);