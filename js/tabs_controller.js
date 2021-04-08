

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

