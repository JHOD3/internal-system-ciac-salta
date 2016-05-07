//ESTADO DEL TURNO - CLICK EN EL ESTADO DEL TURNO
$(document).on("click", ".btn_estado_turno", function(e){
	e.preventDefault();
	var id_turno = $(this).data("id");
	var id_turno_estado = $(this).data("id_turnos_estados");
	var titulo = "Estado del Turno";
	if(id_turno_estado == 2){
		IniciarVentana("ventana_estado_turno", "abrir");
	
		$.ajax({ 
			dataType: "html",
			type: "POST",   
			url: "../ajax/admin_turno.php",
			data: {tipo:"panel_medico", tabla: "turnos", id_turno: id_turno},
			beforeSend: function(data){
				$(ventana_estado_turno).html("");
			},						
			success: function(requestData){
				var rta = requestData;
				$(ventana_estado_turno).html(rta);
				$(ventana_estado_turno).dialog('option', 'title', titulo)
				$(ventana_estado_turno).dialog( "open" );
			},
			complete: function(requestData, exito){
			},
			error: function(requestData){
				alert (requestData);	
			}
		});
	}
	
})

function Duplicados(id_medico, id_especialidad, fecha){
	return true;
}