//ESTA FUNCION INICIA LAS VENTANAS PARA PODER ABRIR PERO PRINCIPALMENTE PARA CERRAR VENTANAS DENTRO DE VENTANAS
function IniciarVentana(nombre, tipo, tabla, tipo_abm){
	//VALORES POR DEFECTO... CUANDO ABRO MENU PARA ADMINISTRAR, CAMBIA SEGUN LA TABLA...
	var Height = 700;
	var Width =  '90%';
			
	//ESTO ES PARA ARGUMENTOS OPCIONALES		
	tabla || ( tabla = '' );
	tipo_abm || ( tipo_abm = '' );
	
	
	if (tipo == "abrir"){
		switch (nombre){
			case 'ventana_menu':
				var panel;
				if ($('#panel_menu_sam').length){
					panel = $('#panel_menu_sam');
				}else{
					panel = $('#panel_menu');
				}
				panel.append('<div id="'+nombre+'"></div>');
				switch (tabla){
					case 'pacientes':
					case 'medicos':
						
					break;
					case 'especialidades':
						Width = 600;
					break;
					case 'estudios':
						Width = 600;
					break;
					case 'obras_sociales':
						Width = 800;
					break;
					case 'medicos_especialidades':
						Width = 600;
					break;
				}
			break;
			case '':
				contenedor_listado
			break;
			case "turno_detalle_imprimir":
				$("#contenedor_imprimir").append('<div id="'+nombre+'"></div>');
				Width = 450;
			break;
			case "turnos_detalle_imprimir":
				$("#contenedor_imprimir").append('<div id="'+nombre+'"></div>');
				Width = "90%";
			break;
			case "cobros_imprimir":
				$("#contenedor_imprimir").append('<div id="'+nombre+'"></div>');
				Width = "90%";
			break;
			
			case "ventana_estado_turno":
				$("#grilla_turnos").append('<div id="'+nombre+'"></div>');
				Width = 600;
				Height = 600;
			break;
			case "ventana_estudios":
				$("#grilla_turnos").append('<div id="'+nombre+'"></div>');
				Width = 600;
				Height = 500;
			break;
			case "ventana_estudios_modificacion":
				$("#ventana_estado_turno").append('<div id="'+nombre+'"></div>');
				Width = 600;
				Height = 500;
			break;
			case "ventana_mensaje":
				$("#contenedor_ventana").append('<div id="'+nombre+'"></div>');
				Width = 400;
				Height = 600;
			break;
			case "ventana_hija_"+tabla:
				$("#contenedor_listado").append('<div id="'+nombre+'"></div>');
				switch (tabla){
					case 'medicos_especialidades':
					case 'cobros':
					case 'medicos_estudios':
					case 'obras_sociales_estudios':
					case 'obras_sociales_planes':
					case 'medicos_obras_sociales':
					case 'turnos':
						Width =600;
					break;
					case 'medicos_horarios':
						Width =700;
					break;
				}
			break;
			case "ventana_opciones":
			
				$("#contenedor_listado").append('<div id="'+nombre+'"></div>');
				Width = 600;
				switch (tabla){
					case 'pacientes':
						Height = 600;
					break;
					case 'medicos':
						Height = 600;
					break;
					case 'medicos_estudios':
						Height = 500;
					break;
					case 'especialidades':
						Height = 150;
					break;
					case 'obras_sociales':
						Height = 300;
					break;
					case 'estudios':
						Height = 420;
					break;
					case 'medicos_especialidades':
						Height = 300;
					break;
					case 'medicos_obras_sociales':
						if (tipo_abm == 'alta')
							Height = 500;
						else
							Height = 150;
					break;
					case 'medicos_horarios':
						Height = 350;
					break;
					case 'obras_sociales_estudios':
						Height = 300;
					break;
					case 'obras_sociales_planes':
						Height = 150;
					break;
				}
			break;
		}
		$("#"+nombre).dialog({
			autoOpen: false,
			height: Height,
			width: Width,
			modal: true,
			close: function(){
				$(this).dialog('destroy').remove();	
			}
		});	
	
	}
	
}

//MOSTRAR EL ALERT CON FORMATO UI
this.alert = function (message, title) {
    /// <summary>Redefine la ventana modal de alerta</summary>
    var $div = $("<div />");
    $div.attr("title", typeof title !== "string" ? "Atención" : title);
    //-
    var $p1 = $("<p />");
    var $span = $("<span />");
    $span.addClass("ui-icon");
    $span.addClass("ui-icon-circle-check");
    $span.css({ float: "left", margin: "0 7px 0 0" });
    $p1.append($span);
    $p1.append(message);
    $div.append($p1);
    //-
    $(document.body).append($div);
    $div.dialog({
        modal: true,
        buttons: {
            Ok: function () {
                var $dlg = $(this);
                $dlg.dialog("close");
                $dlg.remove();
            }
        }
    });
}