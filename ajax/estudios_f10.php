<div id="estudiosF10">
    <h1>Estudios <span>F10</span></h1>
    <div id="estudiosF10grilla">
        <table border="0" class="display" id="t_estudios_f10_grilla">
            <thead>
                <tr>
                    <th class="sorting">Estudio</th>
                    <th class="sorting">Importe</th>
                    <th class="sorting">Arancel</th>
                    <th class="sorting">Particular</th>
                    <th class="sorting">Obra Social</th>
                    <th class="sorting">Importe Consulta</th>
                    <th class="sorting">M&eacute;dico</th>
                    <th class="sorting">Particular Cosulta</th>
                    <th class="sorting">Arancel</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>
                        <input type="text" placeholder="Estudio" name="tb_f10_estudio" id="tb_f10_estudio" readonly="readonly" class="f10focus">
                    </th>
                    <th colspan="3"></th>
                    <th>
                        <input type="text" placeholder="Obra Social" name="tb_f10_obrasocial" id="tb_f10_obrasocial" readonly="readonly">
                    </th>
                </tr>
            </tfoot>
            <tbody></tbody>
        </table>
    </div>
    <script>
    $(function(){
        $.ajax(
            '../ajax/estudios_f10_grilla.php'
        ).done(function(data){
            $('#estudiosF10grilla > table > tbody').html(data);
        });
        $('#tb_f10_estudio, #tb_f10_obrasocial').keyup(function(){
            $.post(
                '../ajax/estudios_f10_grilla.php',
                {
                    tb_f10_estudio: $('#tb_f10_estudio').val(),
                    tb_f10_obrasocial: $('#tb_f10_obrasocial').val()
                }
            ).done(function(data){
                $('#estudiosF10grilla > table > tbody').html(data);
            });
        });
    })
    </script>
    <style>
    #estudiosF10{
        float:left;
        top:0;
        left:0;
        position:absolute;
        z-index:999999999;
        background-color:#f0f0f0;
        width:90%;
        padding:5px 5%;
        border-bottom:solid 6px #ddd;
    }
    #estudiosF10 *{
        font-family:"Trebuchet MS",​Arial,​Helvetica,​sans-serif;
    }
    #estudiosF10 h1{
        font-weight:bold;
        text-transform:uppercase;
        font-size:1.4em;
        margin:0;
        color:#007FA6;
    }
    #estudiosF10 h1 span{
        color:#008A47;
    }
    #estudiosF10 table{
        margin:0;
    }
    #estudiosF10 table thead th{
        color:#fff;
    }
    #estudiosF10 table thead th,
    #estudiosF10 table tbody tr td{
        padding:3px 10px;
        font-size:13px;
        font-weight:normal;
    }
    #estudiosF10 table input[type="text"]{
        background-color:#fff!important;
    }
    #estudiosF10 table input[type="text"].f10focus{
        border: solid 2px #008A47;
        box-shadow:2px 2px 4px #007FA6;
    }
    </style>
</div>
