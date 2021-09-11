


function __alertbox_close(){
	
	for (let x of document.getElementsByClassName("alertbox")){
		x.remove();
	}

	for (let x of document.getElementsByClassName("opacity")){
		x.remove();
	}
}


function alertbox(info){

	let title = info.title;
	let content = info.content;

	let opacity = document.createElement("div");
	opacity.className = "opacity";
	opacity.onclick = function(){__alertbox_close();}

	let alertb = document.createElement("div");
	alertb.className = 'content-box alertbox';

	alertb.innerHTML =   `
							<button class='close' onclick='__alertbox_close()'>✕</button>
							<h1>`+title+`</h1>
							<p>`+content+`</p>
							<div class='buttons-container'>
								<button class='alert_yes' id='__alarm_yes'>Si</button>
								<button class='alert_no' id='__alarm_no' >No</button>
							</div>
						`;


	document.body.appendChild(opacity);
	document.body.appendChild(alertb);

	document.getElementById("__alarm_yes").onclick = function(){__alertbox_close(); return info.yes(); }
	document.getElementById("__alarm_no").onclick = function(){__alertbox_close();  return info.no();}
}


function dialogbox(info){

	let title = info.title;
	let content = info.content;

	let opacity = document.createElement("div");
	opacity.className = "opacity";
	opacity.onclick = function(){__alertbox_close();}


	let alertb = document.createElement("div");
	alertb.className = 'content-box alertbox';

	alertb.innerHTML =   `
							<button class='close' onclick='__alertbox_close()'>✕</button>
							<h1>`+title+`</h1>
							<p>`+content+`</p>
						`;


	document.body.appendChild(opacity);
	document.body.appendChild(alertb);
}


function okbox(info){

	let title = info.title;
	let content = info.content;

	let opacity = document.createElement("div");
	opacity.className = "opacity";
	opacity.onclick = function(){__alertbox_close(); info.ok();}


	let alertb = document.createElement("div");
	alertb.className = 'content-box alertbox';

	alertb.innerHTML =   `
							<button class='close' id='__alert_close'>✕</button>
							<h1>`+title+`</h1>
							<p>`+content+`</p>
							<div class='buttons-container'>
								<button class='alert_yes' id='__alarm_ok'>Ok</button>
							</div>
							`;


	document.body.appendChild(opacity);
	document.body.appendChild(alertb);
	document.getElementById("__alarm_ok").onclick = function(){__alertbox_close(); return info.ok(); }
	document.getElementById("__alert_close").onclick = function(){__alertbox_close(); return info.ok(); }

}