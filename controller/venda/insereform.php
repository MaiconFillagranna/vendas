<?php
require_once 'view/topo.php';
require_once 'lib/ContaBancaria.php';

$empresa = $_SESSION['codigo'];
$dataHJ = date("d/m/Y");
$venId = $_SESSION['codigoUsuario'];

$sql = "SELECT uncnome FROM usuarionossocliente WHERE nccodigo = $empresa AND unccodigo = $venId";
$retorno = executaSql($sql);
while ($usuario = pg_fetch_object($retorno)) {
    $venNome = $usuario->uncnome;
}

$contaBancaria = new ContaBancaria();

$adadosCB = $contaBancaria->getDados(["*"], ["cbpadrao = 1"]);
$dadosCB = array_shift($adadosCB);

$sql = executaSql("SELECT * FROM tipooperacao WHERE nccodigo = $1 AND topadrao = 1 AND (tooperacao = 'S' OR tooperacao = 'C') LIMIT 1", [$empresa]);
$dadosTOP = getLinhaQuery($sql);

if (isset($dadosTOP->toclassificacaofinanceira)) {
    $sql3c = "SELECT * FROM classificacaofinanceira WHERE nccodigo =" . $empresa . " AND cfcodigo = " . $dadosTOP->toclassificacaofinanceira;
    $retorno3c = pg_query($DB_CONN, $sql3c);
    while ($reg3c = pg_fetch_assoc($retorno3c)) {
        $classificacaofinnome = $reg3c["cfnome"];
        $cfcodigo = $reg3c["cfcodigo"];
    }
} else {
    $classificacaofinnome = '';
    $cfcodigo = null;
}

if (isset($dadosTOP->toespcodigo)) {
    $espcod = $dadosTOP->toespcodigo;
    $sqlGetEspecie = executaSql("SELECT espsigla FROM especie WHERE nccodigo = $empresa AND espcodigo = $espcod");
    $espnome = getLinhaQuery($sqlGetEspecie)->espsigla;
}

$obs = null;
$sql = executaSql("SELECT confobsprodvenda FROM configuracoes WHERE nccodigo = $1", [$empresa]);
if ($sql) {
    $obs = isset(getLinhaQuery($sql)->confobsprodvenda) ? getLinhaQuery($sql)->confobsprodvenda : null;
}

