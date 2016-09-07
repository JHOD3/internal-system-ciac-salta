//ESTADO DEL TURNO - CLICK EN EL ESTADO DEL TURNO
/*
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
*/

function Duplicados(id_medico, id_especialidad, fecha){
	return true;
}

$(document).on("dblclick", ".reservar", function(e, date){
	e.preventDefault();
	var desde = $(this).data("desde");
	var hasta = $(this).data("hasta");
	var fecha = $(this).data("fecha");
	if ($(this).data("consultorio")){
		consultorio = $(this).data("consultorio");
	}else{
		consultorio = 0;
	}
	var id_medico = $("#id_medico").val();
	var id_especialidad = $("#medicos_especialidades").val();
	var id_turno_tipo = $(this).data("turnos_tipos");
	
	var fechacompleta = $("#agenda").datepicker('getDate');
	var dia = fechacompleta.getDay();
	
	if ($("#id_pacientes").length){
		var id_paciente = $("#id_pacientes").val();
		var id_obra_social = $("#id_obras_sociales").val();
		var id_obra_social_plan = $("#id_obras_sociales_planes").val();
		
		var variables = "id_medico="+id_medico+"&id_especialidad="+id_especialidad+"&id_paciente="+id_paciente+"&desde="+desde+"&hasta="+hasta+"&fecha="+fecha+"&dia="+dia+"&id_turno_tipo="+id_turno_tipo+"&consultorio="+consultorio;
		
		$.ajax({ 
			dataType: "html",
			type: "POST",   
			url: "../ajax/altas.php",
			data: {variables: variables, tabla: "turnos"},
			beforeSend: function(data){},						
			success: function(requestData){
				//alert (requestData);
				if (requestData != 'existe_turno'){
					var id_turno = requestData;
					
					//SI ES TURNO DE ESTUDIO ABRO LA PANTALLA QUE CARGA LOS ESTUDIOS		
					//alert (id_turno_tipo, 'ID TURNO TIPO');
					
					if (id_turno_tipo == 'estudios'){
						IniciarVentana("ventana_estudios", "abrir");
						$.ajax({ 
							dataType: "html",
							type: "POST",   
							url: "../ajax/admin_turno_estudio.php",
							data: {tipo:"panelAlta", tabla: "turnos_estudios", id_turno: id_turno, id_medico: id_medico, id_obra_social: id_obra_social},
							beforeSend: function(data){
								$(ventana_estudios).html("");	
							},						
							success: function(requestData){
								var rta = requestData;
								//alert(rta);
								$(ventana_estudios).html(rta);
								$(ventana_estudios).dialog('option', 'title', 'Agregar Estudios al Turno');
								$(ventana_estudios).dialog( "open" );
							},
							complete: function(requestData, exito){
							},
							error: function(requestData){
								alert (requestData);	
							}
						});
						
					}
				}else{
					alert ('Ya existe un turno reservado en esa horario.');
				}
				GrillaInicial(fechacompleta);
				
			},
			complete: function(requestData, exito){
			},
			error: function(requestData){
				alert ("error");	
			}
		});
	}else{
		alert ("Falta seleccionar paciente.", 'ATENCIÓN');	
	}
});

