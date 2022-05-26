//MEDICOS:
// ----- TrabajaConOS
// ----- CrearAgenda()
// ----- Grilla Inicial(date) -->Grilla de Turnos

//FUNCION PARA SABER SI EL MEDICO TRABAJA CON UNA OBRA SOCIAL DETERMINADA
function TrabajaConOS(id_medico, id_os, id_plan){

	$.ajax({  
		type: "POST",   
		url: "../ajax/operaciones_varias.php",					
		data: {tipo: 'TrabajaConOS', id_medico: id_medico, id_os: id_os, id_plan: id_plan}, 
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			if (rta){
				$('.trabajaos').show();
				$('.notrabajaos').hide();
			}else{
				$('.notrabajaos').show();
				$('.trabajaos').hide();
			}
		},
		complete: function(requestData, exito){
			
		},
		error: function (){
			alert ("error");
		}
	});
}



//GRILLA DE TURNOS
function GrillaInicial(date){
	var id_medico = $("#id_medico").val();
	var id_especialidad = $("#medicos_especialidades").val();
	
	day  = date.getDate(),  
	month = date.getMonth() + 1,              
	year =  date.getFullYear();
	var fecha = year + '-' + month + '-' + day;
	
	var dia = date.getDay();
	
	$.ajax({  
		type: "POST",   
		url: "../ajax/grilla_turnos.php",					
		data: {dia: dia, fecha: fecha,  id_medico: id_medico, id_especialidad: id_especialidad}, 
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			$("#cont_grilla_turnos").html(rta).show();
		},
		complete: function(requestData, exito){
			//VERIFICO  SI ESTA CARGADO EL PACIENTE PARA COMPROBAR SI EL MEDICO TRABAJA CON LA OS DEL PACIENTE
			if ($('#id_pacientes').val() != '' && $('#id_pacientes').val() != undefined){
				var id_medico = $('#id_medico').val();
				var id_os = $('#id_obras_sociales').val();
				var id_plan = $('#id_obras_sociales_planes').val();
				
				//Comprobar si el medico trabaja con esa obra social
				TrabajaConOS(id_medico, id_os, id_plan);
				
				
			}
			$('#habilitar_inhabilitar').show();
		},
		error: function (){
			alert ("error");
		}
	});
	
	Duplicados(id_medico, id_especialidad, fecha)
}

//CREAR AGENDA
function CrearAgenda(fecha = ''){
	var id_medico = $("#id_medico").val();
	var id_especialidad = $("#medicos_especialidades").val();
	var date = new Date();
	if(fecha == '') {
		fecha = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	}
	$.ajax({
		type: "POST",
		url: "../ajax/crear_agenda.php",
		data: {id_medico: id_medico, id_especialidad: id_especialidad, fecha: fecha },
		beforeSend: function() {
		},
		success: function(requestData){
			var rta = requestData;
			$("#contenedor_agenda").html(rta).fadeIn("slow");
			$("#btn_bajo_calendario_1").html('<a class="btn_opciones-1 btn" href="#" data-id="'+id_medico+'" data-tipo_btn="tabla_hija" data-hija="medicos_especialidades" data-nombre="Especialidades por M�dicos" alt="Especialidades por M�dicos" style="padding: 6px 0px 0px 6px;">Horarios<img src="https://ciacsaltadb.ddns.net/files/img/btns/medicos_horarios.png" border="0"></a>').fadeIn("slow");
			$("#btn_bajo_calendario_2").html('<a class="btn_opciones-1 btn" href="#" data-id="'+id_medico+'"  data-tipo_btn="tabla_hija" data-hija="medicos_obras_sociales" data-nombre="Planes de Obras Sociales por M�dicos" alt="Planes de Obras Sociales por M�dicos" style="padding: 6px 0px 0px 6px;">Obras Sociales<img src="https://ciacsaltadb.ddns.net/files/img/btns/medicos_obras_sociales.png" border="0"></a>').fadeIn("slow");
			$("#btn_bajo_calendario_3").html('<a class="btn_opciones-1 btn" href="#" data-id="'+id_medico+'" data-tipo_btn="tabla_hija" data-hija="medicos_estudios" data-nombre="Especialidades por M�dicos" alt="Especialidades por M�dicos" style="padding: 6px 0px 0px 6px;">Estudios<img src="https://ciacsaltadb.ddns.net/files/img/btns/medicos_estudios.png" border="0"></a>').fadeIn("slow");
			$('#agenda').datepicker("setDate", new Date(fecha) );
		},
		complete: function(requestData, exito){
		},
		error: function (){
			alert ("error");
		}
	});
	return false;
};