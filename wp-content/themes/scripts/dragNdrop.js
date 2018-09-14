function allowDrop(ev){
	ev.preventDefault();
	}
	
function drag(ev)
   {
     ev.dataTransfer.setData("ID",ev.target.id); // charge dans la variable Texte, l'id de ce qui est dragué
	var toChange = document.getElementById(ev.target.id);
	var changed = toChange.src.replace('-gif-ombre.gif','.png');  // pour remplacer le gif par le png quand on drague. 
	toChange.src = changed;
   } 

function drop(ev)
   {
     ev.preventDefault();
     var data=ev.dataTransfer.getData("ID"); 
	if(ev.target.getAttribute('data-drop') == data)  { // si l'id de ce qui est dragué est le même que celui qui reçoit le drop, alors ouvre la page ..
	location.href = ev.target.getAttribute('link-it');
	}
	else {
		var toDisplay = 'Glisse moi sur : '+data;
		alert(toDisplay);
	}
// ev.target.appendChild(document.getElementById(data));
   }

