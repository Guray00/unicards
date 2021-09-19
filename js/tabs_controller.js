// tiene traccia del numero di tab attive
MAX_TAB	 = 0;

window.addEventListener("load",function(){
	/*scorro tutte le tab in modo da mostrare quella selezionata di default*/
	for(i of document.getElementsByClassName("tab")){

		if(i.checked){
			for(j of document.getElementsByClassName("tab-container")){
				if(j.id === i.value){
					j.style.display = "flex";
				}
		
				else {
					j.style.display="none";
				}
			}
		}

		/* aggiungo l'handler per la pressione della tab */
		if (i.nodeName === "INPUT"){	
			i.onclick = function(){
				tabClicked(this);				
			}
			MAX_TAB++;
		}
	}
	
	for (i of document.getElementsByClassName("add-tab")){
		i.onclick = function(){
			// prendo il tab-header a cui il pulsante di aggiunta fa riferimento
			let p = this.parentNode;
			
			// aggiorno il conteggio delle tab per avere sempre id unici
			MAX_TAB++;

			// creo il radio button per selezionare la tab, con id unico
			let rdr   = document.createElement("input");
			rdr.type  = "radio";
			rdr.id    = "tab"+MAX_TAB;
			rdr.value = "tab"+MAX_TAB;
			rdr.name  = "tabs";
			rdr.className = "tab";
			rdr.checked = true;			//lo imposto come selezionato
			rdr.onclick = function(){
				tabClicked(this);
			}

			// creo la label per selezionare la tab, con id unico
			let lbl = document.createElement("label");
			lbl.setAttribute("for", "tab"+MAX_TAB);
			lbl.className = "tab";
			lbl.id = MAX_TAB+"_section";
			lbl.innerText = "Untitled";//+MAX_TAB; 

			// inserisco il contenitore di elementi
			let div = document.createElement("div");
			div.className="tab-container";
			div.id = "tab"+MAX_TAB;
			
			// aggiungo le tab al tab-header
			p.appendChild(rdr);
			p.appendChild(lbl);		

			//aggiungo il contenuto della tab al gestore di tab
			document.getElementById("tab-content-"+p.id.replace("tab-header-","")).appendChild(div);
			tabClicked(rdr);
		}
	}
})

/* mostra una tab nascondendo le rimanenti */
function tabClicked(self){
	for(i of document.getElementsByClassName("tab-container")){
		if(i.id === self.value){
			i.style.display = "flex";
		}

		else {
			i.style.display="none";
		}
	}
}

