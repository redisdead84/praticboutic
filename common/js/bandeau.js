

$(document).ready(function(){
	var corps = document.body;
	var newdiv = document.createElement("div");
	newdiv.id = "barre";
	newdiv.innerHTML = "Ce site utilise des cookies";
	var newdiv2 = document.createElement("div");
	newdiv2.id = "fermer";
	newdiv.appendChild(newdiv2);
	corps.appendChild(newdiv);


  if (sessionStorage.getItem("barre") == "close")
		$('#barre').hide();

  $('#barre').animate({
    marginTop: "0",
  }, 500);
  $("#fermer").mousedown(function(){      
    $('#barre').animate({ 
      marginBottom: "-90px",
    }, 500);
    sessionStorage.setItem("barre","close");
   });
 });
