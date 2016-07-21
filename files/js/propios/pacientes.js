//PACIENTES:
//----Buscar

$(function(){
	//FUNCION PARA BUSCAR PACIENTES ANTES DE ASIGNAR UN TURNO
	$("#btn_buscar_paciente").on("click", function(event){
        event.preventDefault();
		var dni = $("#dni").val();
		if (dni != ""){
			$.ajax({  
				type: "POST",   
				url: "../ajax/buscar_paciente.php",					
				data: {dni: dni}, 
				beforeSend: function() {
				},
				success: function(requestData){
					var rta = requestData;
					$("#contenedor_paciente").html(rta).fadeIn("slow", function(){
						//VERIFICO  SI ESTA CARGADO EL MEDICO PARA COMPROBAR SI EL MEDICO TRABAJA CON LA OS DEL PACIENTE
						if ($('#id_medico').val() != ''){
							var id_medico = $('#id_medico').val();
							var id_os = $('#panel_paciente #id_obras_sociales').val();
							var id_plan = $('#panel_paciente #id_obras_sociales_planes').val();
							
							TrabajaConOS(id_medico, id_os, id_plan);
						} else {
                            $('#cont_grilla_turnos').html('');
						}
					});
				},
				complete: function(requestData, exito){
				},
				error: function (){
					alert ("error");
				}
			});
		}else{
			alert("Debe ingresar DNI del paciente", 'ATENCIÓN');	
		}
	});
    $('#myMenuJQ a').click(function(event){
        event.preventDefault();
		if ($('#id_medico').val() == ''){
            $('#cont_grilla_turnos').html('');
        }
    });
});