$dadosRetorno = filter_input(INPUT_POST, "dados", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if(isset($dadosRetorno)) {
    foreach($dadosRetorno as $index => $dado) {
        $oDado = json_decode(urldecode($dado));
        $novoProduto = new stdClass();
        $novoProduto->vicodservproduto = $oDado->produto_codigo;
        $novoProduto->vinome = $oDado->produto_nome;
        $novoProduto->viquantidade = number_format($oDado->saldo, 4, ',', '.');
        $novoProduto->vivalorunitario = number_format($oDado->valor_sugerido, 4, ',', '.');
        $DADOS->produtos[] = $novoProduto;
        $chave = $oDado->chave;
        $clinome = $oDado->cliente_nome;
        $clicodigo = $oDado->cliente_codigo;
        $obs = "Retorno da NFE $oDado->nota";
    }
}

?>
<div class="container">
    <div class="page-header adicionar">
        <h3>Venda de Produtos<small class="adicionar"> // Cadastro</small></h3>
    </div>

    <div id="mensagens"></div>

    <div id="conteudo">
        <form id="formVenda" method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-5" id="divFornecedor">
                    <div id="msgSucessoFor" style="display: none;">
                        <div style="float:right">
                            <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                            </span>
                            <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                        </div> 
                        <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                    </div>

                    <label>Cliente*</label>
                    <input id="pessoa" type="text" style="margin-bottom: 10px;" autocomplete="off" data-required="required" class="form-control " name="pessoa" value="<?= isset($clinome) ? $clinome : '' ?>"/>
                    <div id="labelAdd" class="labelComplete-field"  style="display: none;">
                        <span class="pull-left">Adicionar</span>
                        <span id="textCompFornecedor" class="text-complete" ></span>
                        <button type='button' name='confirma' id='confirma' class='btn-add'>
                            <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                        </button>
                    </div>
                    <div class="fa fa-refresh fa-spin autocompleteLoading" id="pload" style="display:none;"></div>
                </div>

                <div style="display: none">
                    <label>Cliente Codigo</label><input type="text" id="pessoaId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="pessoaId" value="<?= isset($clicodigo) ? $clicodigo : '' ?>"/>
                    <label>Cliente Exterior</label><input type="text" id="clienteExterior" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="clienteExterior"/>
                    <label>Conta Bancaria Codigo</label><input type="text" id="ContBancId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="ContBancId" value="<?= $dadosCB["codigo"] ?>"/>
                    <label>Vendedor Codigo</label><input type="text" id="vendedorId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="vendedorId" value="<?php echo $venId; ?>"/>
                    <label>Chave da Nota de Entrada</label><input type="text" id="chaveNotaEntrada" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="chaveNotaEntrada" value="<?= isset($chave) ? $chave : '' ?>"/>
                </div>                
                <div class="col-md-7" style="padding-right: 0px;padding-left:0px;">
                    <div class="col-md-3" id="datasaldo" style="padding-right: 5px;padding-left:5px;">
                        <label>Data Emissão</label>
                        <input type="text" placeholder="dd/mm/aaaa" style="margin-bottom: 10px;" autocomplete="off" class="form-control" id="cbdatasaldo" name="vdatapedido" value="<?php echo $dataHJ; ?>"/>
                    </div>
                    <div class="col-md-3" style="padding-right: 5px;padding-left:0px;">
                        <label>Nº do Pedido</label>
                        <input id="numeroPedido" name="numeroPedido" type="text" class="form-control" autocomplete="new-password" maxlength="15">
                    </div>
                    <div class="col-md-6" id="divTipoOperacao" style="padding-right: 0px;padding-left:0px;">
                        <div id="msgSucessoTOP" style="display: none;">
                            <div style="float:right">
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                                </span>
                                <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                            </div> 
                            <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                        </div>
                        <label>Tipo Operação*</label>
                        <input id="tipooperacao" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="tipooperacao"  data-required="required" value="<?= isset($dadosTOP->tonome) ? $dadosTOP->tonome : '' ?>"/>
                        <input id="tipooperacaoid" type="text" style="margin-bottom: 10px;display: none;" autocomplete="off" class="form-control " name="tipooperacaoid" value="<?= isset($dadosTOP->tocodigo) ? $dadosTOP->tocodigo : '' ?>"/>
                        <div id="labelAddtop" class="labelComplete-field" style="display: none;">
                            <span class="pull-left">Adicionar</span>
                            <span id="textComptipooperacao" class="text-complete"></span>
                            <button type='button' name='confirma' id='confirmatop' class='btn-add'>
                                <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                            </button>
                        </div>
                        <div class="fa fa-refresh fa-spin autocompleteLoading" id="topload" style="display:none;"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="gridProdutos"></div>
                    <div>
                        <button type="button" class="btn btn-default" id="btnAddProduto" title="Adicionar item" style="margin-top: 25px;margin-bottom: 25px;">Adicionar produtos</button>
                    </div>
                    <div class="row totais">
                        <div class="col-md-10 pull-left" style="padding-bottom: 15px">
                            <div class="col-md-4">
                                Total Bruto
                                <input id="totalprodutos" readonly="readonly" placeholder="R$" class="form-control" name="totalprodutos" type="text">
                            </div>
                             <div class="col-md-4"style="display:none;">
                                Total Custo
                                <input id="totalcustogeral" readonly="readonly" placeholder="R$" class="form-control" name="totalcustogeral" type="text">
                            </div>  
                            <div class="col-md-4">
                                Frete
                                <input type="text" placeholder="R$" class="form-control" id="vnfvalorfrete" name="vnfvalorfrete" value=""/>
                            </div>
                            <div class="col-md-4">
                                Acréscimo
                                <input type="text" placeholder="R$" class="form-control" id="vvaloracrescimo" name="vvaloracrescimo" value=""/>
                            </div>
                        </div>                        
                        <div class="col-md-10 pull-left">                            
                            <div class="col-md-4">
                                IPI
                                <input type="text" placeholder="R$" class="form-control" id="valortotalipi" name="valortotalipi" value="" readonly="true"/>
                            </div>
                            <div class="col-md-4">
                                Desconto %
                                <input id="descontoPorc" placeholder="%" class="form-control" name="descontoPorc" type="text">
                            </div>
                            <div class="col-md-4">
                                Desconto Total
                                <input id="desconto" placeholder="R$" class="form-control" name="desconto" type="text">
                            </div>                             
                        </div>
                        <div class="col-md-2 pull-right align-right" style="width: 16.66%;">
                            Valor Total
                            <input id="totalpagar" readonly="readonly" placeholder="0,00" class="form-control totalPagar" name="totalpagar" type="text" style="border: 0px none;box-shadow: none;cursor: context-menu; font-size: 21px !important">
                        </div>                        
                    </div>
                </div>
            </div>

            <div class="row" id="DivFormPag">
                <legend><div style="text-transform: uppercase">Formas de Pagamento</div></legend>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="A" checked/>
                        À vista
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="P"/>
                        À prazo
                    </label>
                </div>
            </div>

            <div class="row" id="Parcelas" style="display: none">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" style="padding: 14px;background: #F7F7F7;color: #333;max-width: 100%;font-style: italic;margin-bottom: 15px;">
                            <div class="col-md-12">
                                <p style="text-transform: uppercase">Informe as parcelas para pagamento:</p>
                                <p>30 60 para gerar parcelas para 30 e 60 dias;
                                    0 para gerar uma única parcela (à vista);
                                    6x para gerar 6 parcelas iguais;
                                    0 2x para gerar uma entrada e mais 2 parcelas iguais.
                                </p>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-5" style="padding-left: 0px;  padding-top: 5px;  margin-right: 5px;">
                                <input type="text"  placeholder="" class="form-control" id="parcelamento" name="parcelamento" value=""/>
                            </div>
                            <div class="col-md-6" style="padding-left: 0px;padding-top: 5px;">
                                <button type="button" class="btn btn-default" id="btnGerarParcelas">Gerar Parcelas</button>
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding-left: 0!important;padding-bottom: 30px;">
                            <div id="gridParcelas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default" id="btnAddParcela" title="Adicionar item" style="margin-top: -17px;"><i class="glyphicon glyphicon-plus" style="margin-top: 3px;margin-left: -5px;margin-right: 5px;"></i>Adicionar parcela</button>
                        </div>
                    </div>
                </div>
            </div>       
            <div class="col-md-3 alinhaMd3" id="divCB" style="margin-top: 30px">
                <div id="msgSucessoCB" style="display: none;">
                    <div style="float:right">
                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                        </span>
                        <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                    </div> 
                    <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                </div>

                <label>Conta Bancária*</label>
                <input id="ContBanc" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" data-required="required" name="ContBanc" value="<?= $dadosCB["nomeoriginal"] ?>"/>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="cbload" style="display: none;"></div>

                <div id="labelCB" style="display: none;position: absolute;width: 96.6%;margin-top: -12px;color: #FFF;padding: 10px 8px;z-index: 11111;top: 8px;right: 0px;background: #18B5C2 none repeat scroll 0% 0%;border-radius: 2px 2px 0px 0px !important;">
                    <span>Adicionar</span>
                    <span id="textCompCB" style="font-weight: bold"></span>
                    <button type='button' name='confirmaCB' id='confirmaCB' class='pull-right' style='margin-top: -5px;color: #FCFCFC;background: transparent none repeat scroll 0% 0%;vertical-align: middle;margin-bottom: -13px;border: 0px none;'>
                        <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                    </button>
                </div>
            </div>
            <div class="col-md-3 alinhaMd3" id="divCF" style="margin-top: 30px">
                <div id="msgSucessoCF" style="display: none;">
                    <div style="float:right">
                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                        </span>
                        <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                    </div> 
                    <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                </div>

                <label>Classificação Financeira*</label>
                <input id="ClassiFin" type="text" style="margin-bottom: 10px;" data-required="required" autocomplete="off" class="form-control" value="<?php echo $classificacaofinnome; ?>"/>
                <div id="labelCF" class="labelComplete-field" style="display: none">
                    <span class="pull-left">Adicionar</span>
                    <span id="textCompCF" class="text-complete"></span>
                    <button type='button' name='confirmaCF' id='confirmaCF' class='btn-add'>
                        <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                    </button>
                </div>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="cfload" style="display: none;"></div>
                <input type="text" placeholder="" class="form-control" name="ClassiFinId"  id="ClassiFinId" style="display: none;" value="<?php echo $cfcodigo; ?>">
            </div> 
            <div class="col-md-3 alinhaMd3" style="margin-top: 30px" id="divCentroCusto">
                <label>Centro de Custo</label>
                <input type="text" id="ccId" style="display:none" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" name="centrocustoid" value=""/>
                <input id="cc" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="centrocusto"  value=""/>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="loadcc" style="display: none;"></div>

                <div id="cclabel" class="labelComplete-field" style="display: none;"></div>
            </div>
            <div class="col-md-3 alinhaMd3" style="margin-top: 30px" id="divEspecie">
                <label>Espécie</label>
                <input type="text" id="espId" style="display:none" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" name="especieid" value="<?php echo isset($espcod) ? $espcod : "" ?>"/>
                <input id="esp" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="especie"  value="<?php echo isset($espnome) ? $espnome : "" ?>"/>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="loadesp" style="display: none;"></div>

                <div id="esplabel" class="labelComplete-field" style="display: none;"></div>
            </div>
            <legend></legend>
            <div class="row" style="display: none;margin-top: 15px">
                <div class="row">
                    <div class="col-md-12">
                        <div id="mostrar2" class="exibirCampo">
                            Informações de Frete e Pedido
                            <div class="pull-right">
                                Exibir <i class="mdi-navigation-unfold-more"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="ocultar2" class="ocultarCampo" style="display: none;">
                            Informações de Frete e Pedido
                            <div class="pull-right">
                                Ocultar <i class="mdi-navigation-unfold-less"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="BLOCO2" style="display: none">
                <div class="row">
                    <div class="col-md-4">
                        <label>Cobrar Frete do Cliente?</label><select style="margin-bottom: 10px;"  name ="vnftipofrete" class="form-control">
                            <option value="N">
                                Não
                            </option>
                            <option value="S">
                                Sim
                            </option>
                        </select>
                    </div>

                    <div class="col-md-5" id="">
                        <input type="text" style="margin-bottom: 10px; display: none" autocomplete="off" class="form-control" id="vnfcodtransportadora" name="vnfcodtransportadora"/>
                        <label>Transportadora</label><input type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" id="vnftransportadora" name="vnftransportadora"/>
                    </div>

                    <!--                    <div class="col-md-4" id="">
                                            <label>Valor do Frete</label><input type="text" placeholder="R$" class="form-control" id="vnfvalorfrete" name="vnfvalorfrete" value=""/>
                                        </div>-->

                    <div class="col-md-4" id="">
                        <label>Número do Pedido</label><input type="text" placeholder="" class="form-control" id="vordemcompra" name="vordemcompra" value=""/>
                    </div>                    
                </div>
            </div>

            <div class="row" style="margin-top: 15px">
                <div class="row">
                    <div class="col-md-12">
                        <div id="mostrar" class="exibirCampo">
                            Observações
                            <div class="pull-right">
                                Exibir <i class="mdi-navigation-unfold-more"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="ocultar" class="ocultarCampo" style="display: none;">
                            Observações
                            <div class="pull-right">
                                Ocultar <i class="mdi-navigation-unfold-less"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="BLOCO" style="display: none">
                <div class="row">
                    <div class="col-md-12" id="">
                        <span class="caracteres" style="float: right;margin-top: 15px; color: #3299CC"></span>
                        <label>Nossas Observações</label><textarea class="form-control" rows="3" id="vobsempresa" name="vobsempresa" onkeyup="contarCaracteres($(this), 2000)"><?= $obs ?></textarea>
                    </div>
                </div>
            </div>
            <legend></legend>
            <div class="col-md-6" id="divVendedor">

                <div id="msgSucessoFor" style="display: none;">
                    <div style="float:right">
                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                        </span>
                        <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                    </div> 
                    <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                </div>

                <label>Vendedor</label>
                <input id="vendedor" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="vendedor" value="<?php echo $venNome ?>"/>
                <div id="labelAddVendedor" class="labelComplete-field" style="display: none;">
                    <span class="pull-left">Adicionar</span>
                    <span id="textCompFornecedor" class="text-complete"></span>
                    <button type='button' name='confirma' id='confirma' class='btn-add'>
                        <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                    </button>
                </div>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="pload" style="display:none;"></div>
            </div> 

            <div class="row">               
                <div class="col-xs-12 col-sm-4 col-md-9">
                    <button type="submit" id="enviar" name="enviar" class="btn btn-navegacao btn-success">
                        <span class="mdi-navigation-check span-btn-align" style="font-size: 25px;"></span> <span class="span-btn-align-text">Salvar Cadastro</span>
                    </button>
                </div>

                <div class="col-xs-12 col-sm-4 col-md-3">
                    <a href="<?php echo $URL_BASE_PATH ?>/modulos/venda/consulta.php" class="btn btn-navegacao btn-primary">
                        <i class="mdi-navigation-arrow-back span-btn-align" style="font-size: 25px;"></i>
                        <span class="span-btn-align-text">Voltar à Listagem</span>
                    </a>
                </div>
            </div>
            <div id="modalDescontoTotal"></div>
        </form>
    </div>
</div>

<?php
require_once 'script.php';
include_once 'view/rodape.php';
?>
<script>
    $(function () {
        var $formNome = "#" + $('form').attr('id');
        var $form = $($formNome);
        var erro = false;
        $("#enviar").on('click', function (e) {
            e.preventDefault();
            $($formNome + ' input').each(function () {
                if ($(this).val() === "" && $(this).data("required") === "required" && $(this).is(':visible')) {
                    $(this).addClass("error");
                    $(this).focus();
                    erro = true;
                    return false;
                } else if (!($(this).val() === "") && $(this).data("required") === "required" && $(this).is(':visible')) {
                    $(this).removeClass("error");
                    erro = false;
                    erro--;
                }
            });
            if (erro === -1) {
                if (!validaTotalParcelas()) {
                    return false;
                } else {
                    if (!validaTotalParcelasCusto()) {
                        return false;
                    } else {
                        $($formNome).submit();
                    }
                }
            }
        });

        $form.submit(function (event) {
            $pessoaId = $('#pessoaId'),
                    $cbId = $('#ContBancId'),
                    $vendedorId = $('#vendedorId'),
                    $snId = $('#serieNotaId'),
                    $sevExeID = $('#sevExecId');
            $pessoa = $('#pessoa'),
                    $cb = $('#ContBanc'),
                    $vendedor = $('#vendedor'),
                    $sn = $('#vnfserienome'),
                    $serExe = $('#vgovservnome');

            if (!$pessoaId.val()) {
                $pessoa.focus();
                return false;
            }
            if ($cb.val() && !$cbId.val()) {
                $cb.focus();
                return false;
            }
            if ($vendedor.val() && !$vendedorId.val()) {
                $vendedor.focus();
                return false;
            }
            if ($sn.val() && !$snId.val()) {
                $sn.focus();
                return false;
            }
            if ($serExe.val() && !$sevExeID.val()) {
                $serExe.focus();
                return false;
            }
            $("#enviar").prop("disabled", true);

            jQuery.ajax({
                type: "POST",
                url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/insere.php",
                data: $(this).serialize(),
                success: function () {
                    swal({
                        title: "Sucesso!",
                        text: "Salvo com sucesso!",
                        type: "success",
                        showCancelButton: false
                    });
                    setTimeout(function () {
                        window.location.href = '<?php echo $URL_BASE_PATH ?>/modulos/venda/consulta.php';
                    }, 800);
                }
            });

            return false;
        });
    });
</script>
<style>
    .alinhaMd2{
        width: 20.1%
    }
    .alinhaMd3{
        width: 25%;
    }
    .btn-finalizar{            
        color: #449D44;
        padding-left: 31px;
        padding-right: 31px;
        background: #FFF none repeat scroll 0% 0%;
        border: 1px solid #449D44;
        border-radius: 5px;
        min-width: 190px;
        height: 50px;
    }
    
    .btn-finalizar:hover{      
        color: #fff;
        background:  #4BA75E;
    }
</style>