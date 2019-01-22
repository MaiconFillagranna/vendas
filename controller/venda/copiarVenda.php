<?php
require_once 'view/topo.php';

$empresa = $_SESSION['codigo'];
$id = $_GET['id']; // Recebendo o valor vindo do link

$dataHJ = date("d/m/Y");

$sql = "SELECT * FROM venda WHERE nccodigo =" . $empresa . " AND vcodigo = " . $id;
$retorno = pg_query($DB_CONN, $sql);
if (!($DADOS = pg_fetch_object($retorno))) {
    return;
}
$DADOS->vdatapedido = $dataHJ;
$tipo = $DADOS->vtipo;
$codigoPessoa = $DADOS->vcodcli;
if ($codigoPessoa != '') {
    $sql = "SELECT clinome FROM cliente WHERE nccodigo =" . $empresa . " AND clicodigo = " . $codigoPessoa;
    $retorno = pg_query($DB_CONN, $sql);
    while ($reg = pg_fetch_assoc($retorno)) {
        $nomepessoa = $reg["clinome"];
    }
} else {
    $nomepessoa = '';
}
$fDescontoPorc = $DADOS->vvalormercadorias > 0 ? $DADOS->vvalordesconto/$DADOS->vvalormercadorias*100 : 0;
$descontoPorc = number_format($fDescontoPorc, 2, ',', '.');
$DADOS->vdatapedido = inverte_data($DADOS->vdatapedido, '-');
$DADOS->vvalordesconto = number_format($DADOS->vvalordesconto, 2, ',', '.');
$DADOS->vvalormercadorias = number_format($DADOS->vvalormercadorias, 2, ',', '.');
$DADOS->vvalortotal = number_format($DADOS->vvalortotal, 2, ',', '.');
$DADOS->vnfvalorfrete = number_format($DADOS->vnfvalorfrete, 2, ',', '.');
$DADOS->valortotalipi = 0;

$totalpagar = number_format(($DADOS->vvalormercadorias + $DADOS->vnfvalorfrete - $DADOS->vvalordesconto),2,',','.');

$sql2 = "SELECT * FROM vendaitens WHERE nccodigo = $empresa and vcodigo = $id";
$retorno = pg_query($DB_CONN, $sql2);

$DADOS->produtos = [];
$fvvalorcusto = 0;
while ($reg = pg_fetch_object($retorno)) {
    $DADOS->produtos[] = $reg;
    $quantidade = $reg->viquantidade;
    $DADOS->valortotalipi += $reg->vivalipi;
    $total = $reg->viquantidade * $reg->vivalorunitario;
    $reg->viquantidade = number_format($reg->viquantidade, 4, ',', '.');
    $reg->vivalordesconto = number_format($reg->vivalordesconto, 2, ',', '.');
    $reg->vialiqipi = number_format($reg->vialiqipi, 2, ',', '.');
    $reg->vivalipi = number_format($reg->vivalipi, 2, ',', '.');
    $reg->vivalorunitario = number_format($reg->vivalorunitario, 7, ',', '.');
    $reg->total = number_format($total, 2, ',', '.');

    $sql2b = "SELECT * FROM produtoservico WHERE nccodigo = $empresa and prodservcodigo = " . $reg->vicodservproduto;
    $retornob = pg_query($DB_CONN, $sql2b);
    while ($regb = pg_fetch_object($retornob)) {
        $vivalorcusto = $regb->prodservvalorcusto * $quantidade;
        $fvvalorcusto += $vivalorcusto;
        $reg->vivalorcusto = number_format($regb->prodservvalorcusto, 2, ',', '.');               
    }
    $vvalorcusto = number_format($fvvalorcusto, 2, ',', '.'); 
    $reg->totalcusto = $vvalorcusto;
}
$DADOS->valortotalipi = number_format($DADOS->valortotalipi, 2, ',', '.');

$sql3 = "SELECT * FROM vendafinanceiro WHERE nccodigo = $empresa and vcodigo = $id";
$retorno = pg_query($DB_CONN, $sql3);

$DADOS->financeiro = [];


$DADOS->vavistaprazo = 'A';

$pago = '';
$sql5 = "SELECT finstatus FROM financeiro WHERE nccodigo = $empresa AND fcodvenda = $id";
$retorno = pg_query($DB_CONN, $sql5);
while ($verifica = pg_fetch_object($retorno)) {
    $status = $verifica->finstatus;
    if ($status === 'P') {
//        echo 'disabled';
    } else if ($status === 'A' && $pago !== 'disabled') {
        $pago = '';
    }
}

