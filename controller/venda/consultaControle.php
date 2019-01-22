<?php

require_once 'class/Filtro.php';
require_once 'class/CampoDinheiro.php';
require_once 'class/ParametroTexto.php';
require_once 'class/ParametroInteiro.php';

$EMPRESA = $_SESSION['codigo'];
$codUsu = $_SESSION['codigoUsuario'];
$tipoUsu = $_SESSION['tipoUsuario'];

$TOTAL_LINHAS = 0;
$LINHAS_CONSULTA = [];

function montaRestricaoBusca()
{
    global $EMPRESA;
    global $codUsu;
    global $tipoUsu;

    $filtroBusca = Filtro::of('search');
    $intervalo = Filtro::of('de');
    $intervalo2 = Filtro::of('ate');
    $usuario = Filtro::of('usuario');
    $exibir = Filtro::of('exibir');    

    $where = ['v.nccodigo = $1', 'cli.nccodigo = $1', 'v.vtipo = $2', 'v.vprodserv = $3', 'v.vnfcenumero is null'];
    $params = [$EMPRESA, 'V', 'P'];

    
    if ($exibir->getValor() !== 'T' ) {
         if ($exibir->getValor() == 'F' ) {
             $where[] = "v.vnfnumero > 0 and v.vnfstatus = 'A'";
         }else{
             $where[] = "((v.vnfnumero is null) or (v.vnfnumero > 0 and v.vnfstatus <> 'A')) ";
         }        
    }

    if ($tipoUsu === 'V') {
        $where[] = 'v.vvendedorcod =' . $codUsu;
    }
    if (!$usuario->isVazio()) {
        $where[] = "v.vvendedorcod = " . $usuario->getValor();
    }

    if (!$intervalo->isVazio() && !$intervalo2->isVazio()) {
        $where[] = "(v.vdatapedido BETWEEN '" . $intervalo->getValor() . "' AND '" . $intervalo2->getValor() . "')";
    }

    if (!$filtroBusca->isVazio()) {
        $trim = str_replace(' ', '', strtolower($filtroBusca->getValor()));
        if (ctype_alpha($trim)) {
//           $where[] = '(LOWER(cli.clinome) LIKE \'%\'||$4||\'%\' OR TO_CHAR(v.vdatapedido, \'DD/MM/YYYY\') = $4)';           
           $where[] = '(LOWER(cli.clinome) LIKE \'%\'||$4||\'%\')';
           $params[] = strtolower($filtroBusca->getValor());
        }else{
            $where[] = '(v.vcodigo = $4)';
            $params[] = strtolower($filtroBusca->getValor());
        }
    }

    return [
        ' WHERE ' . implode(' AND ', $where),
        $params
    ];
}

