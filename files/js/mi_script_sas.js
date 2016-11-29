//PARA LOS BOTONES DE OPCIONES DE CADA TABLA DEL ADMIN. 
//Ej. Detalle, Editar, Tablas Hijas
$(document).on("click", ".btn_opciones", function(e){
	e.preventDefault();
	var tipo_btn = $(this).data("tipo_btn");
	var id = $(this).data("id");
	var id_padre = $(this).data("id_padre");
	
	if (tipo_btn == "tabla_hija"){
		
		var tabla_hija = $(this).data("hija");
		//var ventana_opciones_hijas = "#dialog_tabla_" + tabla_hija;
	
		IniciarVentana('ventana_hija_'+tabla_hija, "abrir", tabla_hija);

		$.ajax({  
			type: "POST",   
			url: "../ajax/admin_tabla.php",					
			data: {tabla: tabla_hija, id_padre: id}, 
			beforeSend: function() {
				$('#ventana_hija_'+tabla_hija).html("");
			},
			success: function(requestData){
				var rta = requestData;
				$('#ventana_hija_'+tabla_hija).html(rta);
			},
			complete: function(requestData, exito){
			},
			error: function (){
				alert ("error");
			}
		});
		
		var nombre = $(this).data('nombre');
		$('#ventana_hija_'+tabla_hija).dialog('option', 'title', 'Administraci\u00f3n de ' + nombre);
		$('#ventana_hija_'+tabla_hija).dialog( "open" );
		
		$('#ventana_hija_'+tabla_hija).focus();
			
	}else if(tipo_btn == "tabla_eme"){
		var tabla = $(this).data("tabla");
		var tipo = $(this).data("tipo");
		var titulo = $(this).data("titulo");
        var id = $(this).data("id");

		IniciarVentana("ventana_menu", "abrir", tabla);
		$(ventana_menu).html('');
		$(ventana_menu).dialog('option', 'title', 'Editar Paciente');
		$(ventana_menu).dialog( "open" );
		$(ventana_menu).focus();

		$(ventana_menu).dialog({
		  autoOpen: false,
		  height: 670,
		  width: "90%",
		  modal: true,
		  close: function() {  }
		});

		$.ajax({
			type: "POST",
			url: "../ajax/admin_form.php",
			data: {id: id, tabla: tabla, tipo: tipo, tipo_btn: tipo_btn},
			beforeSend: function() {
			},
			success: function(requestData){
				var rta = requestData;
				$(ventana_menu).html(rta);
			},
			complete: function(requestData, exito){
			},
			error: function (){
				alert ("error");
			}
		});

		$(ventana_menu).dialog('option', 'title', titulo);
		$(ventana_menu).dialog( "open" );
	}else{
		var tabla = $(this).data("tabla");
		var tipo = $(this).data("tipo");
		var titulo = $(this).data("titulo");
		
		IniciarVentana("ventana_opciones", "abrir", tabla, tipo);
		
		$.ajax({  
			type: "POST",   
			url: "../ajax/admin_form.php",					
			data: {tabla: tabla, id: id, tipo: tipo, id_padre: id_padre}, 
			beforeSend: function() {
				$(ventana_opciones).html("");
			},
			success: function(requestData){
				var rta = requestData;
				$(ventana_opciones).html(rta);
			},
			complete: function(requestData, exito){
			},
			error: function (){
				alert ("error");
			}
		});
		
		$(ventana_opciones).dialog('option', 'title', titulo);
		$(ventana_opciones).dialog( "open" );
	}
})

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
        if ($('#bloqueado').val() == '0') {
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
        }
	} else {
		alert ("Falta seleccionar paciente.", 'ATENCIÓN');	
	}
});	