$topID = '';
$topnome = '';
$gerafin = '';
if ($DADOS->vnftipooperacao) {
    $topID = $DADOS->vnftipooperacao;
    $sql6 = "SELECT tonome, togerafinanceiro FROM tipooperacao WHERE tocodigo = $DADOS->vnftipooperacao AND nccodigo = $empresa";
    $retorno = pg_query($DB_CONN, $sql6);
    while ($tipooperacao = pg_fetch_object($retorno)) {
        $topnome = $tipooperacao->tonome;
        $gerafin = $tipooperacao->togerafinanceiro;
    }
}
if ($gerafin == 'S') {
    if ($DADOS->vclassifin) {
        $sql3c = "SELECT * FROM classificacaofinanceira WHERE nccodigo =" . $empresa . " AND cfcodigo = " . $DADOS->vclassifin;
        $retorno3c = pg_query($DB_CONN, $sql3c);
        while ($reg3c = pg_fetch_assoc($retorno3c)) {
            $classificacaofinnome = $reg3c["cfnome"];
            $cfcodigo = $reg3c["cfcodigo"];
        }
    } else {
        if ($toclassificacaofinanceira) {
            $sql3c = "SELECT * FROM classificacaofinanceira WHERE nccodigo =" . $empresa . " AND cfcodigo = " . $toclassificacaofinanceira;
            $retorno3c = pg_query($DB_CONN, $sql3c);
            while ($reg3c = pg_fetch_assoc($retorno3c)) {
                $classificacaofinnome = $reg3c["cfnome"];
                $cfcodigo = $reg3c["cfcodigo"];
            }
        } else {
            $classificacaofinnome = '';
            $cfcodigo = null;
        }
    }
}

