<?php
class Menus{
	private $obj_estructura;

	function __construct(){
        requerir_class("estructura");
		$this->obj_estructura = new Estructura();
	}
	
 	public function armarMenu(){
        $htm_menu_tablas = $this->obj_estructura->html("menu/tablas_sas");
        if ($_SESSION["SUPERUSER"] == 3) {
            $tLi = '
            <li id="turnos_tipos_menu">
                <a href="#" class="btn_tabla" data-nombre="turnos_tipos">
                    <i class="fa fa-address-book"></i>
                    <span>Turnos Tipo</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_TURNOS_TIPOS", $tLi);
        }else{
            $htm_menu_tablas->Asigna("LI_TURNOS_TIPOS", "");
        }

        if($_SESSION["PERMISO_AGENDA"] == 1){
            $tLi='
            <li id="agenda_menu">
                <a href="#" class="btn_tabla" data-nombre="agendas">
                    <i class="fa fa-address-book"></i>
                    <span>Agenda</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_AGENDA", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_AGENDA", "");
        }
        
        if($_SESSION["PERMISO_COMUNICACION"] == 1 || $_SESSION["PERMISO_COMUNICADOS_GERENCIA"] == 1 || $_SESSION["PERMISO_NOVEDADES_DIARIAS"] == 1 || $_SESSION["PERMISO_NOTAS_IMPRECION"] == 1){
            $tLiSubmenu = false;
            $tLi='
            <li id="comunicacion_menu">
                <a href="#">
                    <i class="fa fa-newspaper-o"></i>
                    <span>Comunicaci&oacute;n</span>
                </a>';
        
            if($_SESSION["PERMISO_COMUNICADOS_GERENCIA"] == 1){
                $tLiSubmenu = true;
                $tLi.='
                <ul class="dl-submenu">
                    <li id="comunicados_gerencia_menu">
                        <a href="#" class="btn_tabla" data-nombre="novedades">
                            <i class="fa fa-newspaper-o"></i>
                            <span>Comunicados de Gerencia</span>
                        </a>
                    </li>';
            }
            if($_SESSION["PERMISO_NOVEDADES_DIARIAS"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="novedades_diarias_menu">
                    <a href="#" class="btn_tabla" data-nombre="novedades_diarias">
                        <i class="fa fa-newspaper-o"></i>
                        <span>Novedades Diarias</span>
                    </a>
                </li>';
            }
            if($_SESSION["PERMISO_NOTAS_IMPRECION"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="notas_imprecion_menu">
                    <a href="#" class="btn_tabla" data-nombre="notas_impresion">
                        <i class="fa fa-newspaper-o"></i>
                        <span>Notas de Impresión</span>
                    </a>
                </li>';
            }
            ($tLiSubmenu) ? $tLi.= '</ul>' : '';
            $tLi.='</li>';
        
            $htm_menu_tablas->Asigna("LI_COMUNICACION", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_COMUNICACION", "");
        }
        
        if($_SESSION["PERMISO_ENCUESTAS"] == 1){
            $tLi='
            <li id="encuestas_menu">
                <a href="#" class="btn_tabla" data-nombre="encuestas">
                    <i class="fa fa-list-ol"></i>
                    <span>Encuestas</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_ENCUESTAS", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_ENCUESTAS", "");
        }
        
        if($_SESSION["PERMISO_ESPECIALIDADES"] == 1){
            $tLi='
            <li id="especialidades_menu">
                <a href="#" class="btn_tabla" data-nombre="especialidades">
                    <i class="fa fa-stethoscope"></i>
                    <span>Especialidades</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_ESPECIALIDADES", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_ESPECIALIDADES", "");
        }
        
        if($_SESSION["PERMISO_ESTUDIOS"] == 1){
            $tLi='
            <li id="estudios_menu">
                <a href="#" class="btn_tabla" data-nombre="estudios">
                    <i class="icon-medical-microscope"></i>
                    <span>Estudios</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_ESTUDIOS", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_ESTUDIOS", "");
        }
        
