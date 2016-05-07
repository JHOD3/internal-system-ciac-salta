//VERIFICA SI HAY MENSAJES NUEVOS --> POR AHORA DESHABILITADO
/*window.setInterval(function(){
	$.ajax({  
		type: "POST",   
		url: "../ajax/admin_mensajes.php",					
		data: {tipo: 'verificar_mensajes'}, 
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			//alert (rta);
			$("#mensajes_nuevos").html(rta);
			
		},
		complete: function(requestData, exito){
		}
	});
},3000);*/