function buscaTotalRegistros($where, $params)
{
    $query = executaSql('
      SELECT
        COUNT(*) AS total
      FROM
        venda v
        LEFT JOIN cliente cli on v.vcodcli = cli.clicodigo
      ' . $where . '
    ', $params);

    $total = (float)getLinhaQuery($query)->total;

    limpaQuery($query);

    return $total;
}

function parseNomeBanco($nome)
{
    $nomes = [
        'codigo' => 'vcodigo',
        'nomeCliente' => 'vnomecli',
        'dataPedido' => 'vdatapedido',
        'prazoEntrega' => 'vprazoentrega',
        'valorTotal' => 'vvalortotal',
        'atualizada1' => 'vatualizada',
        'vnfnumero' => 'vnfnumero'
    ];

    if (isset($nomes[$nome])) {
        return $nomes[$nome];
    }

    return null;
}

function buscaRegistros($where, $params)
{
    $linhas = [];

    list($paramOffset, $paramLimit) = ParametroInteiro::of(
        ['nome' => 'offset', 'obrigatorio' => true], ['nome' => 'limit', 'obrigatorio' => true]);

    list($paramSort, $paramOrder) = ParametroTexto::of(
        'sort', 'order');

    $i = count($params);
    $sort = $paramSort->isVazio() ? 'vcodigo' : parseNomeBanco($paramSort->getValor());
    $order = $paramOrder->isVazio() ? 'DESC' : $paramOrder->getValor() == 'desc' ? 'ASC' : 'DESC';

    $query = executaSql('
      SELECT 
        v.vcodigo AS "codigo",
        cli.clinome AS "nomeCliente",
        TO_CHAR(v.vdatapedido, \'DD/MM/YYYY\') AS "dataPedido",
        v.vprazoentrega AS "prazoEntrega",
        v.vvalortotal AS "valorTotal",
        v.vatualizada as atualizada,
        v.vnfnumero AS "vnfnumero",
        v.vnfnumero AS "vnfnumero2",
        v.vnfemitida AS "vnfemitida",        
        v.vnfstatus AS "vnfstatus",
        v.voscod AS "numeroOS",
        v.vemail AS email,
        (SELECT
            COUNT(fin.*)
        FROM
            financeiro fin
        WHERE
            fin.nccodigo = v.nccodigo AND
            fin.fcodvenda = v.vcodigo AND
            fin.finstatus = \'P\') AS "totalParcelasPagas"
      FROM
        venda v
        LEFT JOIN cliente cli on v.vcodcli = cli.clicodigo
      ' . $where . '
      ORDER BY
        ' . $sort . ' ' . $order . '
      OFFSET
        $' . (++$i) . '
      LIMIT
        $' . (++$i) . '
    ', array_merge($params, [
        $paramOffset->getValor(),
        $paramLimit->getValor()]));

    while ($linha = getLinhaQuery($query)) {
        switch ($linha->atualizada) {
            case 'N':
                $linha->atualizada1 = '<i class="mdi-content-clear" style="font-size: 26px;color:RED;" title="Não Atualizada"></i>';
                break;
            case 'S':
                $linha->atualizada1 = '<i class="mdi-navigation-check" style="font-size: 26px;color:#5CB85C;" title="Atualizada"></i>';
                break;
        }

        switch ($linha->vnfstatus) {
            case 'A':
                $linha->vnfnumero2 = $linha->vnfnumero;
                $linha->statusDescricao = '<i class="mdi-navigation-check" style="font-size: 26px;margin-top: -7px;color:#5CB85C;" title="Nota Autorizada"></i>';
                break;
            case 'C':
                $linha->vnfnumero2 = $linha->vnfnumero;
                $linha->statusDescricao = '<i class="mdi-navigation-check" style="font-size: 26px;margin-top: -7px;color:#ccc;" title="Nota Cancelada"></i>';
                break;
            case 'D':
                $linha->vnfnumero2 = $linha->vnfnumero;
                $linha->statusDescricao = '<i class="mdi-navigation-check" style="font-size: 26px;margin-top: -7px;color:#ccc;" title="Nota Denegada"></i>';
                break;
            case 'I':
                $linha->vnfnumero2 = $linha->vnfnumero;
                $linha->statusDescricao = '<i class="mdi-navigation-check" style="font-size: 26px;margin-top: -7px;color:#ccc;" title="Nota Inutilizada"></i>';
                break;
            case 'R':
                $linha->vnfnumero2 = '';
                $linha->statusDescricao = '<i class="mdi-content-clear" style="font-size: 26px;color:RED;" title="Nota Rejeitada"></i>';
                break;
            case 'P':
                $linha->vnfnumero2 = '';
                $linha->statusDescricao = '<i class="mdi-navigation-check" style="font-size: 26px;margin-top: -7px;color:#EDAD1C;" title="Nota Pendente"></i>';
                break;
            default:
                $linha->vnfnumero2 = '';
                $linha->statusDescricao = '';
                break;
        }
        
        if ($linha->email === 't') {
            $linha->email = '<i class="glyphicon glyphicon-envelope email" style="font-size: 22px;color:#02C627;" title="Enviar E-mail"></i>';
        } else {
            $linha->email = '<i class="glyphicon glyphicon-envelope email" style="font-size: 22px;" title="Enviar E-mail"></i>';
        }
        
        $linha->relatorio = '<a target="_blank" class="edit ml10" href="'.getLinkReport($linha->codigo).'" title="Imprimir Relatório"><i class="mdi-maps-local-print-shop" style="font-size: 25px;"></i></a>';

        $formatValorTotal = CampoDinheiro::of(['nome' => 'valorTotal', 'valor' => $linha->valorTotal]);
        $linha->valorTotal = $formatValorTotal->getValorTela();
        $linhas[] = $linha;
    }

    limpaQuery($query);

    return $linhas;
}

function getLinkReport($venda) {
    $token = $_SESSION['nctoken'];
    $empresa = $_SESSION['codigo'];
    $usuario = $_SESSION['codigoUsuario'];
    
    return "http://report.nxfacil.com.br/report?name=venda&type=pdf&token=$token&idEmpresa=$empresa&idUsuario=$usuario&codigoVenda=$venda";
}

try {
    list($where, $params) = montaRestricaoBusca();

    $TOTAL_LINHAS = buscaTotalRegistros($where, $params);
    $LINHAS_CONSULTA = buscaRegistros($where, $params);
} catch (Exception $ex) {
    flash('erroConsulta', Alert::DANGER, "Erro ao Executar Consulta!", $ex->getMessage());
}