        if($_SESSION["PERMISO_MANTENIMIENTO"] == 1){
            $tLi='
            <li id="mantenimiento_menu">
                <li id="mantenimiento_reciente_menu">
                    <a href="#" class="btn_tabla" data-nombre="mantenimientos">
                        <i class="fa fa-cogs"></i>
                        <span>Mantenimiento</span>
                    </a>
                </li>
            </li>';
            $htm_menu_tablas->Asigna("LI_MANTENIMIENTO", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_MANTENIMIENTO", "");
        }
        
        if($_SESSION["PERMISO_MEDICOS"] == 1 || $_SESSION["PERMISO_MEDICOS_CIAC"] == 1 || $_SESSION["PERMISO_MEDICOS_EXTERNOS"] == 1 || $_SESSION["PERMISO_MEDICOS_EMPRESAS"] == 1){
            $tLiSubmenu = false;
            $tLi='
            <li id="medicos_menu">
                <a href="#">
                    <i class="fa fa-user-md"></i>
                    <span>M&eacute;dicos</span>
                </a>';
        
            if($_SESSION["PERMISO_MEDICOS_CIAC"] == 1){
                $tLiSubmenu = true;
                $tLi.='
                <ul class="dl-submenu">
                    <li id="medicos_ciac_menu">
                        <a href="#" class="btn_tabla" data-nombre="medicos">
                            <i class="fa fa-user-md"></i>
                            <span>M&eacute;dicos CIAC</span>
                        </a>
                    </li>';
            }
            if($_SESSION["PERMISO_MEDICOS_EXTERNOS"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="medicos_externos_menu">
                    <a href="#" class="btn_tabla" data-nombre="medicosext">
                        <i class="fa fa-user-md"></i>
                        <span>M&eacute;dicos Externos</span>
                    </a>
                </li>';
            }
            if($_SESSION["PERMISO_MEDICOS_EMPRESAS"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="medicos_empresas_menu">
                    <a href="#" class="btn_tabla" data-nombre="medicosexp">
                        <i class="fa fa-user-md"></i>
                        <span>M&eacute;dicos Expensas</span>
                    </a>
                </li>';
            }
            ($tLiSubmenu) ? $tLi.= '</ul>' : '';
            $tLi.='</li>';
        
            $htm_menu_tablas->Asigna("LI_MEDICOS", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_MEDICOS", "");
        }
        
        if($_SESSION["PERMISO_OBRAS_SOCIALES"] == 1){
            $tLi='
            <li id="oobras_sociales_menu">
                <a href="#" class="btn_tabla" data-nombre="obras_sociales">
                    <i class="fa fa-id-card-o"></i>
                    <span>Obras Sociales</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_OBRASSOCIALES", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_OBRASSOCIALES", "");
        }
        
        if($_SESSION["PERMISO_PACIENTES"] == 1){
            $tLi='
            <li id="pacientes_menu">
                <a href="#" class="btn_tabla" data-nombre="pacientes">
                    <i class="fa fa-group"></i>
                    <span>Pacientes</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_PACIENTES", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_PACIENTES", "");
        }
        
        if($_SESSION["PERMISO_PLANES_CONTINGENCIA"] == 'desactivado'){
            $tLi='
            <li id="planes_contingencia_manu">
                <a href="#" class="btn_tabla" data-nombre="planes_de_contingencia">
                    <i class="fa fa fa-newspaper-o"></i>
                    <span>Planes de contingencia</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_PLANES_CONTINGENCIA", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_PLANES_CONTINGENCIA", "");
        }
        
        if($_SESSION["PERMISO_PRACTICAS_MEDICAS"] == 1){
            $tLi='
            <li id="practicas_medicas_menu">
                <a href="#" class="btn_tabla" data-nombre="diagnosticos">
                    <i class="fa fa-medkit"></i>
                    <span>Pr&aacute;cticas M&eacute;dicas</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_PRACTICAS_MEDICAS", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_PRACTICAS_MEDICAS", "");
        }
        