$(document).on("click", ".imprimir", function(){
	var id = $(this).data('id');
	var tipo = $(this).data('tipo');
	
	switch (tipo){
		/*case "turno":
			IniciarVentana('turno_detalle_imprimir', "abrir");
			
			var titulo = "Estado del Turno";
			$.ajax({ 
				dataType: "html",
				type: "POST",   
				url: "../ajax/imprimir.php",
				data: {tipo:tipo, id: id},
				beforeSend: function(data){
					
				},						
				success: function(requestData){
					var rta = requestData;
					//EL DIV CONTENEDOR ESTA EN GRILLA TURNOS
					$(turno_detalle_imprimir).html(rta);
					
					$(turno_detalle_imprimir).dialog('option', 'title', titulo)
					$(turno_detalle_imprimir).dialog( "open" );
				},
				complete: function(requestData, exito){
				},
				error: function(requestData){
					alert (requestData);	
				}
			});
		break;*/
		case "turno":
			
			$.ajax({ 
				dataType: "html",
				type: "POST",   
				url: "../ajax/imprimir.php",
				data: {tipo:tipo, id: id},
				beforeSend: function(data){
					
				},						
				success: function(requestData){
					var rta = requestData;
					//EL DIV CONTENEDOR ESTA EN GRILLA TURNOS
					$("#contenedor_imprimir").html(rta);
				},
				complete: function(requestData, exito){
				},
				error: function(requestData){
					alert (requestData);	
				}
			});
		break;
		case "turnos_todos":
			IniciarVentana('turnos_detalle_imprimir', "abrir");
	
			var titulo = "Turnos del Medico";
			var fecha = $(this).data('fecha');
			var id_especialidad = $(this).data('id_especialidad');
			
			$.ajax({ 
				dataType: "html",
				type: "POST",   
				url: "../ajax/imprimir.php",
				data: {tipo:tipo, id: id, fecha: fecha, id_especialidad: id_especialidad},
				beforeSend: function(data){
					
				},						
				success: function(requestData){
					var rta = requestData;
					//EL DIV CONTENEDOR ESTA EN GRILLA TURNOS
					$(turnos_detalle_imprimir).html(rta);
					
					$(turnos_detalle_imprimir).dialog('option', 'title', titulo)
					$(turnos_detalle_imprimir).dialog( "open" );
				},
				complete: function(requestData, exito){
				},
				error: function(requestData){
					alert (requestData);	
				}
			});
		break;
		case "cobros":
			IniciarVentana('cobros_imprimir', "abrir");
	
			var titulo = "Cobros del Medico";
			var fecha = $(this).data('fecha');
			var id_especialidad = $(this).data('id_especialidad');
			
			$.ajax({ 
				dataType: "html",
				type: "POST",   
				url: "../ajax/imprimir.php",
				data: {tipo:tipo, id: id, fecha: fecha, id_especialidad: id_especialidad},
				beforeSend: function(data){
					
				},						
				success: function(requestData){
					var rta = requestData;
					//EL DIV CONTENEDOR ESTA EN GRILLA TURNOS
					$(cobros_imprimir).html(rta);
					
					$(cobros_imprimir).dialog('option', 'title', titulo)
					$(cobros_imprimir).dialog( "open" );
				},
				complete: function(requestData, exito){
				},
				error: function(requestData){
					alert (requestData);	
				}
			});
		break	
	}
	
	
	
	
	
	
});

