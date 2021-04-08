
// evito di dare lo stesso id a carte differenti quando vengono aggiunte o rimosse
MAX_CARD = 1;
MAX_TAB	 = 1;

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


	for (i of document.getElementsByClassName("section_name")){
		i.onchange = function(){
			number = this.id.substr(0, this.id.indexOf("_"));
			console.log("val: "+ number);
			document.getElementById(number+"_section").innerText = this.value;
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


	let a  = document.createElement("textarea");
	a.id   = section+"_answer"+MAX_CARD;
	a.name = section+"_answer"+MAX_CARD;
	a.required = true;

	lq.innerText = "Domanda "+ number;
	la.innerText = "Risposta "+number;

	btn.parentNode.appendChild(lq);
	btn.parentNode.appendChild(la);
	btn.parentNode.appendChild(q);
	btn.parentNode.appendChild(a);

	textAreaHandler(q, a, lq, la);
	
	/*
	c.onchange = function() {
		if(this.value == "" && d.value == "" && btn.parentNode.getElementsByTagName("textarea").length > 2){
			let p = this.parentNode;
			p.removeChild(this);
			p.removeChild(d);
			p.removeChild(a);
			p.removeChild(b);
			updateLabels(p);
		}
	}

	d.onchange = function() {
		if(this.value == "" && c.value == ""  && btn.parentNode.getElementsByTagName("textarea").length > 2){
			let p = this.parentNode;
			p.removeChild(this);
			p.removeChild(c);
			p.removeChild(a);
			p.removeChild(b);
			updateLabels(p);
		}
	}*/
}

/*sfruttiamo l'arrotondamento per rinominare
correttamente le etichette delle domande
e delle risposte*/
function updateLabels(p){
	q = 0.5; 
	for (i of p.getElementsByTagName("label")){
		if(i.innerText.includes("Domanda")) i.innerText = "Domanda " + Math.round(q);
		else if(i.innerText.includes("Risposta")) i.innerText = "Risposta " +Math.round(q);
		q+=0.5;
	}
}

/*

<div class="tab-container" id="container1">
						<div id="grid">	
							<?php
								if (!isset($cards)){
									echo '<label>Domanda 1</label>
									<label>Risposta 1</label>
									<textarea></textarea>
									<textarea></textarea>';
								}

								else {
								}
							?>
							<button type='button' id="btn-add" onclick="addCard(this)">Aggiungi carta</button> 
						</div>
					</div>

					<div class="tab-container" id="container2">bb</div>
					<div class="tab-container" id="container3">cc</div>


*/ 