require_once 'view/topo.php';
?>
<div class="container">
    <div class="page-header alteracao">
        <h3>Venda de Produtos<small class="alteracao"> // Copiar Informações</small></h3>
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

                    <label>Cliente</label>
                    <input id="pessoa" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="pessoa" required value="<?php echo $nomepessoa; ?>"/>
                    <div id="labelAdd" style="display: none;position: absolute;width: 96.6%;margin-top: -12px;color: #FFF;padding: 10px 8px;z-index: 11111;top: 8px;right: 0px;background: #18B5C2 none repeat scroll 0% 0%;border-radius: 2px 2px 0px 0px !important;">
                        <span>Adicionar</span>
                        <span id="textCompFornecedor" style="font-weight: bold"></span>
                        <button type='button' name='confirma' id='confirma' class='pull-right' style='margin-top: -5px;color: #FCFCFC;background: transparent none repeat scroll 0% 0%;vertical-align: middle;margin-bottom: -13px;border: 0px none;'>
                            <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                        </button>
                    </div>
                    <div class="fa fa-refresh fa-spin autocompleteLoading" id="pload" style="display:none;"></div>
                </div>
                <div style="display: none">
                    <label>Venda Codigo</label><input type="text" id="vcodigo" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="vcodigo" value="<?php echo $DADOS->vcodigo; ?>"/>
                    <label>Cliente Codigo</label><input type="text" id="pessoaId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="pessoaId" value="<?php echo $DADOS->vcodcli; ?>"/>
                    <label>CB Codigo</label><input type="text" id="ContBancId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="ContBancId" value="<?php echo $DADOS->vcontabancariacod ?>"/>
                    <label>Vendedor Codigo</label><input type="text" id="vendedorId" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" placeholder="" name="vendedorId" value="<?php echo $DADOS->vvendedorcod ?>"/>
                </div>
                <div class="col-md-7" style="padding-right: 0px;padding-left:5px;">
                    <div class="col-md-3" id="datasaldo" style="padding-right: 5px;padding-left:0px;">
                        <label>Data Emissão</label><input type="text" <?php echo $pago; ?> placeholder="dd/mm/aaaa" style="margin-bottom: 10px;" autocomplete="off" class="form-control" id="cbdatasaldo" name="vdatapedido" value="<?php echo $DADOS->vdatapedido; ?>"/>
                    </div>
                    <div class="col-md-3" style="padding-right: 5px;padding-left:0px;">
                        <label>Nº do Pedido</label>
                        <input id="numeroPedido" name="numeroPedido" type="text" class="form-control" autocomplete="new-password" maxlength="15" value="<?php echo $DADOS->vpedido; ?>">
                    </div>
                    <div class="col-md-6" id="divTipoOperacao" style="padding-right: 5px;padding-left:0px;">
                        <div id="msgSucessoTOP" style="display: none;">
                            <div style="float:right">
                                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                                </span>
                                <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                            </div> 
                            <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                        </div>
                        <label>Tipo Operação*</label>
                        <input id="tipooperacao" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="tipooperacao" data-required="true" value="<?php echo $topnome; ?>"/>
                        <input id="tipooperacaoid" type="text" style="margin-bottom: 10px;display: none;" autocomplete="off" class="form-control " name="tipooperacaoid" value="<?php echo $topID ?>"/>
                        <div id="labelAddtop" style="display: none;position: absolute;width: 96.6%;margin-top: -12px;color: #FFF;padding: 10px 8px;z-index: 11111;top: 8px;right: 0px;background: #18B5C2 none repeat scroll 0% 0%;border-radius: 2px 2px 0px 0px !important;">
                            <span>Adicionar</span>
                            <span id="textComptipooperacao" style="font-weight: bold"></span>
                            <button type='button' name='confirma' id='confirmatop' class='pull-right' style='margin-top: -5px;color: #FCFCFC;background: transparent none repeat scroll 0% 0%;vertical-align: middle;margin-bottom: -13px;border: 0px none;'>
                                <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                            </button>
                        </div>
                        <div class="fa fa-refresh fa-spin autocompleteLoading" id="topload" style="display:none;"></div>
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="gridProdutos">                  

                    </div>
                    <div>
                        <button type="button" class="btn btn-default" id="btnAddProduto" title="Adicionar item" style="margin-top: 25px;margin-bottom: 25px;">Adicionar produtos</button>
                    </div>
                    <div class="row totais">    
                        <div class="col-md-10 pull-left" style="padding-bottom: 15px">
                            <div class="col-md-4">
                                Total Bruto
                                <input id="totalprodutos" readonly="readonly" placeholder="R$" class="form-control" name="totalprodutos" type="text" value="<?php echo $DADOS->vvalormercadorias; ?>">
                            </div>
                             <div class="col-md-4"style="display:none;">
                                Total Custo
                                <input id="totalcustogeral" readonly="readonly" placeholder="R$" class="form-control" name="totalcustogeral" type="text" value="<?php echo $vvalorcusto; ?>">
                            </div>  
                            <div class="col-md-4">
                                Frete
                                <input <?php echo $pago; ?> type="text" placeholder="R$" class="form-control" id="vnfvalorfrete" name="vnfvalorfrete" value="<?php echo $DADOS->vnfvalorfrete; ?>">
                            </div>
                           <div class="col-md-4">
                                Acréscimo
                                <input type="text" placeholder="R$" class="form-control" id="vvaloracrescimo" name="vvaloracrescimo" value="<?php echo $DADOS->vvaloracrescimo; ?>"/>
                            </div>
                        </div>                        
                        <div class="col-md-10 pull-left">                            
                            <div class="col-md-4">
                                IPI
                                <input type="text" placeholder="R$" class="form-control" id="valortotalipi" name="valortotalipi" readonly="true" value="<?php echo $DADOS->valortotalipi; ?>">
                            </div>
                            <div class="col-md-4">
                                Desconto %
                                <input id="descontoPorc" placeholder="%" class="form-control" name="descontoPorc" type="text" value="<?php echo $descontoPorc; ?>">
                            </div>
                            <div class="col-md-4">
                                Desconto Total
                                <input id="desconto" placeholder="R$" class="form-control" name="desconto" type="text" value="<?php echo $DADOS->vvalordesconto; ?>">
                            </div>                             
                        </div>
                        <div class="col-md-2 pull-right align-right" style="width: 16.66%;">
                            Valor Total
                            <input id="totalpagar" readonly="readonly" placeholder="0,00" class="form-control totalPagar" name="totalpagar" type="text" style="border: 0px none;box-shadow: none;cursor: context-menu; font-size: 21px !important" value="<?php echo $DADOS->vvalortotal; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="DivFormPag">
                <div class="radio">
                    <legend>Formas de Pagamento</legend>
                    <input type="text" placeholder="" class="form-control" name="vavistaprazo" style="display: none;" id="vavistaprazo" value="<?php echo $DADOS->vavistaprazo; ?>">                        
                    <label>
                        <input type="radio" <?php echo $pago; ?> name="optionsRadios" id="optionsRadios1" value="A" <?php echo ($DADOS->vavistaprazo == "A") ? "checked" : null; ?>>
                        À vista
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" <?php echo $pago; ?> name="optionsRadios" id="optionsRadios2" value="P" <?php echo ($DADOS->vavistaprazo == "P") ? "checked" : null; ?> >
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
                        <div class="row col-md-12" style="padding-left: 0!important;padding-top: 25px;padding-bottom: 25px;">
                            <div id="gridParcelas">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="button" class="btn btn-default" id="btnAddParcela" title="Adicionar item" style="margin-top: -17px;margin-left: 15px;"><i class="glyphicon glyphicon-plus" style="margin-top: 3px;margin-left: -5px;margin-right: 5px;"></i>Adicionar parcela</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3" id="divCB" style="margin-top: 30px">
                <div id="msgSucessoCB" style="display: none;">
                    <div style="float:right">
                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                        </span>
                        <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                    </div> 
                    <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                </div>

                <label>Conta Bancária</label>
                <input id="ContBanc" type="text" style="margin-bottom: 10px;" data-required="true" autocomplete="off" class="form-control" name="vcontabancarianome" value="<?php echo $DADOS->vcontabancarianome; ?>"/>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="cbload" style="display: none;"></div>

                <div id="labelCB" style="display: none;position: absolute;width: 96.6%;margin-top: -12px;color: #FFF;padding: 10px 8px;z-index: 11111;top: 8px;right: 0px;background: #18B5C2 none repeat scroll 0% 0%;border-radius: 2px 2px 0px 0px !important;">
                    <span>Adicionar</span>
                    <span id="textCompCB" style="font-weight: bold"></span>
                    <button type='button' name='confirmaCB' id='confirmaCB' class='pull-right' style='margin-top: -5px;color: #FCFCFC;background: transparent none repeat scroll 0% 0%;vertical-align: middle;margin-bottom: -13px;border: 0px none;'>
                        <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                    </button>
                </div>
            </div>
            <div class="col-md-3" id="divCF" style="margin-top: 30px">
                <div id="msgSucessoCF" style="display: none;">
                    <div style="float:right">
                        <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                        </span>
                        <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                    </div> 
                    <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                </div>

                <label>Classificação Financeira*</label>
                <input id="ClassiFin" type="text" style="margin-bottom: 10px;" data-required="true" autocomplete="off" class="form-control" name="finclassificacao" value="<?php echo $classificacaofinnome; ?>"/>
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
            <div class="col-md-3" style="margin-top: 30px" id="divCentroCusto">
                <label>Centro de Custo</label>
                <input type="text" id="ccId" style="display:none" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" name="centrocustoid" value="<?php echo isset($DADOS->vavistaprazo) && $DADOS->vavistaprazo == "A" && isset($ccCod) ? $ccCod : "" ?>"/>
                <input id="cc" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="centrocusto"  value="<?php echo isset($DADOS->vavistaprazo) && $DADOS->vavistaprazo == "A" && isset($ccnome) ? $ccnome : "" ?>"/>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="loadcc" style="display: none;"></div>

                <div id="cclabel" class="labelComplete-field" style="display: none;"></div>
            </div>
            <div class="col-md-3" style="margin-top: 30px" id="divEspecie">
                <label>Espécie</label>
                <input type="text" id="espId" style="display:none" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" name="especieid" value="<?php echo isset($DADOS->vavistaprazo) && $DADOS->vavistaprazo == "A" && isset($espCod) ? $espCod : "" ?>"/>
                <input id="esp" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="especie" value="<?php echo isset($DADOS->vavistaprazo) && $DADOS->vavistaprazo == "A" && isset($espnome) ? $espnome : "" ?>"/>
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
                        <label>Cobrar Frete do Cliente?</label><select style="margin-bottom: 10px;" id="vnftipofrete"  name ="vnftipofrete" class="form-control" value="<?php echo $DADOS->vnftipofrete; ?>">
                            <option  value="N" <?= ($DADOS->vnftipofrete == "N") ? "selected" : ""; ?> <?php echo $pago; ?>>
                                Não
                            </option>
                            <option <?php echo $pago; ?> value="S" <?= ($DADOS->vnftipofrete == "S") ? "selected" : ""; ?> <?php echo $pago; ?>>
                                Sim
                            </option>

                        </select>
                    </div>
                    <div class="col-md-5" id="">
                        <input type="text" style="margin-bottom: 10px; display: none" autocomplete="off" class="form-control" id="vnfcodtransportadora" name="vnfcodtransportadora" value="<?php echo $DADOS->vnfcodtransportadora; ?>">
                        <label>Transportadora</label><input <?php echo $pago; ?> type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" id="vnftransportadora" name="vnftransportadora" value="<?php echo $DADOS->vnftransportadora; ?>">
                    </div>
                    <!--                    <div class="col-md-4" id="">
                                            <label>Valor do Frete</label><input <?php echo $pago; ?> type="text" placeholder="R$" class="form-control" id="vnfvalorfrete" name="vnfvalorfrete" value="<?php echo $DADOS->vnfvalorfrete; ?>">
                                        </div>-->
                    <div class="col-md-4" id="">
                        <label>Número do Pedido</label><input type="text" placeholder="" class="form-control" id="vordemcompra" name="vordemcompra" value="<?php echo $DADOS->vordemcompra; ?>"/>
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
                        <!--<label>Nossas Observações</label><textarea class="form-control" rows="3" id="vobsempresa" name="vobsempresa"></textarea>-->
                        <label>Nossas Observações</label><textarea <?php echo $pago; ?> class="form-control" rows="3" id="vobsempresa" name="vobsempresa"><?php echo $DADOS->vobsempresa; ?></textarea>
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
                <input id="vendedor" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="vendedor" value="<?php echo $DADOS->vvendedornome ?>"/>
                <div id="labelAddVendedor" style="display: none;position: absolute;width: 96.6%;margin-top: -12px;color: #FFF;padding: 10px 8px;z-index: 11111;top: 8px;right: 0px;background: #18B5C2 none repeat scroll 0% 0%;border-radius: 2px 2px 0px 0px !important;">
                    <span>Adicionar</span>
                    <span id="textCompFornecedor" style="font-weight: bold"></span>
                    <button type='button' name='confirma' id='confirma' class='pull-right' style='margin-top: -5px;color: #FCFCFC;background: transparent none repeat scroll 0% 0%;vertical-align: middle;margin-bottom: -13px;border: 0px none;'>
                        <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                    </button>
                </div>
                <div class="fa fa-refresh fa-spin autocompleteLoading" id="pload" style="display:none;"></div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <button type="submit" name="enviar" id="enviar" class="btn btn-success" style="width: 169px;height: 54px;" <?php echo $pago ?>>
                        <i class="mdi-navigation-check" style="font-size: 25px;margin-left: -68px;margin-top: -17px;position: absolute;"></i>
                        <span style="position: absolute;margin-top: -5px;margin-left: -35px;">Salvar Alterações</span></button>
                    <a href="<?php echo $URL_BASE_PATH ?>/modulos/venda/consulta.php" class="btn btn-primary pull-right" style="height: 54px;width: 177px;">
                        <i class="mdi-navigation-arrow-back" style="font-size: 25px;margin-left: -75px;margin-top: -5px;position: absolute;"></i>
                        <span style="position: absolute;margin-top: 5px;margin-left: -45px;">Voltar à Listagem</span></a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