        if($_SESSION["PERMISO_SECTORES"] == 1 || $_SESSION["PERMISO_SECTORES_UNO"] == 1 || $_SESSION["PERMISO_SUBSECTORES"] == 1 || $_SESSION["PERMISO_CONSULTORIOS"] == 1 || $_SESSION["PERMISO_DISPONIBILIDADES"] == 1){
            $tLiSubmenu = false;
            $tLi='
            <li id="sectores_menu">
                <a href="#">
                    <i class="fa fa-hospital-o"></i>
                    <span>Sectores / Subsectores</span>
                </a>';
        
            if($_SESSION["PERMISO_SECTORES_UNO"] == 1){
                $tLiSubmenu = true;
                $tLi.='
                <ul class="dl-submenu">
                    <li id="sectores_uno_menu">
                        <a href="#" class="btn_tabla" data-nombre="sectores">
                            <i class="fa fa-hospital-o"></i>
                            <span>Sectores</span>
                        </a>
                    </li>';
            }
            if($_SESSION["PERMISO_SUBSECTORES"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="subsectores_menu">
                    <a href="#" class="btn_tabla" data-nombre="subsectores">
                        <i class="fa fa-hospital-o"></i>
                        <span>Subsectores</span>
                    </a>
                </li>';
            }
            if($_SESSION["PERMISO_CONSULTORIOS"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="consultorios_menu">
                    <a href="#" class="btn_tabla" data-nombre="consultorios">
                        <i class="fa fa-hospital-o"></i>
                        <span>Consultorios</span>
                    </a>
                </li>';
            }
            if($_SESSION["PERMISO_DISPONIBILIDADES"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="disponibilidades_menu">
                    <a href="#" class="btn_tabla" data-nombre="disponibilidades">
                        <i class="fa fa-hospital-o"></i>
                        <span>Disponibilidades</span>
                    </a>
                </li>';
            }
            ($tLiSubmenu) ? $tLi.= '</ul>' : '';
            $tLi.='</li>';
        
            $htm_menu_tablas->Asigna("LI_SECTORES", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_SECTORES", "");
        }
        
        if($_SESSION["PERMISO_TAREAS"] == 'desactivado' || $_SESSION["PERMISO_TAREAS_CONFIGURACION"] == 'desactivado'|| $_SESSION["PERMISO_TAREAS_PENDIENTES"] == 'desactivado'){
            $tLiSubmenu = false;
            $tLi='
            <li id="tareas_menu">
                <a href="#">
                    <i class="fa fa-list-ol"></i>
                    <span>Tareas</span>
                </a>';
        
            if($_SESSION["PERMISO_TAREAS_CONFIGURACION"] == 1){
                $tLiSubmenu = true;
                $tLi.='
                <ul class="dl-submenu">
                    <li id="tareas_configuracion_menu">
                        <a href="#" class="btn_tabla" data-nombre="tareas_configuracion">
                            <i class="fa fa-cogs"></i>
                            <span>Configuraci&oacute;n</span>
                        </a>
                    </li>';
            }
            if($_SESSION["PERMISO_TAREAS_PENDIENTES"] == 1){
                ($tLiSubmenu) ? '' : $tLi.='<ul class="dl-submenu">';
                $tLiSubmenu = true;
                $tLi.=' 
                <li id="tareas_pendientes_menu">
                    <a href="#" class="btn_tabla" data-nombre="tareas_pedidos">
                        <i class="fa fa-list-ol"></i>
                        <span>Tareas Pendientes</span>
                    </a>
                </li>';
            }
        
            ($tLiSubmenu) ? $tLi.= '</ul>' : '';
            $tLi.='</li>';
        
            $htm_menu_tablas->Asigna("LI_TAREAS", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_TAREAS", "");
        }
        
        if($_SESSION["PERMISO_USUARIOS_PERMISO"] == 1){
            $tLi='
            <li id="usuarios_permiso_menu">
                <a href="#" class="btn_tabla" data-nombre="usuarios">
                    <i class="fa fa-group"></i>
                    <span>Usuarios</span>
                </a>
            </li>';
            $htm_menu_tablas->Asigna("LI_USUARIO_MENU", $tLi);
        }
        else{
            $htm_menu_tablas->Asigna("LI_USUARIO_MENU", "");
        }

  		return $htm_menu_tablas;
  	}
}
?>