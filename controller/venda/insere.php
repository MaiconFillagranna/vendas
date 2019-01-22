<?php

date_default_timezone_set("America/Sao_Paulo");
require_once 'class/Funcoes.php';
require_once 'class/Formulario.php';
require_once 'class/CampoTexto.php';
require_once 'class/CampoDinheiro.php';
require_once 'class/CampoInteiro.php';
require_once 'class/ControllerComissao.php';
require_once 'modulos/especificos/locacaopallet/locacao/ControllerMaterialTerceiros.php';
require_once 'modulos/industrializacao/ControllerIndustrializacao.php';

$Cadastra = new CadastraVenda($DB_CONN);

$Cadastra->formulario = new Formulario();

// venda
$Cadastra->vcodcli = CampoInteiro::of(["nome" => "pessoaId", "obrigatorio" => true]);
$Cadastra->vnomecli = CampoTexto::of(["nome" => "pessoa", "obrigatorio" => true]);
$Cadastra->vdatapedido = CampoTexto::of("vdatapedido"); // TODO fazer campo data
$Cadastra->vprazoentrega = CampoTexto::of("vprazoentrega");
$Cadastra->vobsempresa = CampoTexto::of("vobsempresa");
$Cadastra->vvendedorcod = CampoInteiro::of("vendedorId");
$Cadastra->vvendedornome = CampoTexto::of("vendedor");
$Cadastra->vvalordesconto = CampoDinheiro::of("desconto");
$Cadastra->vvalorcusto = CampoDinheiro::of("totalcustogeral");
$Cadastra->tipooperacaoid = CampoTexto::of("tipooperacaoid");
$Cadastra->vvalormercadorias = CampoDinheiro::of(["nome" => "totalprodutos", "obrigatorio" => true]);
$Cadastra->vvalortotal = CampoDinheiro::of(["nome" => "totalpagar", "obrigatorio" => true]);
$Cadastra->vclassifin = CampoInteiro::of("ClassiFinId");
$Cadastra->vpedido = CampoTexto::of("numeroPedido");
$Cadastra->vnfchavedevolucao = CampoTexto::of("chaveNotaEntrada");

$Cadastra->vnftipofrete = CampoTexto::of("vnftipofrete");
$Cadastra->vnfcodtransportadora = CampoInteiro::of("vnfcodtransportadora");
$Cadastra->vnftransportadora = CampoTexto::of("vnftransportadora");
$Cadastra->vnfvalorfrete = CampoDinheiro::of("vnfvalorfrete");
$Cadastra->vvaloracrescimo = CampoDinheiro::of("vvaloracrescimo");

$Cadastra->vcontabancariacod = CampoInteiro::of("ContBancId");
$Cadastra->vcontabancarianome = CampoTexto::of("ContBanc");
$Cadastra->vordemcompra = CampoTexto::of("vordemcompra");
$Cadastra->vespecieid = CampoTexto::of("especieid");
$Cadastra->vcentrocustoid = CampoTexto::of("centrocustoid");

$Cadastra->formulario->addCampos($Cadastra->vcodcli, $Cadastra->vnomecli, $Cadastra->vdatapedido, $Cadastra->vprazoentrega, $Cadastra->vobsempresa, $Cadastra->vvendedorcod, $Cadastra->vvendedornome, $Cadastra->vvalordesconto, $Cadastra->vvalormercadorias, $Cadastra->vvalortotal, $Cadastra->vnftipofrete, $Cadastra->vnfcodtransportadora, $Cadastra->vnftransportadora, $Cadastra->vnfvalorfrete,$Cadastra->vvaloracrescimo, $Cadastra->vcontabancariacod, $Cadastra->vcontabancarianome, $Cadastra->tipooperacaoid, $Cadastra->vespecieid,$Cadastra->vcentrocustoid);