require_once 'script.php';
?>
<script>
    $(function () {
        var $form = $('#formVenda'), erro = false, gerafin = '<?php echo $gerafin; ?>', vistaprazo = '<?php echo $DADOS->vavistaprazo ?>';

        if (vistaprazo.length > 0 && vistaprazo == "P") {
            $('#divEspecie').hide("slow");
        }

        if (gerafin === 'S') {
            $('#divCB').show();
            $('#divCF').show();
            $('#DivFormPag').show();

            $('#ContBanc').attr("data-required", "required");
            $('#ContBanc').show();
            $('#ClassiFin').attr("data-required", "required");

            $('#ClassiFin').show();
        } else if (gerafin === 'N') {

            $('#divCB').hide();
            $('#divCF').hide();
            $('#DivFormPag').hide();


            $('#ContBanc').removeAttr('data-required');
            $('#ContBanc').removeClass("error");
            $('#ContBanc').hide();
            $('#ClassiFin').removeAttr('data-required');
            $('#ClassiFin').removeClass("error");
            $('#ClassiFin').hide();
            $('#ContBancId').val(null);
            $('#ContBanc').val(null);
            $('#ClassiFinId').val(null);
            $('#ClassiFin').val(null);
        }

        $form.submit(function (e) {
            e.preventDefault();
            $form.find("input").each(function () {
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
                    $("#enviar").prop("disabled", true);
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/insere.php",
                        data: $(this).serialize(),
                        success: function () {
                            swal("Muito bem!", "Alterado com sucesso!", "success");
                            setTimeout(function () {
                                window.location.href = '<?php echo $URL_BASE_PATH ?>/modulos/venda/consulta.php';
                            }, 800);
                        }
                    });
                    return false;
                }
            }
        });
    });
</script>
<style>
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
<?php
include_once 'view/rodape.php';