//ESTADO DEL TURNO - CLICK EN EL ESTADO DEL TURNO
$(document).on("dblclick", ".btn_estado_turno", function(e){
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


$(document).on("change", "#medicos_especialidades", function(){
	CrearAgenda();
	var fecha=new Date();
	
	GrillaInicial(fecha);
});

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

$(document).on("click", "#btn_enviar", function(e){
	e.preventDefault();
	var variables = $("#mensajes").serialize();
	//alert ("variables: "+variables);
	$.ajax({  
		type: "POST",   
		url: "../ajax/altas.php",					
		data: {variables: variables, tabla: "mensajes"}, 
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			IniciarVentana("ventana_mensaje", "cerrar");
			$(ventana_mensaje).html(rta);
		},
		complete: function(requestData, exito){
		},
		error: function (){
			alert ("error");
		}
	});
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

$(document).on("click", "#btn_agregar_obra_social", function(){
	var ids_obras_sociales = "";
	$(".chosen-choices li.search-choice a").each(function( index ) {
		var id = $(this).data("option-array-index");
		id_obra_social = $("select#estudios option:eq("+id+")").val();
		ids_obras_sociales = ids_obras_sociales + id_obra_social + ", ";
	});
	//alert(ids_obras_sociales);
	
	
	var variables = $("form").serialize();
	$.ajax({ 
		dataType: "html",
		type: "POST",   
		url: "../ajax/altas.php",
		data: {variables: variables, tabla: "medicos_obras_sociales", ids_obras_sociales: ids_obras_sociales},
		beforeSend: function(data){},						
		success: function(requestData){
			var rta = requestData;
			TableMedicosObrasSociales.fnDraw();
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



//PARA IMPRIMIR
function imprSelec(muestra){
	var toPrint = document.getElementById(muestra);
	var popupWin = window.open('', 'popimpr', 'width=1000,height=700,location=no');
	popupWin.document.open();
	popupWin.document.write('<html><title>Cuadro de Impresión</title><link rel="stylesheet" type="text/css" href="../files/css/print.css" /></head><body onload="window.print(); window.close();">')
	popupWin.document.write(toPrint.innerHTML);
	popupWin.document.write('</body></html>');
	popupWin.document.close();
	popupWin.print();
	popupWin.close();
	
	if(popupWin.attachEvent) {
		popupWin.attachEvent("onunload", ClosePopup);
	}else{
		if(popupWin.addEventListener) {
			popupWin.addEventListener("unload", ClosePopup, false);
		} else {
			//popupWin.onunload = ClosePopup;
		}
	}
}

$(document).on("mouseover",".ui-state-default", function(){
	//alert('adas');
});


function SoloNros(e){
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
	return true;
	 
	return /\d/.test(String.fromCharCode(keynum));
}






$(document).ready(function(){
	//CAPTURAR EL ENTER EN LOS INPUTS
	$(document).keypress(function(e){
	   if(e.which == 13){
		  var elto = $(document).find(':focus');
		  switch(elto.attr("id")){
			case "dni": //CUANDO BUSCA UN PACIENTE POR EL ID
				$("#btn_buscar_paciente").trigger("click");
			break; 
		  }
	   }
	});
});




function HoraActual(){
	if (!document.layers&&!document.all&&!document.getElementById)
	return

	 var Digital=new Date()
	 var hours=Digital.getHours()
	 var minutes=Digital.getMinutes()
	 var seconds=Digital.getSeconds()

	var dn="PM"
	if (hours<12)
	dn="AM"
	if (hours>12)
	hours=hours-12
	if (hours==0)
	hours=12

	 if (minutes<=9)
	 minutes="0"+minutes
	 if (seconds<=9)
	 seconds="0"+seconds
	//change font size here to your desire
	myclock="<span>"+hours+":"+minutes+":"
	 +seconds+" "+dn+"</span>"
	if (document.layers){
	document.layers.liveclock.document.write(myclock)
	document.layers.liveclock.document.close()
	}
	else if (document.all)
	liveclock.innerHTML=myclock
	else if (document.getElementById)
	document.getElementById("liveclock").innerHTML=myclock
	setTimeout("HoraActual()",1000)
}

$(function() {
    $("#medicos").keypress(function(event){
        if (console && console.log) console.log(event.key);
        letters = ['á','é','í','ó','ú','ñ','ü','Á','É','Í','Ó','Ú','Ñ','Ü'];
        replace = ['a','e','i','o','u','n','u','A','E','I','O','U','N','U'];
        POS = $.inArray(event.key, letters);
        if (POS >= 0) {
            event.preventDefault();
            $(this).val($(this).val() + replace[POS]);
            return false;
        }
    });
	$("#medicos").autocomplete({
        source: "../ajax/buscar.php",
		focus: function( event, ui ) {
			//$( "#medicos" ).val( ui.item.label );
			return false;
		},
		select: function( event, ui ) {
			$( "#medicos" ).val( ui.item.nombres + " " + ui.item.apellidos);
			$( "#id_medico" ).val( ui.item.id );
			$( "#medicos_especialidad" ).html( ui.item.especialidad );
			
			var id_medico = $("#id_medico").val();
			
			$.ajax({  
				type: "POST",   
				url: "../ajax/generar_control.php",					
				data: {tipo: "drop", tabla: "medicos_especialidades", valor: id_medico}, 
				beforeSend: function() {
				},
				success: function(requestData){
					var rta = requestData;
					$("#contenedor_drop_especialidades").html(rta);
					
					var id_especialidad = $("#medicos_especialidades").val();
					
					if (id_especialidad != null){
						CrearAgenda();	
					}else{
						$("#contenedor_agenda").html("");
					}
					
					var fecha=new Date();
					
					GrillaInicial(fecha);
					

					
					
					
				},
				complete: function(requestData, exito){
					
				},
				error: function (){
					alert ("error");
				}
			});
	
			return false;
		}
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		//.append( "<a>" + item.nombres + " " + item.apellidos + "<br><small>" + item.especialidad + "</small></a>" )
		.append( "<a>" + item.saludo + " " + item.nombres + " " + item.apellidos + "</a>" )
		.appendTo( ul );
	};
});

function Duplicados(id_medico, id_especialidad, fecha){
	$.ajax({  
		type: "POST",   
		url: "../ajax/operaciones_varias.php",					
		data: {fecha: fecha,  id_medico: id_medico, id_especialidad: id_especialidad, tipo:'duplicados'}, 
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			//alert (rta);
			$("#duplicados").html(rta);
			$("#cont_duplicados").show();
		},
		complete: function(requestData, exito){
		},
		error: function (){
			alert ("error1");
		}
	});
}
