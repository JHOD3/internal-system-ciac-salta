//OBRAS SOCIALES
// ----- Crear Drop Obra Social

$(function(){
	//CREA EL DROP DE PLANES SEGUN OBRA SOCIAL ELEGIDA
	$(document).on("change","select#obras_sociales",function() {
		var valor = $(this).val();
		
		if (valor == "")
			tipo = "drop_vacio";
		else
			tipo = "drop";
		
		$.ajax({  
			type: "POST",   
			url: "../ajax/generar_control.php",
			data: {tipo: tipo, tabla: "obras_sociales_planes", valor: valor}, 
			beforeSend: function() {
			},
			success: function(requestData){
				var rta = requestData;
				$("#obras_sociales_planes_change").html(rta);
			},
			complete: function(requestData, exito){},
			error: function (){
				alert ("Ocurri&oacute; un error. Vuelva a Intentarlo.");
			}
		});
	});
});