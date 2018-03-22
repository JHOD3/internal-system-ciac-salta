<?php if (count($aMedicos) > 0): ?>
    <div class="row margin-10">
        <?php foreach ($aMedicos AS $rsM): ?>
            <div class="col-sm-6 col-md-4">
                <?php if (count($rsM['especialidades']) == 1): ?>
                    <?php foreach ($rsM['especialidades'] AS $rwEsp): ?>
                        <a
                            href="#"
                            class="onClickButtonAgenda"
                            data-id_medicos="<?=$rsM['id_medicos']?>"
                            data-id_especialidades="<?=$rwEsp['id_especialidades']?>"
                        >
                            <div class="txtBig">
                                <span class="fa fa-user-md" aria-hidden="true"></span>
                                <strong><?=doSaludo($rsM, false)?></strong>
                            </div>
                            <div>
                                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                <?=upper($rwEsp['nombre'])?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a
                        href="#"
                        class="onClickButtonMedico"
                    >
                        <div class="txtBig">
                            <span class="fa fa-user-md" aria-hidden="true"></span>
                            <strong><?=doSaludo($rsM, false)?></strong>
                        </div>
                    </a>
                    <div>
                        <?php foreach ($rsM['especialidades'] AS $rwEsp): ?>
                            <div>
                                <a
                                    href="#"
                                    class="onClickButtonAgenda"
                                    data-id_medicos="<?=$rsM['id_medicos']?>"
                                    data-id_especialidades="<?=$rwEsp['id_especialidades']?>"
                                >
                                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                    <?=upper($rwEsp['nombre'])?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="clearfloat30"></div>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
    $(function(){
        $('.onClickButtonMedico').click(function(event){
            event.preventDefault();
            $(this).next().prepend('<div>Debes elegir una de sus agendas:</div>');
        });
        $('.onClickButtonAgenda').click(function(event){
            event.preventDefault();
            id_especialidades = $(this).data('id_especialidades');
            id_medicos = $(this).data('id_medicos');
            $('#id_medicos').val(id_medicos);
            $('#id_especialidades').val(id_especialidades);
            $('#divLoading').html('<div class="opacityBackground"><div class="loading"><img src="assets/images/loading.gif" alt="" /></div></div>');
            if (typeof ajxM != "undefined" && ajxM && ajxM.readyState != 4){
                ajxM.abort();
            }
            ajxM = $.ajax({
                url:
<?php if ($_SERVER['HTTP_HOST'] == 'localhost' or $_SERVER['HTTP_HOST'] == '192.168.0.10'): ?>
                    'http://<?=$_SERVER['HTTP_HOST']?>/dgadmin/dgadmin2016/ciac/sistema/tajax/index.php/turnero/calendar/' +
<?php else: ?>
                    'http://ciacsaltadb.ddns.net/tajax/index.php/turnero/calendar/' +
<?php endif; ?>
                    id_especialidades +
                    '/' +
                    id_medicos +
                    '/<?=isset($post['fecha']) ? date("Y/m/d", strtotime($post['fecha'])) : date("Y/m/d")?>'
                    <?php if (isset($post['desde'])): ?>
                        +'/<?=$post['desde']?>'
                    <?php endif; ?>
                ,
                context: document.body
            }).done(function(data) {
                $('#calendar').html(data);
                $('#divLoading').html('');
            }).fail(function(jqXHR, textStatus, errorThrown ){
                if (textStatus == 'abort'){
                    $('#calendar').html('<div style="width:100%;text-align:center;">Espere unos segundos por favor...</div>');
                } else {
                    $('#calendar').html('<div style="width:100%;text-align:center;">Hubo un problema de conexión. Por favor reintente mas tarde.</div>');
                    $('#divLoading').html('');
                }
            });
        });
<?php if (count($aMedicos) == 1 and count($aMedicos[0]['especialidades']) == 1): ?>
        setTimeout(function(){
            $('.onClickButtonAgenda').click();
        }, 100);
<?php endif; ?>
    });
    </script>
<?php else: ?>
    <div class="row margin-10">
        <div class="col-md-6">
            No se encontraron resultados con los filtros seleccionados
        </div>
    </div>
<?php endif; ?>
