<?php
require_once 'view/topo.php';
$empresa = $_SESSION['codigo'];
$perfil = $_SESSION['perfil'];
$sql = executaSql("SELECT * FROM pfilho WHERE nccodigo = $empresa AND pcodigo = $perfil AND pftela LIKE '1.3'");

while ($reg = pg_fetch_array($sql)) {
    $adm = $reg['pfadmin'];
    $ver = $reg['pfativo'];
    if ($reg['pfinclui'] === '1') {
        $incluir = $reg['pfinclui'];
    }
    if ($reg['pfaltera'] === '1') {
        $editar = $reg['pfaltera'];
    }
    if ($reg['pfexclui'] === '1') {
        $excluir = $reg['pfexclui'];
    }
    if ($reg['pfpdf'] === '1') {
        $pdf = $reg['pfpdf'];
    }
    if ($reg['pfexcel'] === '1') {
        $excel = $reg['pfexcel'];
    }
}

$sql = executaSql("SELECT pfinclui FROM pfilho WHERE nccodigo = $empresa AND pcodigo = $perfil AND pftela LIKE '1.4'");

while ($reg = pg_fetch_array($sql)) {
    if ($reg['pfinclui'] === '1') {
        $incluirNFE = $reg['pfinclui'];
    }
}
if (isset($ver)) {
    ?>
    <script src="../../js/countUp.min.js"></script>
    <link href="../../css/bmstyle.css" rel="stylesheet"/>
    <div class="container">
        <div class="page-header listagem">
            <div class="row">
                <div class="col-md-6">
                    <h3>Venda
                        <small class="listagem"> // Listagem</small>
                    </h3>
                </div>
                <div id="exibir" class="col-md-1" style="padding: 0px">
                    <button class="btn btn-default dropdown-toggle grid" id="btnMostrar" data-toggle="dropdown" aria-expanded="false" style="margin-left: -38px;">Mostrar<span class="caret"></span></button>
                    <ul class="dropdown-menu consulta-dropdown dropdown-menu-form menuExibir" role="menu">
                        <li><a href="#"> <label for="todos"><input type="checkbox" name="todos" id="todos" class="marcar" checked style="margin-right: 15px">Todos</label></a></li>
                        <li><a href="#"> <label><input type="checkbox" id="faturada" class="marcar" value="" style="margin-right: 15px">Faturada</label></a></li>
                        <li><a href="#"> <label><input type="checkbox" id="naofaturada" class="marcar" value="" style="margin-right: 15px">Não faturada</label></a></li>
                        <li>
                            <button id="aplicaMostrar" class="btn btn-success" data-toggle="dropdown"
                                    aria-expanded="false">Aplicar
                            </button>
                        </li>
                    </ul>
                </div>                  
                <div id="periodos" class="col-md-4 pull-right" style="padding: 0px; text-align: right">
                    <button class="btn btn-default glyphicon glyphicon-chevron-left grid" id="retrocedePeriodo"
                            type="button" aria-expanded="false" style="margin: 0px"></button>
                    <button class="btn btn-default glyphicon grid" id="bntIntervalos" data-toggle="dropdown"
                            aria-expanded="false" style="margin: 0px">
                        <div id="datas" style="font-family: 'Open Sans'"></div>
                    </button>

                    <ul class="dropdown-menu menuPeriodos" role="menu" style="top: 49px;left: 75px;">
                        <li><a href="#" id="btnHJ"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Hoje</a>
                        </li>
                        <li><a href="#" id="btnSem"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Essa
                                Semana</a></li>
                        <li><a href="#" id="btnMes"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Esse
                                Mês</a></li>
                        <li><a href="#" id="btnAno"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Esse
                                Ano</a></li>
                        <legend></legend>
                        <li><a href="#" id="btnSelPeriodo"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Selecionar
                                Periodo</a></li>
                        <li><a href="#" id="btnTudo"> <i class="fa fa-calendar" style="margin-right: 5px"> </i>Todos</a>
                        </li>
                    </ul>
                    <button class="btn btn-default glyphicon glyphicon-chevron-right grid" id="avancaPeriodo"
                            type="button" aria-expanded="false"></button>
                </div>

                <div id="selecionaPeriodos" class="pull-right"
                     style="display:none;position: relative;background: #FFF none repeat scroll 0% 0%;width: 341px;box-shadow: 0px 4px 6px 3px rgba(107, 107, 107, 0.34);z-index: 11111111;left: 2px;padding: 20px 22px 20px 7px;margin-bottom: -205px;">
                    <div clas class="col-md-12">
                        <div class="col-md-12"
                             style="padding: 8px 8px 22px 0px;color: #00496D;font-weight: normal;font-size: 14px;text-align: center;text-transform: uppercase;">
                            <span
                                style="margin: auto;display: block;width: 148px;text-align: center;">Digite um período</span>
                        </div>
                        <div clas class="col-md-6" style="padding: 5px;">
                            <input type="text" id="inicioPeriodo" class="form-control" style="font-weight: normal">
                        </div>

                        <div clas class="col-md-6" style="padding: 5px;">
                            <input type="text" id="fimPeriodo" class="form-control" style="font-weight: normal"/>
                        </div>
                        <div class="btn-group grid">
                            <div class="col-md-12" style="padding: 5px;">
                                <button type="button" id="aplicaPeriodo" class="btn btn-success"
                                        style="width: 287px ! important;border-radius: 2px;border: 0px none;">Aplicar
                                </button>
                            </div>
                            <div class="col-md-12" style="padding: 5px;">
                                <button type="button" id="cancelar" class="btn btn-warning"
                                        style="width: 287px ! important;margin-top: -5px;">Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="mensagens">
            <!-- Mensagem status acoes -->
            <?php if ($statusMessage = flash("statusMessage")) { ?>
                <div class="<?php echo $statusMessage->getTipo() ?>" role="alert">
                    <strong><?php echo $statusMessage->getAssunto() ?></strong>
                    <?php echo $statusMessage->getMensagem() ?>
                </div>
            <?php } ?>
        </div>

        <div id="conteudo">
            <div id="grid-toolbar">
                <?php
                if (isset($incluir)) {
                    ?>
                    <a class="btn btn-success grid" href="<?php echo "$URL_BASE_PATH/modulos/venda/insereform.php" ?>"
                       id="btnAdicionar">
                        <i class="glyphicon glyphicon-plus"></i> Adicionar
                    </a>
                    <?php
                }
                if (isset($editar)) {
                    ?>
                    <button class="btn btn-primary grid" type="button" id="btnAlterar" disabled="disabled">
                        <i class="glyphicon glyphicon-pencil"></i> Alterar
                    </button>
                    <?php
                }
                if (isset($excluir)) {
                    ?>
                    <button class="btn btn-danger grid" type="button" id="btnExcluir" disabled="disabled">
                        <i class="glyphicon glyphicon-trash"></i> Excluir
                    </button>
                    <?php
                }
                if (isset($editar)) {
                    ?>
                    <button class="btn btn-danger grid btnToolTip" type="button" id="desatualizaNota"
                            disabled="disabled" title="Desatualizar Venda" data-placement="top" data-toggle="tooltip">
                        <i class="glyphicon glyphicon-cog"></i>
                    </button>
                    <!--            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle grid" data-toggle="dropdown">
                                        Ações <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#" id="btnNFE"> <i class="glyphicon glyphicon-list-alt"> </i> Emitir Nota Fiscal</a></li>                  
                                    </ul>
                                </div>-->
                    <?php
                }
                if (isset($incluirNFE)) {
                    ?>
                    <button class="btn btn-success grid btnToolTip" type="button" id="btnNFE" disabled="disabled"
                            title="Gerar Nota Fiscal" data-placement="top"
                            data-toggle="tooltip">
                        NF-e
                    </button>
                    <?php
                }
                if (isset($pdf)) {
                    ?>
                    <button type="button" class="btn btn-primary fa fa-print grid btnToolTip" id="bntImprimePDF"
                            title="Gerar PDF" data-placement="top" data-toggle="tooltip">
                    </button>
                    <?php
                }
                if (isset($excel)) {
                    ?>
                    <button type="button" class="btn btn-primary fa fa-table grid btnToolTip" id="bntImprimeXLS"
                            data-toggle="dropdown" title="Gerar Planilha" data-placement="top" data-toggle="tooltip">
                    </button>
                    <?php
                }
                ?>
                <button type="button" class="btn btn-primary glyphicon glyphicon-duplicate grid btnToolTip"
                        id="btnCopiar" data-toggle="dropdown" title="Copiar Venda" data-placement="top"
                        data-toggle="tooltip" disabled>
                </button>
            </div>

            <table id="grid-consulta" class="table table-hover" style="margin-top: 7px;"
                   data-url="<?php echo "$URL_BASE_PATH/modulos/venda/consultaJson.php" ?>"
                   data-cache="false"
                   data-click-to-select="true"
                   data-pagination="true"
                   data-side-pagination="server"
                   data-search="true"
                   data-toolbar="#grid-toolbar"
                   data-cookie="true"
                   data-cookie-id-table="vendaservico"
                   data-page-list="[5, 10, 20, 50, 100, 200, All]">
                <thead>
                <tr class="success">
                    <th data-field="state" data-checkbox="true"></th>
                    <th data-field="codigo" data-sortable="true" class="col-md-1">Nro</th>
                    <th data-field="nomeCliente" data-sortable="true" style="width: 30%;">Cliente</th>
                    <th data-field="dataPedido" data-sortable="true">Data Pedido</th>
                    <th data-field="prazoEntrega" data-sortable="true" style="width: 30%;">Prazo Entrega</th>
                    <th data-field="valorTotal" data-sortable="true">Valor Total</th>
                    <th data-field="numeroOS" data-sortable="true">OS</th>
                    <th data-field="atualizada1"
                        class="centraliza campoPersonalizado col-md-1 atualiza">Atualizada
                    </th>
                    <th data-field="statusDescricao" class="alinha-centro consulta-2">NF-e Emitida</th>
                    <!--<th data-field="vnfnumero" data-sortable="true" class="alinha-centro">Seq</th>-->
                    <th data-field="vnfnumero2" class="alinha-centro">Nota</th>
                    <!--<th data-field="vnfnumero" data-sortable="true" class="alinha-centro">Nota</th>-->
                    <th data-field="email" data-sortable="false" class=""></th>
                    <th data-field="relatorio" data-sortable="false" class=""></th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="row" style="margin: 0px; margin-bottom: 15px">
            <div class="col-md-2 pull-right" style="padding: 15px;color: black;padding-bottom: 5px;padding-top: 0px">
                <div class="row legendaValor">
                    Total Geral:
                </div>
                <div class="row valor">
                    <div class="legendaValorReal"><?php echo "R$ "; ?></div>
                    <div id="valortotal">0,00</div>
                </div>
            </div>
        </div>        
    </div>
    <div id="modal1" class="modal modal-fixed-footer">
        <div class="modal-content">
            <div class="page-header adicionar">
                <h3>Venda de Produto
                    <small class="adicionar"> // Enviar por E-mail</small>
                </h3>
            </div>
            <div class="container" style="width: 100%;max-width: 980px;margin-bottom: 50px;">
                <div id="conteudo">
                    <form id="formEnviaEmail" method="post" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo $URL_BASE_PATH ?>/modulos/empresa/email.php"><i
                                        class="glyphicon glyphicon-cog" style="float: right; font-size: 20px;"
                                        title="Configuração de E-mail"></i></a>
                            </div>
                            <div class="col-md-12">
                                <label data-toggle="tooltip" title="*Separe os e-mails por virgula">Para:*</label>
                                <input data-toggle="tooltip" title="*Separe os e-mails por virgula" type="text"
                                       style="margin-bottom: 10px;" class="form-control" id="para" name="para"
                                       data-campo="" placeholder="Ex: exemplo1@dominio.com.br," required>
                                <input type="text" style="margin-bottom: 10px;display: none;" class="form-control"
                                       id="codigo" name="codigo">
                            </div>
                            <div class="col-md-12">
                                <label data-toggle="tooltip" title="*Separe os e-mails por virgula">CC:</label>
                                <input data-toggle="tooltip" title="*Separe os e-mails por virgula" type="text"
                                       style="margin-bottom: 10px;" class="form-control" name="cc" id="cc"
                                       data-campo="cc" placeholder="Ex: exemplo2@dominio.com.br,">
                            </div>

                            <div class="col-md-12">
                                <label data-toggle="tooltip" title="*Assunto da mensagem">Assunto:*</label>
                                <input data-toggle="tooltip" title="*Assunto da Mensagem" type="text"
                                       style="margin-bottom: 10px;" class="form-control" name="assunto" id="assunto"
                                       data-campo="cc" placeholder="Ex: Vendas" required>
                            </div>
                            <div class="col-md-12">
                                <label data-toggle="tooltip" title="*Mensagem">Mensagem:</label>
                                <div class="message">
                                    <textarea id="mensagem" class="form-control" name="msg"
                                              style="height: 100px;"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="botoes" class="col-md-12">
                                <div id="botoes-item" style="float: left">
                                    <input type="submit" name="enviar" class="btn btn-success" value="Enviar E-mail">
                                </div>
                                <button type="button" style="float: right; width: 150px; height: 45px;"
                                        class="btn btn-primary pull-right modal-close">
                                    <i class="mdi-navigation-arrow-back"
                                       style="font-size: 20px;margin-left: -29px;position: absolute;margin-top: -6px;"></i>Voltar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-acoes" class="modal modal-fixed-footer" style="max-width: 500px;">
        <div class="modal-content" style="height: 100%;">
            <?php
            if (isset($pdf)) {
                ?>
                <button type="button" id="bntImprimePDFResp" class="modal-col-content">
                    <div>
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        <br/>
                        <span>Gerar PDF</span>
                    </div>
                </button>
                <?php
            }
            if (isset($excel)) {
                ?>
                <button type="button" id="bntImprimeXLSResp" class="modal-col-content">
                    <div>
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        <br/>
                        <span>Gerar Planilha</span>
                    </div>
                </button>
                <?php
            }
            ?>
            <?php
            if (isset($editar)) {
                ?>

                <button type="button" id="desatualizaNotaResp" class="modal-col-content" disabled="disabled">
                    <div>
                        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                        <br/>
                        <span>Desatualizar Nota</span>
                    </div>
                </button>
                <?php
            }
            ?>
            <?php
            if (isset($incluirNFE)) {
                ?>
                <button type="button" id="btnNFEResp" class="modal-col-content" disabled="disabled">
                    <div>
                        NF-E
                        <br/>
                        <span>Gerar Nota Fiscal</span>
                    </div>
                </button>
                <?php
            }
            ?>
            <button type="button" href="#" id="btnCopiarResp" disabled="disabled" class="modal-col-content">
                <div>
                    <i class="glyphicon glyphicon-duplicate" aria-hidden="true"></i>
                    <br/>
                    <span>Copiar</span>
                </div>
            </button>
        </div>
    </div>
    <div class="fixed-action-btn click-to-toggle actionButton" style="bottom: 20px; right: 24px;display: none">
        <a class="btn-floating btn-large blueNx">
            <i class="glyphicon glyphicon-plus" style="left: 1px;"></i>
        </a>
        <ul>
            <?php
            if (isset($incluir)) {
                ?>
                <li><a href="<?php echo "$URL_BASE_PATH/modulos/venda/insereform.php" ?>" class="btn-floating green"
                       id=""><i class="glyphicon glyphicon-plus" style="left: 0.5px;"></i></a></li>
                <?php
            }
            if (isset($editar)) {
                ?>
                <li>
                    <button type="button" class="btn-floating blueEdit" id="btnAlterarResp" disabled="disabled"><i
                            class="glyphicon glyphicon-pencil"></i></button>
                </li>
                <?php
            }
            if (isset($excluir)) {
                ?>
                <li>
                    <button type="button" class="btn-floating red" disabled="disabled" id="btnExcluirResp"><i
                            class="glyphicon glyphicon-trash"></i></button>
                </li>
                <?php
            }
            ?>
            <li><a class="btn-floating blueNx" id="acoesBtn"><i class="fa fa-ellipsis-h"></i></a></li>
            <li><a class="btn-floating blueNx" id="acoesBtn"><i class="fa fa-question"></i></a></li>
        </ul>
    </div>
    <?php
    $sql = executaSql("SELECT * FROM nossocliente WHERE nccodigo = $1", [$_SESSION["codigo"]]);
    $dadosEmpresa = getLinhaQuery($sql);
    
    $sql = executaSql("SELECT uncflagos FROM usuarionossocliente WHERE nccodigo = $1 AND unccodigo = $2", [$_SESSION['codigo'], $_SESSION['codigoUsuario']]);
    $flagOS = getLinhaQuery($sql)->uncflagos;    
    ?>
    <script type="text/javascript">
        function atualizaValores(periodo, periodo2, exibir ,$strBusca, usuario) {
            if (periodo !== '' && periodo2 !== '') {
                periodo = periodo.format("YYYY-MM-DD");
                periodo2 = periodo2.format("YYYY-MM-DD");
            }
            if (usuario === '') {
                usuario = null;
            }
            var options = {
                useEasing: true,
                useGrouping: true,
                separator: '.',
                decimal: ','
            };
            jQuery.ajax({
                type: "POST",
                url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/lista.php",
                dataType: 'json',
                data: jQuery.param({
                    de: periodo,
                    ate: periodo2,
                     exibir: exibir,
                    busca: $strBusca,
                    usuario: usuario
                }),
                success: function (resposta) {
                    var demo = new countUp("valortotal", 0, resposta, 2, 1.0, options);
                    demo.start();
                }
            });
        }        
        jQuery(function ($) {
            $('.btnToolTip').tooltip();
            var $gridConsulta = $('#grid-consulta'),
                $busca = $('#busca'),
                $formBusca = $('#formBusca'),
                $btnRel = $('#bntRelatorios'),
                $btnEmitirPDF = $('#bntImprimePDF'),
                $btnEmitirXLS = $('#bntImprimeXLS'),
                $btnExcluir = $('#btnExcluir'),
                $btnNFE = $('#btnNFE'),
                $btnCopiar = $('#btnCopiar'),
                $btnAlterar = $('#btnAlterar'),
                $desatualizaNota = $('#desatualizaNota'),
                $acoesBtn = $('#acoesBtn'),
                $btnRelResp = $('#bntRelatoriosResp'),
                $btnEmitirPDFResp = $('#bntImprimePDFResp'),
                $btnEmitirXLSResp = $('#bntImprimeXLSResp'),
                $btnExcluirResp = $('#btnExcluirResp'),
                $btnNFEResp = $('#btnNFEResp'),
                $btnCopiarResp = $('#btnCopiarResp'),
                $btnAlterarResp = $('#btnAlterarResp'),
                $desatualizaNotaResp = $('#desatualizaNotaResp'),
                $msgs = $('#mensagens');
            var $tipoPeriodo = ''; //Variavel para saber se é ano, mês, semana...
            var $intervalo = '';
            var $intervalo2 = '';
            var $exibir = 'T';            
            (function () {
                $(document).on('click', ".email", function () {
                    $selections = $gridConsulta.bootstrapTable("getSelections");
                    $(".message").find("#mensagem").remove().html('<textarea id="mensagem" class="form-control" name="msg" style="height: 100px;"></textarea>');
                    $(".message").html('<textarea id="mensagem" class="form-control" name="msg" style="height: 100px;"></textarea>');
                    $.post("<?= "$URL_BASE_PATH/modulos/venda/email/getEmail.php" ?>", {venda: $selections[0].codigo}, function (retorno) {
                        retorno = JSON.parse(retorno);
                        $emails = "";
                        $nome = "";
                        $.map(retorno, function (dadosCli) {
                            if (dadosCli.email) {
                                $emails += dadosCli.email + ", ";
                            }
                            $nome = dadosCli.nome;
                        });
                        $("#para").val($emails);
                        $("#codigo").val($selections[0].codigo);

                        $mensagem = '<?=$dadosEmpresa->ncmensagemprodserv?>';
                        $assunto = '<?=$dadosEmpresa->ncassuntoprodserv?>';
                        if ($mensagem.length === 0) {
                            $mensagem = "<p>Olá " + $nome + ",</p><p>Segue em anexo a venda de produto número " + $selections[0].codigo + ".</p><p>Abraços...</p><p><?= $_SESSION["nomeEmpresa"] ?></p>";
                        } else {
                            $mensagem = $mensagem.replace("{numero}", $selections[0].codigo);
                            $mensagem = $mensagem.replace("{cliente}", $nome);
                            $mensagem = $mensagem.replace("{tipo}", "Venda de Produto");
                            $mensagem = $mensagem.replace("{minhaempresa}", '<?=$_SESSION["nomeEmpresa"]?>');
                        }
                        if ($assunto.length === 0) {
                            $assunto = "Venda de Produto Numero " + $selections[0].codigo;
                        } else {
                            $assunto = $assunto.replace("{numero}", $selections[0].codigo);
                            $assunto = $assunto.replace("{tipo}", "Venda de Produto");
                        }
                        $("#mensagem").html($mensagem);
                        $("#assunto").val($assunto);

                        tinymce.init({
                            noneditable_leave_contenteditable: true,
                            selector: "#mensagem",
                            theme: "modern",
                            language: "pt_BR",
                            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print link | forecolor backcolor emoticons",
                            plugins: [
                                "advlist autolink link image lists charmap print hr anchor pagebreak spellchecker",
                                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                                "save table contextmenu directionality emoticons template paste textcolor noneditable"
                            ]
                        });
                    });
                    $("#modal1").openModal();
                    $gridConsulta.bootstrapTable("uncheckAll");
                });
                $("#formEnviaEmail").submit(function () {
                    ajax({
                        url: "<?= "$URL_BASE_PATH/modulos/venda/email/envia.php" ?>",
                        urlRetorno: "<?= "$URL_BASE_PATH/modulos/venda/consulta.php" ?>",
                        dados: $(this).serialize(),
                        msgSucesso: "E-mail enviado com sucesso!"
                    });
                    return false;
                });
                if ($.cookie('intervalo') || $.cookie('intervalo2')) {
                    $intervalo = moment($.cookie('intervalo'));
                    $intervalo2 = moment($.cookie('intervalo2'));
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                } else {
                    $('#datas').text('Todos');
                }

                //Hoje
                $('#btnHJ').click(function () {
                    $intervalo = moment().startOf('day');
                    $intervalo2 = moment().endOf('day');
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                    $.cookie('intervalo', $intervalo);
                    $.cookie('intervalo2', $intervalo2);
                    $tipoPeriodo = 'day';
                    $.cookie('tipoPeriodo', $tipoPeriodo);
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Semana
                $('#btnSem').click(function () {
                    $intervalo = moment().startOf('week');
                    $intervalo2 = moment().endOf('week');
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                    $.cookie('intervalo', $intervalo);
                    $.cookie('intervalo2', $intervalo2);
                    $tipoPeriodo = 'week';
                    $.cookie('tipoPeriodo', $tipoPeriodo);
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Mês
                $('#btnMes').click(function () {
                    $intervalo = moment().startOf('month');
                    $intervalo2 = moment().endOf('month');
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                    $.cookie('intervalo', $intervalo);
                    $.cookie('intervalo2', $intervalo2);
                    $tipoPeriodo = 'month';
                    $.cookie('tipoPeriodo', $tipoPeriodo);
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Ano
                $('#btnAno').click(function () {
                    $intervalo = moment().startOf('year');
                    $intervalo2 = moment().endOf('year');
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                    $.cookie('intervalo', $intervalo);
                    $.cookie('intervalo2', $intervalo2);
                    $tipoPeriodo = 'year';
                    $.cookie('tipoPeriodo', $tipoPeriodo);
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Todos
                $('#btnTudo').click(function () {
                    $intervalo = '';
                    $intervalo2 = '';
                    $('#datas').text('Todos');
                    $tipoPeriodo = '';
                    $.removeCookie('intervalo');
                    $.removeCookie('intervalo2');
                    $.removeCookie('tipoPeriodo');
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Seleciona os Periodos
                $('#btnSelPeriodo').click(function () {
                    $('#selecionaPeriodos').show(500);
                    $('#periodos').hide();
                    $('#selecionaPeriodos input').datepicker({
                        format: "dd/mm/yyyy",
                        language: "pt-BR",
                        autoclose: true,
                        todayHighlight: true
                    });
                });
                $('#aplicaPeriodo').click(function () {
                    $('#periodos').show(500);
                    $('#selecionaPeriodos').hide();
                    $intervalo = $('#inicioPeriodo').val();
                    $intervalo2 = $('#fimPeriodo').val();
                    //Transformando de DD/MM/YYYY para YYYY/MM/DD
                    var intervaloTemp = $intervalo.split("/");
                    $intervalo = intervaloTemp[2] + "/" + intervaloTemp[1] + "/" + intervaloTemp[0];
                    var intervaloTemp = $intervalo2.split("/");
                    $intervalo2 = intervaloTemp[2] + "/" + intervaloTemp[1] + "/" + intervaloTemp[0];
                    $intervalo = moment($intervalo);
                    $intervalo2 = moment($intervalo2);
                    $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                    $('#datas').text($datas);
                    $.cookie('intervalo', $intervalo);
                    $.cookie('intervalo2', $intervalo2);
                    $tipoPeriodo = 'week';
                    $.cookie('tipoPeriodo', $tipoPeriodo);
                    $gridConsulta.bootstrapTable('refresh');
                });
                //Setup dos botões de avançar e retroceder.
                $('#retrocedePeriodo').click(function () {
                    if ($intervalo !== '' && $intervalo2 !== '') {
                        $tipoPeriodo = $.cookie('tipoPeriodo');
                        $intervalo = moment($intervalo).subtract(1, $tipoPeriodo).startOf($tipoPeriodo);
                        $intervalo2 = moment($intervalo2).subtract(1, $tipoPeriodo).endOf($tipoPeriodo);
                        $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                        $('#datas').text($datas);
                        $.cookie('intervalo', $intervalo);
                        $.cookie('intervalo2', $intervalo2);
                        $gridConsulta.bootstrapTable('refresh');
                    }
                });
                $('#avancaPeriodo').click(function () {
                    if ($intervalo !== '' && $intervalo2 !== '') {
                        $tipoPeriodo = $.cookie('tipoPeriodo');
                        $intervalo = moment($intervalo).add(1, $tipoPeriodo).startOf($tipoPeriodo);
                        $intervalo2 = moment($intervalo2).add(1, $tipoPeriodo).endOf($tipoPeriodo);
                        $datas = $intervalo.format("DD/MM/YYYY") + ' - ' + $intervalo2.format("DD/MM/YYYY");
                        $('#datas').text($datas);
                        $.cookie('intervalo', $intervalo);
                        $.cookie('intervalo2', $intervalo2);
                        $gridConsulta.bootstrapTable('refresh');
                    }
                });
                $('.dropdown-menu').on('click', function (e) {
                    if ($(this).hasClass('dropdown-menu-form')) {
                        e.stopPropagation();
                    }
                });
                $('#cancelar').on('click', function () {
                    $('#selecionaPeriodos').hide();
                    $('#periodos').show();
                });
                $('#aplicaMostrar').on('click', function () {
                    $('#exibir').removeClass('open');
                });
                
                //--------------------------------------
                todos = $('#todos').prop('checked');
                if (todos === true) {
                    $('.marcar').each(
                        function () {
                            $(this).prop('checked', true);
                        }
                    );
                    $exibir = 'T';
               
                }

                $('#todos').change(function () {
                    todos = $('#todos').prop('checked');
                    if (todos === true) {
                        $('.marcar').each(
                            function () {
                                $(this).prop('checked', true);
                            }
                        );
                        $exibir = 'T';
                    
                    } else if (todos !== true) {
                        $('.marcar').each(
                            function () {
                                $(this).prop('checked', false);
                            }
                        );
                    }
                });
                $('#exibir').on('change', function () {
                    todos = $('#todos').prop('checked');
                    faturada = $('#faturada').prop('checked');
                    naofaturada = $('#naofaturada').prop('checked');

                    if (faturada === true && naofaturada === true) {
                        $('#todos').prop('checked', true);
                        $exibir = 'T';                     
                    } else {
                        $exibir = '';                      
                        $('#todos').prop('checked', false);
                        if (faturada === true) {
                            $exibir += 'F';
                        }
                        if (naofaturada === true) {
                            $exibir += 'N';
                        }
                    }
                });
                $('#aplicaMostrar').click(function () {
                    $gridConsulta.bootstrapTable('refresh');
                });
                //----------------------------------------------------------------
                
                
            })();
            function addMsg(data) {
                if (jQuery.isArray(data.mensagem)) {
                    jQuery.each(data.mensagem, function () {
                        $msgs.html('<div class="' + data.tipo + '" role="alert"><strong>' + data.assunto + '</strong> ' + this + '</div>');
                    });
                } else {
                    $msgs.html('<div class="' + data.tipo + '" role="alert"><strong>' + data.assunto + '</strong> ' + data.mensagem + '</div>');
                }
            }
            
            $btnEmitirPDF.click(function () {
                let busca = $('.search input').val();
                let intervalo = $intervalo ? $intervalo.format('DD/MM/YYYY') : '';
                let intervalo2 = $intervalo2 ? $intervalo2.format('DD/MM/YYYY') : '';
                window.open('http://report.nxfacil.com.br/report?name=vendas&type=pdf&token=<?php echo $_SESSION['nctoken']; ?>&periodo='+intervalo+'&periodo2='+intervalo2+'&busca='+busca+'&exibir='+$exibir);
            });
            $btnEmitirXLS.click(function () {
                let busca = $('.search input').val();
                let intervalo = $intervalo ? $intervalo.format('DD/MM/YYYY') : '';
                let intervalo2 = $intervalo2 ? $intervalo2.format('DD/MM/YYYY') : '';
                window.open('http://report.nxfacil.com.br/report?name=vendas&type=xls&token=<?php echo $_SESSION['nctoken']; ?>&periodo='+intervalo+'&periodo2='+intervalo2+'&busca='+busca+'&exibir='+$exibir);
            });
            $btnEmitirPDFResp.click(function () {
                let busca = $('.search input').val();
                let intervalo = $intervalo ? $intervalo.format('DD/MM/YYYY') : '';
                let intervalo2 = $intervalo2 ? $intervalo2.format('DD/MM/YYYY') : '';
                window.open('http://report.nxfacil.com.br/report?name=vendas&type=pdf&token=<?php echo $_SESSION['nctoken']; ?>&periodo='+intervalo+'&periodo2='+intervalo2+'&busca='+busca+'&exibir='+$exibir);
            });
            $btnEmitirXLSResp.click(function () {
                let busca = $('.search input').val();
                let intervalo = $intervalo ? $intervalo.format('DD/MM/YYYY') : '';
                let intervalo2 = $intervalo2 ? $intervalo2.format('DD/MM/YYYY') : '';
                window.open('http://report.nxfacil.com.br/report?name=vendas&type=xls&token=<?php echo $_SESSION['nctoken']; ?>&periodo='+intervalo+'&periodo2='+intervalo2+'&busca='+busca+'&exibir='+$exibir);
            });
            $acoesBtn.click(function () {
                $("#modal-acoes").openModal();
                $('.fixed-action-btn').closeFAB();
            });
            $desatualizaNota.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                swal({
                    title: 'Tem certeza?',
                    text: 'Deseja mesmo desatualizar esse(s) dado(s)?',
                    type: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    disableButtonsOnConfirm: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Desatualizar",
                    cancelButtonText: "Não desatualizar"
                }, function (isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: '<?php echo "$URL_BASE_PATH/modulos/venda/desatualizaVenda.php" ?>',
                            type: 'POST',
                            dataType: 'json',
                            cache: false,
                            data: {
                                'excluir': 1,
                                id: jQuery.map(selecionados, function (linha) {
                                    return linha.codigo;
                                })
                            },
                            statusCode: {
                                '<?php echo STATUS_CODE_NOT_FOUND ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Essa pagina não foi encontrada... :(",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                },
                                '<?php echo STATUS_CODE_INTERNAL_SERVER_ERROR ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.tipo === 'alert alert-danger') {
                                    swal({
                                        title: "Ooops...",
                                        text: data.mensagem,
                                        type: "error",
                                        confirmButtonText: "Tudo bem :(",
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        confirmButtonColor: "#449D44"
                                    });
                                } else {
                                    swal({
                                        title: "Sucesso!",
                                        text: "Dado(s) desatualizado(s) com Sucesso!",
                                        type: "success",
                                        showCancelButton: false,
                                        timer: 2000
                                    });
                                }
                                $gridConsulta.bootstrapTable('refresh');
                                setStatusBotoes([]);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (textStatus === 'parsererror') {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            }
                        });
                    }
                });
            });
            $desatualizaNotaResp.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                swal({
                    title: 'Tem certeza?',
                    text: 'Deseja mesmo desatualizar esse(s) dado(s)?',
                    type: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    disableButtonsOnConfirm: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Desatualizar",
                    cancelButtonText: "Não desatualizar"
                }, function (isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: '<?php echo "$URL_BASE_PATH/modulos/venda/desatualizaVenda.php" ?>',
                            type: 'POST',
                            dataType: 'json',
                            cache: false,
                            data: {
                                'excluir': 1,
                                id: jQuery.map(selecionados, function (linha) {
                                    return linha.codigo;
                                })
                            },
                            statusCode: {
                                '<?php echo STATUS_CODE_NOT_FOUND ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Essa pagina não foi encontrada... :(",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                },
                                '<?php echo STATUS_CODE_INTERNAL_SERVER_ERROR ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.tipo === 'alert alert-danger') {
                                    swal({
                                        title: "Ooops...",
                                        text: data.mensagem,
                                        type: "error",
                                        confirmButtonText: "Tudo bem :(",
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        confirmButtonColor: "#449D44"
                                    });
                                } else {
                                    swal({
                                        title: "Sucesso!",
                                        text: "Dado(s) desatualizado(s) com Sucesso!",
                                        type: "success",
                                        showCancelButton: false,
                                        timer: 2000
                                    });
                                }
                                $gridConsulta.bootstrapTable('refresh');
                                setStatusBotoes([]);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (textStatus === 'parsererror') {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            }
                        });
                    }
                });
            });
            function setStatusBotoes(selecionados) {
                if (selecionados.length > 1) {
                    $btnExcluir.removeAttr('disabled');
                    $btnRel.attr('disabled', 'disabled');
                    $btnNFE.attr('disabled', 'disabled');
                    $btnAlterar.attr('disabled', 'disabled');
                    $btnEmitirPDF.attr('disabled', 'disabled');
                    $btnEmitirXLS.attr('disabled', 'disabled');
                    $btnCopiar.attr('disabled', 'disabled');

                    $btnExcluirResp.removeAttr('disabled');
                    $btnRelResp.attr('disabled', 'disabled');
                    $btnNFEResp.attr('disabled', 'disabled');
                    $btnAlterarResp.attr('disabled', 'disabled');
                    $btnEmitirPDFResp.attr('disabled', 'disabled');
                    $btnEmitirXLSResp.attr('disabled', 'disabled');
                    $btnCopiarResp.attr('disabled', 'disabled');
                    
                } else if (selecionados.length === 1) {
                    $btnExcluir.removeAttr('disabled');
                    $btnRel.removeAttr('disabled');
                    $btnNFE.removeAttr('disabled');
                    $btnEmitirPDF.attr('disabled', 'disabled');
                    $btnEmitirXLS.attr('disabled', 'disabled');
                    $btnCopiar.removeAttr('disabled');

                    $btnExcluirResp.removeAttr('disabled');
                    $btnRelResp.removeAttr('disabled');
                    $btnNFEResp.removeAttr('disabled');
                    $btnEmitirPDFResp.attr('disabled', 'disabled');
                    $btnEmitirXLSResp.attr('disabled', 'disabled');
                    $btnCopiarResp.removeAttr('disabled');
                    if (selecionados[0].numeroOS > 0) {
                        $desatualizaNota.attr('disabled', 'disabled');
                        $desatualizaNotaResp.attr('disabled', 'disabled');
                    } else {
                        if (selecionados[0].atualizada === 'S') {
                            $btnAlterar.attr('disabled', 'disabled');
                            $desatualizaNota.removeAttr('disabled');
                            $btnNFE.removeAttr('disabled');
                            $btnAlterarResp.attr('disabled', 'disabled');
                            $desatualizaNotaResp.removeAttr('disabled');
                            $btnNFEResp.removeAttr('disabled');
                            $btnExcluir.attr('disabled', 'disabled');
                        } else {
                            $btnAlterar.removeAttr('disabled');
                            $btnExcluir.removeAttr('disabled');
                            $desatualizaNota.attr('disabled', 'disabled');
                            $btnNFE.attr('disabled', 'disabled');
                            $btnAlterarResp.removeAttr('disabled');
                            $desatualizaNotaResp.attr('disabled', 'disabled');
                            $btnNFEResp.attr('disabled', 'disabled');
                        }
                    }

                } else {
                    $btnExcluir.attr('disabled', 'disabled');
                    $btnCopiar.attr('disabled', 'disabled');
                    $btnRel.attr('disabled', 'disabled');
                    $btnNFE.attr('disabled', 'disabled');
                    $btnAlterar.attr('disabled', 'disabled');
                    $btnEmitirPDF.removeAttr('disabled');
                    $btnEmitirXLS.removeAttr('disabled');
                    $desatualizaNota.attr('disabled', 'disabled');

                    $btnExcluirResp.attr('disabled', 'disabled');
                    $btnCopiarResp.attr('disabled', 'disabled');
                    $btnRelResp.attr('disabled', 'disabled');
                    $btnNFEResp.attr('disabled', 'disabled');
                    $btnAlterarResp.attr('disabled', 'disabled');
                    $btnEmitirPDFResp.removeAttr('disabled');
                    $btnEmitirXLSResp.removeAttr('disabled');
                    $desatualizaNotaResp.attr('disabled', 'disabled');
                }
                jQuery.map(selecionados, function (verificar) {
                    if (verificar.vnfnumero > 0) {
                        $btnExcluir.attr('disabled', 'disabled');
                        $btnRel.attr('disabled', 'disabled');   

                        $btnExcluirResp.attr('disabled', 'disabled');
                        $btnRelResp.attr('disabled', 'disabled');                        

                        if (verificar.vnfstatus == 'A' || verificar.vnfstatus == 'C' || verificar.vnfstatus == 'D' || verificar.vnfstatus == 'I'){
                            $desatualizaNota.attr('disabled', 'disabled');
                            $desatualizaNotaResp.attr('disabled', 'disabled');  
                            
                            $btnAlterar.attr('disabled', 'disabled');
                            $btnAlterarResp.attr('disabled', 'disabled'); 
                            
                            $btnNFE.attr('disabled', 'disabled');
                            $btnNFEResp.attr('disabled', 'disabled');                            
                        }

                    }
                    if (verificar.atualizada == 'S') {
//                         if (verificar.vnfstatus == 'A' || verificar.vnfstatus == 'C' || verificar.vnfstatus == 'D'){
                           $btnExcluir.attr('disabled', 'disabled');
                           $btnExcluirResp.attr('disabled', 'disabled');
//                         }

                        $btnRel.attr('disabled', 'disabled');
                        $btnAlterar.attr('disabled', 'disabled');
                        
                        $btnRelResp.attr('disabled', 'disabled');
                        $btnAlterarResp.attr('disabled', 'disabled');

                    }else{
                        $desatualizaNota.attr('disabled', 'disabled');
                        $desatualizaNotaResp.attr('disabled', 'disabled');
                    }
                });
            }

            $btnExcluir.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                swal({
                    title: 'Tem certeza?',
                    text: 'Deseja mesmo excluir esse(s) dado(s)?',
                    type: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    disableButtonsOnConfirm: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Excluir",
                    cancelButtonText: "Não excluir"
                }, function (isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: '<?php echo "$URL_BASE_PATH/modulos/venda/excluirJson.php" ?>',
                            type: 'POST',
                            dataType: 'json',
                            cache: false,
                            data: {
                                'excluir': 1,
                                id: jQuery.map(selecionados, function (linha) {
                                    return linha.codigo;
                                })
                            },
                            statusCode: {
                                '<?php echo STATUS_CODE_NOT_FOUND ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Essa pagina não foi encontrada... :(",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                },
                                '<?php echo STATUS_CODE_INTERNAL_SERVER_ERROR ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.tipo === 'alert alert-danger') {
                                    swal({
                                        title: "Ooops...",
                                        text: data.mensagem,
                                        type: "error",
                                        confirmButtonText: "Tudo bem :(",
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        confirmButtonColor: "#449D44"
                                    });
                                } else {
                                    swal({
                                        title: "Sucesso!",
                                        text: "Dado(s) excluido(s) com Sucesso!",
                                        type: "success",
                                        showCancelButton: false,
                                        timer: 2000
                                    });
                                }
                                $gridConsulta.bootstrapTable('refresh');
                                setStatusBotoes([]);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (textStatus === 'parsererror') {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            }
                        });
                    }
                });
            });
            $btnExcluirResp.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                swal({
                    title: 'Tem certeza?',
                    text: 'Deseja mesmo excluir esse(s) dado(s)?',
                    type: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    disableButtonsOnConfirm: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Excluir",
                    cancelButtonText: "Não excluir"
                }, function (isConfirm) {
                    if (isConfirm) {
                        jQuery.ajax({
                            url: '<?php echo "$URL_BASE_PATH/modulos/venda/excluirJson.php" ?>',
                            type: 'POST',
                            dataType: 'json',
                            cache: false,
                            data: {
                                'excluir': 1,
                                id: jQuery.map(selecionados, function (linha) {
                                    return linha.codigo;
                                })
                            },
                            statusCode: {
                                '<?php echo STATUS_CODE_NOT_FOUND ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Essa pagina não foi encontrada... :(",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                },
                                '<?php echo STATUS_CODE_INTERNAL_SERVER_ERROR ?>': function () {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            },
                            success: function (data, textStatus, jqXHR) {
                                if (data.tipo === 'alert alert-danger') {
                                    swal({
                                        title: "Ooops...",
                                        text: data.mensagem,
                                        type: "error",
                                        confirmButtonText: "Tudo bem :(",
                                        showCancelButton: false,
                                        showConfirmButton: true,
                                        confirmButtonColor: "#449D44"
                                    });
                                } else {
                                    swal({
                                        title: "Sucesso!",
                                        text: "Dado(s) excluido(s) com Sucesso!",
                                        type: "success",
                                        showCancelButton: false,
                                        timer: 2000
                                    });
                                }
                                $gridConsulta.bootstrapTable('refresh');
                                setStatusBotoes([]);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (textStatus === 'parsererror') {
                                    swal({
                                        title: "Ooops...",
                                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                                        type: "error",
                                        showCancelButton: true,
                                        confirmButtonColor: "#DD6B55"
                                    });
                                }
                            }
                        });
                    }
                });
            });
            $btnAlterar.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/venda/alteraform.php" ?>?id=' + selecionados[0].codigo;
            });
            $btnCopiar.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/venda/copiarVenda.php" ?>?id=' + selecionados[0].codigo;
            });
            $btnNFE.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/nfe/nfe.php" ?>?id=' + selecionados[0].codigo;
            });
            $btnAlterarResp.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/venda/alteraform.php" ?>?id=' + selecionados[0].codigo;
            });
            $btnCopiarResp.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/venda/copiarVenda.php" ?>?id=' + selecionados[0].codigo;
            });
            $btnNFEResp.click(function () {
                var selecionados = $gridConsulta.bootstrapTable('getSelections');
                location.href = '<?php echo "$URL_BASE_PATH/modulos/nfe/nfe.php" ?>?id=' + selecionados[0].codigo;
            });
            $gridConsulta.bootstrapTable({
                queryParams: function (params) {

                    var busca = jQuery.trim($busca.val()), $adm = '<?= $adm ?>', $usuario = <?= $_SESSION['codigoUsuario'] ?>, $verOs = '<?= $flagOS ?>';
                    if (busca.length) {
                        params.search = busca;
                    }

                    if ($intervalo) {
                        $intervalo = $.cookie('intervalo');
                        $intervalo = moment($intervalo);
                        params.de = $intervalo.format("YYYY/MM/DD");
                    } else {
                        $intervalo = '';
                    }
                    if ($adm !== 'S' && $verOs !== 'S') {
                        params.usuario = $usuario;
                    }

                    if ($intervalo2) {
                        $intervalo2 = $.cookie('intervalo2');
                        $intervalo2 = moment($intervalo2);
                        params.ate = $intervalo2.format("YYYY/MM/DD");
                    } else {
                        $intervalo2 = '';
                    }
                    if ($exibir.length) {
                        params.exibir = $exibir;

                    }                      
                    window.atualizaValores($intervalo, $intervalo2, $exibir, params.search, $usuario);
                    return params;
                }
            }).bind({
                'check-all.bs.table': function (e) {
                    var selecionados = $gridConsulta.bootstrapTable('getSelections');
                    setStatusBotoes(selecionados);
                },
                'uncheck-all.bs.table': function (e) {
                    var selecionados = $gridConsulta.bootstrapTable('getSelections');
                    setStatusBotoes(selecionados);
                },
                'check.bs.table': function (e, row) {
                    var selecionados = $gridConsulta.bootstrapTable('getSelections');
                    setStatusBotoes(selecionados);
                },
                'uncheck.bs.table': function (e, row) {
                    var selecionados = $gridConsulta.bootstrapTable('getSelections');
                    setStatusBotoes(selecionados);
                },
                'load-error.bs.table': function () {
                    $gridConsulta.bootstrapTable('hideLoading');
                    swal({
                        title: "Ooops...",
                        text: "Aconteceu algo de errado! Por favor, contate o suporte...",
                        type: "error",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55"
                    });
                },
                'search.bs.table': function () {
                    $gridConsulta.bootstrapTable('showLoading');
                },
                'load-success.bs.table': function () {
                    $gridConsulta.bootstrapTable('hideLoading');
                }
            });
        });
    </script>
    <style type="text/css">
        @media (min-width: 992px) {
            .search.col-md-4.pull-right {
                width: 22%;
            }
        }

        .fixed-table-container {
            border: none !important;
        }

        .fixed-table-container .table,
        .fixed-table-container .table td,
        .fixed-table-container .table th {
            border: none !important;
            border-radius: 0 !important;
        }
    </style>
    <?php
} else {
    ?>
    Você não tem acesso á essa página.
    <?php
}
require_once 'view/rodape.php';