// itens venda
$Cadastra->vicodservproduto = CampoInteiro::of(["nome" => "vicodservproduto", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vinome = CampoTexto::of(["nome" => "vinome", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->qtdnome = count($Cadastra->vinome);
$Cadastra->viquantidade = CampoDinheiro::of(["nome" => "viquantidade", "obrigatorio" => true, "multiplo" => true]);  // TODO fazer campo decimal
$Cadastra->vivalorcustototal = CampoDinheiro::of(["nome" => "totalcusto", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vivalordesconto = CampoDinheiro::of(["nome" => "vivalordesconto", "obrigatorio" => false, "multiplo" => true]);
$Cadastra->vivalipi = CampoDinheiro::of(["nome" => "valoripi", "obrigatorio" => false, "multiplo" => true]);
$Cadastra->vialiqipi = CampoDinheiro::of(["nome" => "porcentagemipi", "obrigatorio" => false, "multiplo" => true]);
//$Cadastra->vinfcfop = CampoInteiro::of(["nome" => "vinfcfop", "multiplo" => true]);  

$Cadastra->vivalorunitario = CampoDinheiro::of(["nome" => "vivalorunitario", "obrigatorio" => true, "multiplo" => true]);

$Cadastra->formulario->addCampos($Cadastra->vicodservproduto, $Cadastra->vinome, $Cadastra->viquantidade, $Cadastra->vivalorunitario);

// parcelas
$Cadastra->optionsRadios = CampoTexto::of(["nome" => "optionsRadios", "obrigatorio" => true]);
$Cadastra->vfdatavencimento = CampoTexto::of(["nome" => "dataparcela", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vfvalor = CampoDinheiro::of(["nome" => "valorparcela", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vfcusto = CampoDinheiro::of(["nome" => "valorcusto", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vfobs = CampoTexto::of(["nome" => "obsparcela", "multiplo" => true]);
$Cadastra->qtdparcela = count($Cadastra->vfdatavencimento);
$Cadastra->vfclassifin = CampoInteiro::of(["nome" => "classfinIDP", "obrigatorio" => true, "multiplo" => true]);
$Cadastra->vfespecieid = CampoInteiro::of(["nome" => "especiecod", "obrigatorio" => false, "multiplo" => true]);
$Cadastra->vfcentrocusto = CampoInteiro::of(["nome" => "centrocustocod", "obrigatorio" => false, "multiplo" => true]);
$Cadastra->vfdias = CampoTexto::of(["nome" => "diasparcela", "multiplo" => true]);

$Cadastra->formulario->addCampos($Cadastra->optionsRadios, $Cadastra->vfdatavencimento, $Cadastra->vfvalor, $Cadastra->vfobs, $Cadastra->vfespecieid,$Cadastra->vfcentrocusto);

iniciaTranzacao();
try {
    $Cadastra->inserir();

    echo json_encode([
        "status" => "success"
    ]);

    confirmaTranzacao();
} catch (Exception $ex) {
    $arr = array(
        "status" => "error",
        "erros" => $Cadastra->erros
    );
    echo json_encode($arr);
    reverterTranzacao();
}

class CadastraVenda {

    public $dbConn;

    /**
     * @var Formulario
     */
    public $formulario;
    public $erros = [];
    public $codigo;
    public $codigofin;
    public $vvalorcusto;
    public $vclassifin;
    public $vfclassifin;
    public $vfespecieid;
    public $vfcentrocusto;
    public $vpedido;
    public $vfdias;
    public $vnfchavedevolucao;
    /** @var Campo */
    public $vcodcli;

    /** @var Campo */
    public $vnomecli;

    /** @var Campo */
    public $vdatapedido;

    /** @var Campo */
    public $vprazoentrega;

    /** @var Campo */
    public $vicodservproduto;

    /** @var Campo */
    public $vinome;

    /** @var Campo */
    public $viquantidade;
    public $vivalorcustototal;
    public $vivalordesconto;
    public $vivalipi;
    public $vialiqipi;
    public $vinfcfop;

    /** @var Campo */
    public $vivalorunitario;
    public $qtdnome;

    /** @var Campo */
    public $vobsempresa;

    /** @var Campo */
    public $vassinatura;

    /** @var Campo */
    public $vvendedorcod;

    /** @var Campo */
    public $vvendedornome;

    /** @var Campo */
    public $vvalordesconto;

    /** @var Campo */
    public $vvalormercadorias;

    /** @var Campo */
    public $vvalortotal;

    /** @var Campo */
    public $optionsRadios;

    /** @var Campo */
    public $vavistaprazo;

    /** @var Campo */
    public $vfparcela;

    /** @var Campo */
    public $vfdatavencimento;

    /** @var Campo */
    public $vfvalor;

    /** @var Campo */
    public $vfobs;
    public $vfcusto;
    public $qtdparcela;
    public $vnftipofrete;
    public $vordemcompra;
    public $vnfcodtransportadora;
    public $vnftransportadora;
    public $vnfvalorfrete;
    public $vvaloracrescimo;
    public $vcontabancariacod;
    public $vcontabancarianome;
    public $cfop;
    public $pesoliq;
    public $pesobruto;
    public $mecodigo;
    public $tipooperacaoid;
    public $geraFinanceiro;
    public $geraEstoque;
    public $classificacaofin;
    public $comissao;
    public $tooperacao;
    public $unidademedida;
    public $ncm;
    public $prodgeraestoque;
    public $classificacaofincusto;
    public $comissao_item;
    public $comissao_tot;
    public $percentual_calculado_item;
    public $vespecieid;
    public $vcentrocustoid;
    public $tooperacaoterceiros;

    public function __construct($dbConn) {
        $this->dbConn = $dbConn;
    }

    public function BuscaInformacoesProduto($CodProd) {
        $empresa = $_SESSION['codigo'];
        $sql = "SELECT * FROM produtoservico WHERE nccodigo = $empresa and prodservcodigo = $CodProd";
        $resultado = executaSqlTransacao($sql);
        while ($reg = pg_fetch_assoc($resultado)) {
            $this->cfop = $reg["prodservcfoppadrao"];
            $this->pesoliq = $reg["prodservpesoliquido"];
            $this->pesobruto = $reg["prodservpesobruto"];
            $this->unidademedida = $reg["prodservumcodigo"];
            $this->ncm = $reg["prodservncm"];
            $this->prodgeraestoque = $reg["prodservatualizaestoque"];
        }
        limpaQuery($resultado);
    }

    public function verificaTOP() {
        $empresa = $_SESSION['codigo'];
        $sql = 'SELECT * FROM tipooperacao WHERE tocodigo = $1 AND nccodigo = $2';
        $tooperacao = executaSqlTransacao($sql, [
            $this->tipooperacaoid->getValor(),
            $empresa
        ]);
        while ($operacao = pg_fetch_object($tooperacao)) {
            $this->geraEstoque = $operacao->tomovimentaestoque;
            $this->geraFinanceiro = $operacao->togerafinanceiro;
            $this->classificacaofin = $operacao->toclassificacaofinanceira;
            $this->classificacaofincusto = $operacao->toclassificacaofinanceiracusto;
            $this->comissao = $operacao->tocomissao;
            $this->tooperacao = $operacao->tooperacao;
            $this->tooperacaoterceiros = $operacao->tooperacaoterceiros;
        }
    }

    public function inserir() {
        if (!$this->formulario->isValido()) {
            $this->erros = $this->formulario->getErrors();
            throw new Exception("Dados Inválidos!");
        }

        $empresa = $_SESSION['codigo'];

        $this->codigo = buscaProximoCodigo('vcodigo', 'venda');

        if ($this->vnfvalorfrete->getValor() > 0) {
            $tipofrete = 0;
        } else {
            $tipofrete = 9;
        }
        $sql = "INSERT INTO venda ("
                . "nccodigo,vcodigo,vparcial,vprodserv,vcodcli,"
                . "vnomecli,vdatapedido,vprazoentrega,vtipo,"
                . "vobsempresa,vavistaprazo,"
                . "vvendedorcod,vvendedornome,vvalordesconto,"
                . "vvalormercadorias,vvalortotal,varquivado,vnftipofrete,"
                . "vnfcodtransportadora,vnftransportadora,vnfvalorfrete, vcontabancariacod, vcontabancarianome, "
                . "vatualizada, vnftipooperacao, vordemcompra, vvalorcusto, vclassifin, vpedido, vvaloracrescimo, "
                . "vnfoutrasdespesas, vnfchavedevolucao) VALUES ("
                . "$1,$2,$3,$4,$5,"
                . "$6,to_date($7, 'DD/MM/YYYY'),$8,$9,$10,"
                . "$11,$12,$13,$14,"
                . "$15,$16,$17,$18,$19,$20,$21,$22,$23,$24, $25, $26, "
                . "$27, $28, $29, $30, $31, $32)";

        executaSqlTransacao($sql, [
            $empresa, $this->codigo, 0, 'P', $this->vcodcli->getValor(),
            $this->vnomecli->getValor(), $this->vdatapedido->getValor(), $this->vprazoentrega->getValor(), 'V',
            $this->vobsempresa->getValor(), $this->optionsRadios->getValor(),
            $this->vvendedorcod->getValor(), $this->vvendedornome->getValor(), $this->vvalordesconto->getValor(),
            $this->vvalormercadorias->getValor(), $this->vvalortotal->getValor(), 'N', $tipofrete,
            $this->vnfcodtransportadora->getValor(), $this->vnftransportadora->getValor(), $this->vnfvalorfrete->getValor(),
            $this->vcontabancariacod->getValor(), $this->vcontabancarianome->getValor(), 'S', $this->tipooperacaoid->getValor(),
            $this->vordemcompra->getValor(), $this->vvalorcusto->getValor(), $this->vclassifin->getValor(), $this->vpedido->getValor(), 
            $this->vvaloracrescimo->getValor(), $this->vvaloracrescimo->getValor(), $this->vnfchavedevolucao->getValor()
        ]);

        $this->verificaTOP();
        $this->inserirItens();

        $this->codigofin = buscaProximoCodigo('fincodigo', 'financeiro');

        if ($this->optionsRadios->getValor() == 'P') {
            $this->inserirFinanceiro();
        } else {
            $this->inserirFinanceiroAvista();
        }
        
        $Comissao = new ControllerComissao();
        $Comissao->addComissaoFromVenda($this->codigo);
        
        if($this->tooperacaoterceiros == 'RL') {
            $locacao = new MaterialTerceirosVenda();
            $locacao->insereRegistros($this->codigo);
        }
        
        if($this->tooperacaoterceiros == 'RI' || $this->tooperacaoterceiros == 'DI') {
            $industrializacao = new ControllerIndustrializacao();
            $industrializacao->insereRegistros($this->codigo, 
                                               'S'.$this->tooperacaoterceiros, 
                                               $this->vcodcli->getValor(), 
                                               $this->vdatapedido->getValor());
        }
    }

    public function inserirItens() {
        $empresa = $_SESSION['codigo'];

        $sql = "INSERT INTO vendaitens (nccodigo, vcodigo, vicodigo, vparcial, vicodservproduto, vinome, vivalorunitario, viquantidade, vinfcfop, vinfpesoliquido, vinfpesobruto, vium, vincm, vivalorcustototal,vimovestoque, vivalordesconto, vipedido, vipedidoitem, vivalipi, vialiqipi)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, $20)";

        $sql2 = "UPDATE produtoservico SET prodservsaldoestoque = $1 WHERE nccodigo = $2 AND prodservcodigo = $3";

        $sql4 = "SELECT prodservvalorcusto FROM produtoservico WHERE prodservcodigo = $1 AND nccodigo = $2";

        for ($i = 0; $i < $this->qtdnome; $i++) {
            $custo = executaSql($sql4, [
                $this->vicodservproduto[$i]->getValor(),
                $empresa
            ]);
            $valorCusto = getLinhaQuery($custo)->prodservvalorcusto;

            $CodProd = $this->vicodservproduto[$i]->getValor();
            $this->BuscaInformacoesProduto($CodProd);

            if ($this->geraEstoque === 'S' && $this->prodgeraestoque == 'S') {
                $atualizaEstoque = 'S';
            } else {
                $atualizaEstoque = 'N';
            }

            $pesoliquido = $this->pesoliq * $this->viquantidade[$i]->getValor();
            $pesobruto = $this->pesobruto * $this->viquantidade[$i]->getValor();
           
            $x = $i + 1;
            executaSqlTransacao($sql, [
                $empresa,
                $this->codigo,
                $i,
                0,
                $this->vicodservproduto[$i]->getValor(),
                $this->vinome[$i]->getValor(),
                $this->vivalorunitario[$i]->getValor(),
                $this->viquantidade[$i]->getValor(),
                $this->cfop,
                $pesoliquido,
                $pesobruto,
                $this->unidademedida,
                $this->ncm,
                $this->vivalorcustototal[$i]->getValor(),
                $atualizaEstoque,
                $this->vivalordesconto[$i]->getValor(),
                $this->vpedido->getValor(),
                $x,
                $this->vivalipi[$i]->getValor(),
                $this->vialiqipi[$i]->getValor()
            ]);
            //<LuisFilipe/>
            if ($atualizaEstoque == 'S') {
                $this->qtdestoque = buscaEstoque($this->vicodservproduto[$i]->getValor());

                if ($this->tooperacao == 'E' || $this->tooperacao == 'V') {// tipo entrada ou devolução de cliente
                    $novaQtd = $this->qtdestoque + $this->viquantidade[$i]->getValor();
                } else {
                    $novaQtd = $this->qtdestoque - $this->viquantidade[$i]->getValor(); // saida
                }

                executaSqlTransacao($sql2, [
                    $novaQtd,
                    $empresa,
                    $this->vicodservproduto[$i]->getValor()
                ]);

                $sql3 = "INSERT INTO movestoque (nccodigo, mecodigo, mecodvendacompra, mecodigoprod, metipo, mefornecedor, mevalorunit,
            mevalorcusto, mequantidade, medata, menota,meespecie)
            VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, to_date($10, 'DD/MM/YYYY'), $11,$12)";

                $this->mecodigo = buscaProximoCodigo('mecodigo', 'movestoque');

                if ($this->tooperacao == 'E') {// tipo entrada
                    $tooperacao = 'E';
                    $especie = 'VDE';
                } else if ($this->tooperacao == 'V') {  //ou devolução de cliente
                    $tooperacao = 'E';
                    $especie = 'DVE';
                } else if ($this->tooperacao == 'C') {  //ou devolução de fornecedor
                    $tooperacao = 'S';
                    $especie = 'DVS';
                } else {
                    $tooperacao = 'S'; //saida
                    $especie = 'VDS';
                }
                executaSqlTransacao($sql3, [
                    $empresa,
                    $this->mecodigo,
                    $this->codigo,
                    $this->vicodservproduto[$i]->getValor(),
                    $tooperacao,
                    $this->vcodcli->getValor(),
                    $this->vivalorunitario[$i]->getValor(),
                    $valorCusto,
                    $this->viquantidade[$i]->getValor(),
                    $this->vdatapedido->getValor(),
                    $this->codigo,
                    $especie
                ]);
            }
            //<PHF Filtros>
            $sqlGeraContrato = executaSql("SELECT ncgeracontrato FROM nossocliente WHERE nccodigo = $empresa");
            $geraContrato = getLinhaQuery($sqlGeraContrato)->ncgeracontrato;
            if ($geraContrato == "S") {
                $sqlGetProdServCodCli = executaSql("SELECT * FROM produtoservico WHERE nccodigo = $empresa AND prodservcodigo = $CodProd  AND prodservtipo = 'P'");
                $produto = getLinhaQuery($sqlGetProdServCodCli);
                $prodservcodcli = $produto->prodservcodcli;
                //Pega o código e o nome do produto que está sendo vendido
                $prodservnome = $produto->prodservnome;
                //Executa sql para pegar informações desse produto do tipo serviço.
                $sqlGetProdServico = executaSql("SELECT * FROM produtoservico WHERE nccodigo = $empresa AND prodservcodcli = '$prodservcodcli' AND prodservnome ILIKE '$prodservnome' AND prodservtipo = 'S'");
                $prodservico = getLinhaQuery($sqlGetProdServico);
                if ($prodservico) {
                    $servCodCli = $prodservico->prodservcodcli;
                    $servCod = $prodservico->prodservcodigo;
                    $servNome = $prodservico->prodservnome;
                    $servQuantidade = $this->viquantidade[$i]->getValor();
                    $servValorUnit = $this->vivalorunitario[$i]->getValor();
                    $desconto = $this->vvalordesconto->getValor();

                    $qtdTotal = count($this->viquantidade);

                    $desconto = $desconto / $qtdTotal;
                    $servValorTotal = ($servValorUnit * $servQuantidade) - $desconto;
                    $codCliente = $this->vcodcli->getValor();
                    $sqlExisteServico = executaSql("SELECT ctcodigo FROM contrato ct LEFT JOIN contratoitens ci ON ci.cicontrato = ct.ctcodigo AND ci.nccodigo = $empresa WHERE ct.nccodigo = $empresa  AND ct.ctcliente = $codCliente AND cicodproduto = $servCod");
                    $ctcodigo = getLinhaQuery($sqlExisteServico)->ctcodigo;
                    if ($ctcodigo) {
                        $dataVenda = $this->vdatapedido->getValor();
                        //Format dataVenda
                        $formatDate = str_replace("/", "-", $dataVenda);
                        $dataVenda = date("Y-m-d", strtotime($formatDate));
                        $startDate = strtotime($dataVenda);
                        $sqlContrato = executaSql("SELECT * FROM contrato WHERE nccodigo = $empresa AND ctcodigo = $ctcodigo");
                        $contrato = getLinhaQuery($sqlContrato);
                        $periodicidade = $contrato->ctperiodicidade;
                        $endDate = $this->calculaDataFinalContratoPHF($periodicidade, $startDate); //N trocar a ordem(Primeiro o endDate, depois o startDate);
                        $startDate = date('Y-m-d', $startDate);
                        $sqlUpdateContrato = "UPDATE contrato SET ctdataini = $3,ctdatafin = $4 WHERE nccodigo = $1 AND ctcodigo = $2";
                        executaSqlTransacao($sqlUpdateContrato, [$empresa, $ctcodigo, $startDate, $endDate]);
                    } else {
                        $sqlGetCpnjPHF = executaSql("SELECT nccnpj FROM nossocliente WHERE nccodigo = $empresa");
                        $cnpj = getLinhaQuery($sqlGetCpnjPHF)->nccnpj;
                        $cnpj = $this->mask($cnpj, '##.###.###/####-##');
                        $sqlGetCodPHF = executaSql("SELECT clicodigo FROM cliente WHERE nccodigo = $empresa AND clicpfcnpj = '$cnpj'");
                        $codPhf = getLinhaQuery($sqlGetCodPHF)->clicodigo;
                        $sqlGetContaBancaria = executaSql("SELECT cbcodigo FROM contabancaria WHERE nccodigo = $empresa AND cbpadrao = 1");
                        $cbcod = getLinhaQuery($sqlGetContaBancaria)->cbcodigo;

                        $sqlGetContrato = "SELECT * FROM contrato ct LEFT JOIN contratoitens ci ON ci.cicontrato = ct.ctcodigo AND ci.nccodigo = $empresa WHERE ct.nccodigo = $empresa AND ct.ctcliente = $codPhf AND cicodproduto = $servCod";
                        $contrato = pg_query($sqlGetContrato);
                        $k = 0;
                        while ($reg = pg_fetch_assoc($contrato)) {
                            $tipoOperacao = $reg["cttop"];
                            $periodicidade = $reg["ctperiodicidade"];
                            $dataVenda = $this->vdatapedido->getValor();
                            //Format dataVenda
                            $formatDate = str_replace("/", "-", $dataVenda);
                            $dataVenda = date("Y-m-d", strtotime($formatDate));
                            $startDate = strtotime($dataVenda);
                            $endDate = $this->calculaDataFinalContratoPHF($periodicidade, $startDate); //N trocar a ordem(Primeiro o endDate, depois o startDate);
                            $startDate = date('Y-m-d', $startDate);
                            $vendedor = $reg["ctvendedor"];
                            $codigoContrato = buscaProximoCodigo('ctcodigo', 'contrato');
                            $nomeCliente = $this->vnomecli->getValor();
                            $descricao = "CONTRATO $nomeCliente";
                            $sqlInsereContrato = "INSERT INTO contrato (nccodigo, ctcodigo, ctcliente, ctdescricao, ctperiodicidade, ctcontabancaria, ctdataini,
                        ctdatafin, ctvalor, ctativo, cttop, ctvalorprod, ctvalordesconto, ctvendedor) VALUES ($1, $2, $3, $4, $5, $6, $7, $8,
                        $9, $10, $11, $12, $13, $14)";
                            executaSqlTransacao($sqlInsereContrato, [
                                $empresa,
                                $codigoContrato,
                                $codCliente,
                                $descricao,
                                $periodicidade,
                                $cbcod,
                                $startDate,
                                $endDate,
                                $servValorTotal,
                                "S",
                                $tipoOperacao,
                                $servValorTotal,
                                $desconto,
                                $vendedor
                            ]);
                            $sqlInsereContratoItens = "INSERT INTO contratoitens (nccodigo, cicontrato, cicodigo, cicodproduto, cinomeprod, ciqtdunit, civalorunit, civalortotal) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
                            executaSqlTransacao($sqlInsereContratoItens, [
                                $empresa,
                                $codigoContrato,
                                $k,
                                $servCod,
                                $servNome,
                                $servQuantidade,
                                $servValorUnit,
                                $servValorTotal
                            ]);
                            $codigoHistoricoCliente = buscaProximoCodigo('hccodigo', 'historicocliente');
                            $hcDescricao = "VENDA Nº " . $this->codigo . " - Produto " . $servNome . " - Quantidade " . $servQuantidade;
                            $hrAtual = date("H:i:s");
                            $sqlInsereHistoricoCliente = "INSERT INTO historicocliente(nccodigo,hccodigo,hccliente,hcdescricao,hcagente,hchora,hcdata) VALUES ($1,$2,$3,$4,$5,$6,$7)";
                            executaSqlTransacao($sqlInsereHistoricoCliente, [
                                $empresa,
                                $codigoHistoricoCliente,
                                $codCliente,
                                $hcDescricao,
                                $vendedor,
                                $hrAtual,
                                $startDate
                            ]);
                            $k++;
                            limpaQuery($sqlInsereContrato);
                            limpaQuery($sqlInsereContratoItens);
                        }
                    }
                }
            }
            //</PHP FILTROS>
        }
//        if ($this->vvalordesconto->getValor()) { // FUNCAO RATEIO DE DESCONTO DE ITENS PARA A NOTA || LUIS
        $acrescimo = $this->vvaloracrescimo->getValor();
        $frete = $this->vnfvalorfrete->getValor();
        $valormercadorias = $this->vvalormercadorias->getValor();
        
        $percentual_acrescimo = $acrescimo * 100 / $valormercadorias;
        $percentual_frete = $frete * 100 / $valormercadorias;

        $sql = 'SELECT * FROM vendaitens WHERE nccodigo = $1 AND vcodigo = $2 order by (viquantidade * vivalorunitario)';
        $retorno = executaSqlTransacao($sql, [
            $empresa,
            $this->codigo
        ]);
        $acrescimo_aux = 0;
        $frete_aux = 0;
        $cont = 0;

        while ($vendaitens = pg_fetch_object($retorno)) {
            $vicodigo = $vendaitens->vicodigo;
            $valorTotItem = $vendaitens->viquantidade * $vendaitens->vivalorunitario;            

            $valorFreteItem = $valorTotItem * $percentual_frete / 100;
            $valorFreteItem = intval($valorFreteItem * 100) / 100;
            $frete_aux = $frete_aux + $valorFreteItem;
            
            $valorAcrescimoItem = $valorTotItem * $percentual_acrescimo / 100;
            $valorAcrescimoItem = intval($valorAcrescimoItem * 100) / 100;
            $acrescimo_aux = $acrescimo_aux + $valorAcrescimoItem;

            $sql = "UPDATE vendaitens SET vivalorfrete = $4, vivaloracrescimo = $5 WHERE nccodigo = $1 AND vcodigo = $2 AND vicodigo = $3";
            executaSqlTransacao($sql, [
                $empresa,
                $this->codigo,
                $vicodigo,
                $valorFreteItem,
                $valorAcrescimoItem
            ]);

            if ($this->comissao == 'R' && $this->optionsRadios->getValor() == 'P') { // se recebe comissão por recebimento COMISSAO 02/05/2017
                $comissao = $this->regras_comissao($vendaitens->vicodservproduto);
                $total_prod = $valorTotItem - $vendaitens->vivalordesconto;
                $comissao_item = ($total_prod * ($comissao / 100));
                $this->comissao_item[$cont] = $comissao_item;
                $this->comissao_tot = $this->comissao_tot + $comissao_item;
                $cont ++;
            }
        }

        if (($acrescimo_aux <> $acrescimo)) { //pega o resto para somar na item com valor maior
            $resto_acrescimo = $acrescimo - $acrescimo_aux;
            $valorAcrescimoItem = $valorAcrescimoItem + $resto_acrescimo;
            $sql = "UPDATE vendaitens SET vivaloracrescimo = $4 WHERE nccodigo = $1 AND vcodigo = $2 AND vicodigo = $3";
            executaSqlTransacao($sql, [
                $empresa,
                $this->codigo,
                $vicodigo,
                $valorAcrescimoItem
            ]);
        }
        if ($frete_aux <> $frete) { //pega o resto para somar na item com valor maior
            $resto_frete = $frete - $frete_aux;
            $valorFreteItem = $valorFreteItem + $resto_frete;
            $sql = "UPDATE vendaitens SET vivalorfrete = $4 WHERE nccodigo = $1 AND vcodigo = $2 AND vicodigo = $3";
            executaSqlTransacao($sql, [
                $empresa,
                $this->codigo,
                $vicodigo,
                $valorFreteItem
            ]);
        }
        if ($this->comissao == 'R' && $this->optionsRadios->getValor() == 'P') { // se recebe comissão por recebimento COMISSAO 02/05/2017
            for ($i = 0; $i < $this->qtdnome; $i++) {
                $this->percentual_calculado_item[$i] = ($this->comissao_item[$i] / $this->comissao_tot);
            }
        }
//        }if desconto
    }

    private function calculaDataFinalContratoPHF($periodicidade, $startDate) {
        if ($periodicidade == 1) { //Diário;
            $endDate = strtotime("+1 day", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 2) { //Semanal
            $endDate = strtotime("+7 day", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 3) { //Quinzenal
            $endDate = strtotime("+15 day", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 4) { //Mensal
            $endDate = strtotime("+1 month", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 5) { //Bimestral
            $endDate = strtotime("+2 month", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 6) { //Trimestral
            $endDate = strtotime("+3 month", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 11) { //Quadrimesral
            $endDate = strtotime("+4 month", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 7) { //Semestral
            $endDate = strtotime("+6 month", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 8) { //Anual
            $endDate = strtotime("+1 year", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 9) { //2 anos
            $endDate = strtotime("+2 year", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 12) { // 3 Anos
            $endDate = strtotime("+3 year", $startDate);
            $endDate = date('Y-m-d', $endDate);
        } else if ($periodicidade == 10) { //5 Anos
            $endDate = strtotime("+5 year", $startDate);
            $endDate = date('Y-m-d', $endDate);
        }

        return $endDate;
    }

    public function inserirFinanceiro() {
        $empresa = $_SESSION['codigo'];
        $parcela = 0;

        for ($i = 0; $i < $this->qtdparcela; $i++) {
            $parcela = $parcela + 1;
            if (!empty($this->vfespecieid[$i]->getValor())) {
                $especiecod = $this->vfespecieid[$i]->getValor();
            } else {
                $especiecod = null;
            }
            if (!empty($this->vfcentrocusto[$i]->getValor())) {
                $centrocustocod = $this->vfcentrocusto[$i]->getValor();
            } else {
                $centrocustocod = null;
            }
            $dias = $this->vfdias[$i]->getValor() ? $this->vfdias[$i]->getValor() : null;
            $sql3 = "INSERT INTO vendafinanceiro (nccodigo, vcodigo, vfparcela, vparcial, vfvalor, vfobs, vfdatavencimento, vfclassifin, vftipopagamento, vfcusto,vfespcodigo,vfcentrocusto, vfdias)
                         VALUES ($1, $2, $3, $4, $5, $6, to_date($7, 'DD/MM/YYYY'), $8, $9, $10,$11,$12,$13)";
            executaSqlTransacao($sql3, [
                $empresa,
                $this->codigo,
                $parcela,
                0,
                $this->vfvalor[$i]->getValor(),
                $this->vfobs[$i]->getValor(),
                $this->vfdatavencimento[$i]->getValor(),
                $this->vfclassifin[$i]->getValor(),
                'P',
                $this->vfcusto[$i]->getValor(),
                $especiecod,
                $centrocustocod,
                $dias
            ]);

            if ($this->geraFinanceiro === 'S') {
                if ($this->tooperacao == 'E') {// tipo entrada
                    $fintipo = 'D'; // Entrada
                    $descricao = 'Entrada de Produtos ' . $this->codigo;
                } else {
                    $fintipo = 'R'; // Saida
                    $descricao = 'Venda ' . $this->codigo;
                }

                $sql4 = "INSERT INTO financeiro (nccodigo, fincodigo, finparcela, fintipo, fincodpessoa, finnomepessoa, findescricao, fincodclassificacao,"
                        . " findatavencimento, findatamovimento, finstatus, fincontbancocod, fincontbancnome, finvalor,"
                        . " fcodvenda, fintransferencia, fincusto, fincodclassificacaocusto, findatacompetencia,finespcodigo,fincentrocustocod,findataemissao,finqtdparcelas)"
                        . " VALUES ($1, $2, $3, $4, $5, $6, $7, $8, to_date($9, 'DD/MM/YYYY'), to_date($10, 'DD/MM/YYYY'), $11, $12, $13, $14, $15, $16, $17, $18, to_date($19, 'DD/MM/YYYY'),$20,$21,to_date($22, 'DD/MM/YYYY'),$23)";

                executaSqlTransacao($sql4, [
                    $empresa,
                    $this->codigofin,
                    $parcela,
                    $fintipo,
                    $this->vcodcli->getValor(),
                    $this->vnomecli->getValor(),
                    $descricao,
                    $this->vfclassifin[$i]->getValor(),
                    $this->vfdatavencimento[$i]->getValor(),
                    $this->vfdatavencimento[$i]->getValor(),
                    'A',
                    $this->vcontabancariacod->getValor(),
                    $this->vcontabancarianome->getValor(),
                    $this->vfvalor[$i]->getValor(),
                    $this->codigo,
                    'N',
                    $this->vfcusto[$i]->getValor(),
                    $this->classificacaofincusto,
                    $this->vdatapedido->getValor(),
                    $especiecod,
                    $centrocustocod,
                    $this->vdatapedido->getValor(),
                    $this->qtdparcela
                ]);
            }
        }
    }

    public function inserirFinanceiroAvista() {
        $empresa = $_SESSION['codigo'];
        $parcela = 1;
        if (!empty($this->vespecieid->getValor())) {
            $especiecod = $this->vespecieid->getValor();
        } else {
            $especiecod = null;
        }
        if (!empty($this->vcentrocustoid->getValor())) {
            $centrocustocod = $this->vcentrocustoid->getValor();
        } else {
            $centrocustocod = null;
        }
        $sql3 = "INSERT INTO vendafinanceiro (nccodigo, vcodigo, vfparcela, vparcial, vfvalor, vfobs, vfdatavencimento, vftipopagamento, vfclassifin, vfcusto,vfespcodigo,vfcentrocusto)
                     VALUES ($1, $2, $3, $4, $5, $6, to_date($7, 'DD/MM/YYYY'), $8, $9, $10,$11,$12)";
        executaSqlTransacao($sql3, [
            $empresa,
            $this->codigo,
            $parcela,
            0,
            $this->vvalortotal->getValor(),
            'PAGAMENTO À VISTA',
            $this->vdatapedido->getValor(),
            'A',
            $this->vclassifin->getValor(),
            $this->vvalorcusto->getValor(),
            $especiecod,
            $centrocustocod
        ]);

        if ($this->geraFinanceiro === 'S') {

            if ($this->tooperacao == 'E') {// tipo entrada
                $fintipo = 'D'; // Entrada
                $descricao = 'Entrada de Produtos ' . $this->codigo;
            } else {
                $fintipo = 'R'; // Saida
                $descricao = 'Venda ' . $this->codigo;
            }
            $empresa = $_SESSION['codigo'];

            $parcela = 1;

            $sql4 = "INSERT INTO financeiro (nccodigo, fincodigo, finparcela, fintipo, fincodpessoa, finnomepessoa, findescricao, fincodclassificacao, findatavencimento,findatamovimento, findatacompetencia, finstatus, fincontbancocod, fincontbancnome, finvalor, fcodvenda, fintransferencia, fincodclassificacaocusto, fincusto,finespcodigo,fincentrocustocod,findataemissao,finqtdparcelas)"
                    . " VALUES ($1, $2, $3, $4, $5, $6, $7, $8, to_date($9, 'DD/MM/YYYY'), to_date($10, 'DD/MM/YYYY'), to_date($11, 'DD/MM/YYYY'), $12, $13, $14, $15, $16, $17, $18, $19,$20,$21,to_date($22, 'DD/MM/YYYY'),$23)";
            executaSqlTransacao($sql4, [
                $empresa,
                $this->codigofin,
                $parcela,
                $fintipo,
                $this->vcodcli->getValor(),
                $this->vnomecli->getValor(),
                $descricao,
                $this->classificacaofin,
                $this->vdatapedido->getValor(),
                $this->vdatapedido->getValor(),
                $this->vdatapedido->getValor(),
                'A',
                $this->vcontabancariacod->getValor(),
                $this->vcontabancarianome->getValor(),
                $this->vvalortotal->getValor(),
                $this->codigo,
                'N',
                $this->classificacaofincusto,
                $this->vvalorcusto->getValor(),
                $especiecod,
                $centrocustocod,
                $this->vdatapedido->getValor(),
                $parcela
            ]);
        }
    }
    
    public function regras_comissao($prodcod) {
        $empresa = $_SESSION['codigo'];
        $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor = $2 AND rcproduto = $3 AND rccliente = $4", [$empresa, $this->vvendedorcod->getValor(), $prodcod, $this->vcodcli->getValor()]);
        if ($dados = pg_fetch_object($sql)) {
            $comissao = $dados->rcpercentual;
            return $comissao;
        } else {
            $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor = $2 AND rcproduto = $3 AND rccliente IS NULL", [$empresa, $this->vvendedorcod->getValor(), $prodcod]);
            if ($dados = pg_fetch_object($sql)) {
                $comissao = $dados->rcpercentual;
                return $comissao;
            } else {
                $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor = $2 AND rccliente = $3 AND rcproduto IS NULL", [$empresa, $this->vvendedorcod->getValor(), $this->vcodcli->getValor()]);
                if ($dados = pg_fetch_object($sql)) {
                    $comissao = $dados->rcpercentual;
                    return $comissao;
                } else {
                    $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor = $2 AND rcproduto IS NULL AND rccliente IS NULL", [$empresa, $this->vvendedorcod->getValor()]);
                    if ($dados = pg_fetch_object($sql)) {
                        $comissao = $dados->rcpercentual;
                        return $comissao;
                    } else {
                        $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor IS NULL AND rcproduto = $2 AND rccliente IS NULL", [$empresa, $prodcod]);
                        if ($dados = pg_fetch_object($sql)) {
                            $comissao = $dados->rcpercentual;
                            return $comissao;
                        } else {
                            $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor = $3 AND rcproduto = $2 AND rccliente IS NULL", [$empresa, $prodcod, $this->vvendedorcod->getValor()]);
                            if ($dados = pg_fetch_object($sql)) {
                                $comissao = $dados->rcpercentual;
                                return $comissao;
                            } else {
                                $sql = executaSql("SELECT rcpercentual FROM regrascomissao WHERE nccodigo = $1 AND rcvendedor IS NULL AND rcproduto IS NULL AND rccliente IS NULL", [$empresa]);
                                if ($dados = pg_fetch_object($sql)) {
                                    $comissao = $dados->rcpercentual;
                                    return $comissao;
                                } else {
                                    $comissao = NULL;
                                    return $comissao;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function addComissao($comissao, $baseComissao, $baseComissaoValor, $valorComissao, $comissionado, $parcelas, $produto, $vendedor, $cliente, $rctipo, $rcvalor, $x) {

        $dataHJ = date("Y-m-d");
        $empresa = $_SESSION['codigo'];
        if ($parcelas > 0 && $this->comissao === 'R' && $this->optionsRadios->getValor() == 'P') {

            if ($rctipo == 'V') { // por valor
                $baseComissao = $baseComissaoValor / $this->qtdparcela;
                $valorComissao = $baseComissao * ($rcvalor);
                for ($i = 0; $i < $this->qtdparcela; $i++) {
                    $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                    $parcela = $i + 1;
                    if ($comissao <> NULL) {
                        executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                            $empresa,
                            $this->codigoComissao,
                            $this->vfdatavencimento[$i]->getValor(),
                            $vendedor,
                            $produto,
                            $cliente,
                            $baseComissao,
                            $comissao,
                            $valorComissao,
                            'N',
                            $this->comissao,
                            $parcela,
                            $this->codigo,
                            $this->codigofin,
                            "Venda " . $this->codigo,
                            $rcvalor
                        ]);

                        if (isset($comissionado)) {
                            foreach ($comissionado as $com) {
                                $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                                if ($com->frctipo == 'V') {
                                    $valorComissaoC = round($baseComissao * ($com->frcvalor), 2);
                                } else {
                                    $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);
                                }


                                executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                                    $empresa,
                                    $this->codigoComissao,
                                    $this->vfdatavencimento[$i]->getValor(),
                                    $com->frccomissionado,
                                    $produto,
                                    $cliente,
                                    $baseComissao,
                                    $com->frcporcentagem,
                                    $valorComissaoC,
                                    'N',
                                    $this->comissao,
                                    $i,
                                    $this->codigo,
                                    $this->codigofin,
                                    "Venda " . $this->codigo,
                                    $com->frcvalor
                                ]);
                            }
                        }
                    }
                }
            } else { // por percentual
                $i = $x;

                $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                $parcela = $i + 1;
                if ($comissao <> NULL) {
                    executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                        $empresa,
                        $this->codigoComissao,
                        $this->vfdatavencimento[$x]->getValor(),
                        $vendedor,
                        $produto,
                        $cliente,
                        $baseComissao,
                        $comissao,
                        $valorComissao,
                        'N',
                        $this->comissao,
                        $parcela,
                        $this->codigo,
                        $this->codigofin,
                        "Venda " . $this->codigo,
                        $rcvalor
                    ]);

                    if (isset($comissionado)) {
                        foreach ($comissionado as $com) {
                            $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                            if ($com->frctipo == 'V') {
                                $valorComissaoC = round($baseComissao * ($com->frcvalor), 2);
                            } else {
                                $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);
                            }


                            executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                                $empresa,
                                $this->codigoComissao,
                                $this->vfdatavencimento[$x]->getValor(),
                                $com->frccomissionado,
                                $produto,
                                $cliente,
                                $baseComissao,
                                $com->frcporcentagem,
                                $valorComissaoC,
                                'N',
                                $this->comissao,
                                $i,
                                $this->codigo,
                                $this->codigofin,
                                "Venda " . $this->codigo,
                                $com->frcvalor
                            ]);
                        }
                    }
                }
            }
        } elseif ($parcelas > 0 && ($this->comissao === 'P')) {
            if ($rctipo == 'V') {
                $baseComissao = $baseComissaoValor / $this->qtdparcela;
            } else {
                $baseComissao = $baseComissao / $this->qtdparcela;
            }


            if ($rctipo == 'V') {
                $valorComissao = $baseComissao * ($rcvalor);
            } else {
                $valorComissao = $baseComissao * ($comissao / 100);
            }

//            $valorComissao = $baseComissao * ($comissao / 100);
            $vencimento = explode("/", $this->vdatapedido->getValor());
            $primeiroDia = $vencimento[0];
            $vencimento = $vencimento[2] . "-" . $vencimento[1] . "-" . $vencimento[0];
            $dias = [0, 30, 60];
            for ($i = 0; $i < $this->qtdparcela; $i++) {
                $dataFinal = date('Y-m-d', strtotime($vencimento . ' +' . $dias[$i] . ' days'));
                $dataFinal = explode("-", $dataFinal);
                $dataFinal = $dataFinal[0] . "-" . $dataFinal[1] . "-" . $primeiroDia;
                $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                $parcela = $i + 1;
                if ($comissao <> NULL) {
                    executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                        $empresa,
                        $this->codigoComissao,
                        $dataFinal,
                        $vendedor,
                        $produto,
                        $cliente,
                        $baseComissao,
                        $comissao,
                        $valorComissao,
                        'N',
                        'V',
                        $parcela,
                        $this->codigo,
                        $this->codigofin,
                        "Venda " . $this->codigo,
                        $rcvalor
                    ]);

                    if (isset($comissionado)) {
                        foreach ($comissionado as $com) {
                            $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                            $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                            if ($com->frctipo == 'V') {
                                $valorComissaoC = round($baseComissao * ($com->frcvalor), 2);
                            } else {
                                $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);
                            }
//                            $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);

                            executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                                $empresa,
                                $this->codigoComissao,
                                $dataFinal,
                                $com->frccomissionado,
                                $produto,
                                $cliente,
                                $baseComissao,
                                $com->frcporcentagem,
                                $valorComissaoC,
                                'N',
                                'V',
                                $i,
                                $this->codigo,
                                $this->codigofin,
                                "Venda " . $this->codigo,
                                $com->frcvalor
                            ]);
                        }
                    }
                }
            }
        } else {
            if (($this->comissao === 'P') or ( $this->comissao === 'R')) {
                $this->comissao = 'V';
            }
            $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
            $i = 1;
            if ($comissao <> NULL) {
                executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                    $empresa,
                    $this->codigoComissao,
                    $this->vdatapedido->getValor(),
                    $vendedor,
                    $produto,
                    $cliente,
                    $baseComissao,
                    $comissao,
                    $valorComissao,
                    'N',
                    $this->comissao,
                    $i,
                    $this->codigo,
                    $this->codigofin,
                    "Venda " . $this->codigo,
                    $rcvalor
                ]);


                if (isset($comissionado)) {
                    foreach ($comissionado as $com) {
                        $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                        $this->codigoComissao = buscaProximoCodigo('ffcodigo', 'filhofinanceiro');
                        if ($com->frctipo == 'V') {
                            $valorComissaoC = round($baseComissao * ($com->frcvalor), 2);
                        } else {
                            $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);
                        }
//                        $valorComissaoC = round($baseComissao * ($com->frcporcentagem / 100), 2);

                        executaSqlTransacao("INSERT INTO filhofinanceiro (nccodigo, ffcodigo, ffdata, fcomissionado, fproduto, fcliente, fbasecomissao,
                    fpercentual, fvalorcomissao, fapurada, ftipocomissao, ffparcela, ffcodvenda, ffvendfin, ffdescricao, ffbvalor) VALUES ($1, $2, to_date($3, 'DD/MM/YYYY'), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)", [
                            $empresa,
                            $this->codigoComissao,
                            $this->vdatapedido->getValor(),
                            $com->frccomissionado,
                            $produto,
                            $cliente,
                            $baseComissao,
                            $com->frcporcentagem,
                            $valorComissaoC,
                            'N',
                            $this->comissao,
                            $i,
                            $this->codigo,
                            $this->codigofin,
                            "Venda " . $this->codigo,
                            $com->frcvalor
                        ]);
                    }
                }
            }
        }
    }

    private function pegaComissionado($codigo, $condicoes) {
        $where = ' AND ' . implode(' AND ', $condicoes);
        $empresa = $_SESSION['codigo'];
        $sql1 = executaSql("SELECT rccodigo FROM regrascomissao WHERE nccodigo = $1 AND rccodigo = $2 $where", [$empresa, $codigo]);
        while ($dados = getLinhaQuery($sql1)) {
            $sql = executaSql("SELECT * FROM fregrascomissao WHERE nccodigo = $1 AND rccodigo = $2", [$empresa, $codigo]);
            $comissionado = [];
            $porcentagem = null;
            while ($dados = pg_fetch_object($sql)) {
                $comissionado[] = $dados;
            }

            return $comissionado;
        }
    }

    private function mask($val, $mask) {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

}