//ESTADO DEL TURNO - CLICK EN EL ESTADO DEL TURNO
// antes era dblclick
$(document).on("click", ".btn_estado_turno", function(e){
	e.preventDefault();
	var id_turno = $(this).data("id");
	var id_turno_estado = $(this).data("id_turnos_estados");
	var titulo = "Estado del Turno";

	//if(id_turno_estado == 1){
		IniciarVentana("ventana_estado_turno", "abrir");
	
		$.ajax({ 
			dataType: "html",
			type: "POST",   
			url: "../ajax/admin_turno.php",
			data: {tipo:"panel", tabla: "turnos", id_turno: id_turno},
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
	//}
	
})

$(document).on("click", "#btn_estudios_asociados", function(e){
	e.preventDefault();
	var id_turno = $(this).data("id_turno");
	var id_obra_social = $(this).data("id_obra_social");
	var id_medico = $(this).data("id_medico");
	
	//alert (id_medico);
	
	IniciarVentana("ventana_estudios_modificacion", "abrir");
	
	$.ajax({ 
		dataType: "html",
		type: "POST",   
		url: "../ajax/admin_turno_estudio.php",
		data: {tipo: "panel_modificacion", tabla: "turnos_estudios", id_turno: id_turno, id_medico: id_medico, id_obra_social: id_obra_social},
		beforeSend: function(data){
			$(ventana_estudios_modificacion).html("");	
		},						
		success: function(requestData){
			var rta = requestData;
			$(ventana_estudios_modificacion).html(rta);
			
		},
		complete: function(requestData, exito){
		},
		error: function(requestData){
			alert (requestData);	
		}
	});
	$(ventana_estudios_modificacion).dialog('option', 'title', 'Administrar Estudios de un Turno');
	$(ventana_estudios_modificacion).dialog( "open" );
})

$(document).on("click", "#btn_agregar_estudio", function(){
	var ids_estudios = "";
	$(".chosen-choices li.search-choice a").each(function( index ) {
		var id = $(this).data("option-array-index");
		id_estudio = $("select#estudios option:eq("+id+")").val();
		ids_estudios = ids_estudios + id_estudio + ", ";
	});
	//alert(ids_estudios, 'ID DE ESTUDIOS A DAR DE ALTA');
	
	var variables = $("form").serialize();
	$.ajax({ 
		dataType: "html",
		type: "POST",   
		url: "../ajax/altas.php",
		data: {variables: variables, tabla: "turnos_estudios", ids_estudios: ids_estudios},
		beforeSend: function(data){},						
		success: function(requestData){
			var rta = requestData;
			//alert(rta, 'ULTIMO ID REGISTRADO');
		},
		complete: function(requestData, exito){
		},
		error: function(requestData){
			alert (requestData);	
		}
	});	
	IniciarVentana("ventana_estudios", "cerrar");
	$(ventana_estudios).dialog('destroy').remove();	
});

$(document).on("click", "#btn_modificar_estudio", function(){
	var ids_estudios = "";
	$(".chosen-choices li.search-choice a").each(function( index ) {
		var id = $(this).data("option-array-index");
		id_estudio = $("select#estudios option:eq("+id+")").val();
		ids_estudios = ids_estudios + id_estudio + ", ";
	});
	//alert(ids_estudios, 'ID DE ESTUDIOS A DAR DE ALTA');
	
	var variables = $("form").serialize();
	$.ajax({ 
		dataType: "html",
		type: "POST",   
		url: "../ajax/altas.php",
		data: {variables: variables, tabla: "turnos_estudios", ids_estudios: ids_estudios},
		beforeSend: function(data){},						
		success: function(requestData){
			var rta = requestData;
			//alert(rta, 'ULTIMO ID REGISTRADO');
		},
		complete: function(requestData, exito){
		},
		error: function(requestData){
			alert (requestData);	
		}
	});	
	IniciarVentana("ventana_estudios_modificacion", "cerrar");
	$(ventana_estudios_modificacion).dialog('destroy').remove();	
});

$(document).on("click", "#btn_agregar_estudio_medico", function(){
	var ids_estudios = "";
	$(".chosen-choices li.search-choice a").each(function( index ) {
		var id = $(this).data("option-array-index");
		id_estudio = $("select#estudios option:eq("+id+")").val();
		ids_estudios = ids_estudios + id_estudio + ", ";
	});
	//alert(ids_obras_sociales);
	
	
	var variables = $("form").serialize();
	$.ajax({ 
		dataType: "html",
		type: "POST",   
		url: "../ajax/altas.php",
		data: {variables: variables, tabla: "medicos_estudios", ids_estudios: ids_estudios},
		beforeSend: function(data){},						
		success: function(requestData){
			var rta = requestData;
			//alert(rta);
			TableMedicosEstudios.fnDraw();
		},
		complete: function(requestData, exito){
		},
		error: function(requestData){
			alert (requestData);	
		}
	});	
	
	IniciarVentana("ventana_opciones", "abrir");
	$(ventana_opciones).dialog('destroy').remove();	
});
