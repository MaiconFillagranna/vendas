<?php

$empresa = $_SESSION['codigo'];
$str = $_GET['str'];
$op = $_GET['op'];


if ($op === 'P') {
    $listaFornecedor = [];
    
    $sql = "SELECT nomeformatado as clinome,
                   clicodigo,
                   clitipooperacao,
                   cliexterior
              FROM (SELECT clinome||COALESCE(' - CPF/CNPJ: '||regexp_replace(clicpfcnpj, '[^0-9]', '', 'g'), '')||COALESCE(' - '||cidade.nome, '')||COALESCE(' - '||estado.sigla, '') as nomeformatado,
                           *
                      FROM cliente
                 LEFT JOIN cidade
                        ON cidade.codigo_cidade = cliente.clicodcidade
                 LEFT JOIN estado
                        ON estado.codigo_estado = cidade.codigo_estado) as dadoscliente
             WHERE nccodigo = $1
               AND cliativo = $2
               AND nomeformatado ILIKE '%'||$3||'%'                         
          ORDER BY clinome ASC";
    $resultado = executaSql($sql, [$empresa, 'S', $str]);

    while ($reg = pg_fetch_assoc($resultado)) {
        $tonome = null;

        if (!empty($reg['clitipooperacao'])) {
            $sqlTO = "SELECT * FROM tipooperacao WHERE nccodigo = " . $empresa . " AND tocodigo = " . $reg['clitipooperacao'] . "  AND toativo != 'N'";
            $resultadoTO = pg_query($DB_CONN, $sqlTO);

            while ($regTO = pg_fetch_assoc($resultadoTO)) {
                $tonome = $regTO["tonome"];
            }
        }
        
        $clienteExterior = false;
        if($reg["cliexterior"] == 'S') {
            $clienteExterior = true;
        }

        $listaFornecedor[] = [
            'label' => $reg["clinome"],
            'value' => $reg["clicodigo"],
            'toID' => $reg["clitipooperacao"],
            'toNome' => $tonome,
            'clienteExterior' => $clienteExterior
        ];
    }
    pg_free_result($resultado);
    $listaFornecedor[] = [
        'label' => '',
        'value' => '',
        'total' => count($listaFornecedor)
    ];
    echo json_encode($listaFornecedor);
} else if ($op === 'T') {
    $listaUsuario = [];
    if ($str) {
        $sql = "SELECT uncnome, unccodigo FROM usuarionossocliente WHERE nccodigo = $empresa AND uncativo = 'S' AND uncnome ILIKE '%$str%'";
    } else {
        $sql = "SELECT uncnome, unccodigo FROM usuarionossocliente WHERE nccodigo = $empresa AND uncativo = 'S'";
    }

    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaUsuario[] = [
            'label' => $reg["uncnome"],
            'value' => $reg["unccodigo"]
        ];
    }

    pg_free_result($resultado);

    echo json_encode($listaUsuario);
} else if ($op === 'CB') {
    $sql = "SELECT cbnomeoriginal, cbcodigo FROM contabancaria WHERE nccodigo = $empresa AND cbnomeoriginal ILIKE '%$str%' AND cbativo != 'N'";
    $resultado = pg_query($DB_CONN, $sql);
    $listacontabancaria = [];

    while ($reg = pg_fetch_assoc($resultado)) {
        $listacontabancaria[] = [
            'label' => $reg["cbnomeoriginal"],
            'value' => $reg["cbcodigo"]
        ];
    }
    pg_free_result($resultado);

    $sql2 = "SELECT count(cbnomeoriginal) as total FROM contabancaria cb WHERE cb.nccodigo = $empresa AND cb.cbnomeoriginal LIKE '%$str' AND cbativo != 'N'";
    $resultado2 = pg_query($DB_CONN, $sql2);
    while ($reg = pg_fetch_assoc($resultado2)) {
        $listacontabancaria[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }
    pg_free_result($resultado2);
    echo json_encode($listacontabancaria);
} else if ($op === 'SN') {
    $sql = "SELECT snnome, sncodigo FROM serienota where nccodigo = $empresa AND snnome ILIKE '%$str%'";
    $resultado = pg_query($DB_CONN, $sql);
    $listaSerie = [];

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaSerie[] = [
            'label' => $reg["snnome"],
            'value' => $reg["sncodigo"]
        ];
    }
    pg_free_result($resultado);
    echo json_encode($listaSerie);
} else if ($op === 'SE') {
    $sql = "SELECT govservdescricao, govservcodigo, govservcod FROM govservicos WHERE govservdescricao ILIKE '%$str%' OR govservcodigo ILIKE '%$str%'";
    $resultado = pg_query($DB_CONN, $sql);
    $listaServico = [];

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaServico[] = [
            'label' => $reg["govservcodigo"] . " - " . $reg["govservdescricao"],
            'value' => $reg["govservcod"]
        ];
    }
    pg_free_result($resultado);
    echo json_encode($listaServico);
} else if ($op === 'PS') {
    $sql = "SELECT nomeformatado as prodservnome, 
                   prodservcodigo, 
                   prodservvalorvenda, 
                   prodservvalorcusto,
                   psaliqipi, 
                   prodservcodcli AS codcli 
              FROM (SELECT prodservnome||COALESCE(' - '||prodservcodcli, '') as nomeformatado,
                           prodservcodigo,
                           prodservvalorvenda,
                           prodservvalorcusto,
                           psaliqipi,
                           prodservcodcli,
                           nccodigo,
                           prodservtipo,
                           prodservativo
                      FROM produtoservico) as dadosproduto
             WHERE nccodigo      = $1
               AND prodservtipo  = $2 
               AND prodservativo = $3
               AND nomeformatado ILIKE '%'||$4||'%'
          ORDER BY nomeformatado";
    $resultado = executaSql($sql, [$empresa, 'P', 'S', $str]);
    $listaprodutoservico = [];
    
    $sugereCusto = getSugerePrecoCusto();

    while ($reg = pg_fetch_assoc($resultado)) {

        $listaprodutoservico[] = [
            'label' => $reg["prodservnome"],
            'value' => $reg["prodservcodigo"],
            'vunitario' => $sugereCusto ? $reg["prodservvalorcusto"] : $reg["prodservvalorvenda"],
            'vcusto' => $reg["prodservvalorcusto"],
            'porcentagemipi' => $reg["psaliqipi"]
        ];
    }
    $listaprodutoservico[] = [
            'label' => '',
            'value' => '',
            'total' => count($listaprodutoservico)
    ];
    pg_free_result($resultado);
    echo json_encode($listaprodutoservico);
} else if ($op === 'TOP') {
    $listatipooperacao = [];
    $sql = "SELECT * FROM tipooperacao WHERE nccodigo = $empresa AND tonome ILIKE '%$str%' AND (tooperacao <> 'R') AND toativo != 'N' order by tonome ASC";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $cfnome = '';
        if (!empty($reg["toclassificacaofinanceira"])) {
            $sqlb = ("SELECT cfnome FROM classificacaofinanceira WHERE nccodigo = " . $empresa . " and cfcodigo = " . $reg["toclassificacaofinanceira"]);
            $resultadob = pg_query($DB_CONN, $sqlb);

            while ($regb = pg_fetch_assoc($resultadob)) {
                $cfnome = $regb["cfnome"];
            }
        }
        if (isset($reg["toespcodigo"])) {
            $espcod = $reg["toespcodigo"];
            $sqlGetEspecie = executaSql("SELECT espnome FROM especie WHERE nccodigo = $empresa AND espcodigo = $espcod");
            $espnome = getLinhaQuery($sqlGetEspecie)->espnome;


            $listatipooperacao[] = [
                'label' => $reg["tonome"],
                'value' => $reg["tocodigo"],
                'gerafin' => $reg["togerafinanceiro"],
                'classifid' => $reg["toclassificacaofinanceira"],
                'classifin' => $cfnome,
                'espcod' => $espcod,
                'espnome' => $espnome
            ];
        } else {

            $listatipooperacao[] = [
                'label' => $reg["tonome"],
                'value' => $reg["tocodigo"],
                'gerafin' => $reg["togerafinanceiro"],
                'classifid' => $reg["toclassificacaofinanceira"],
                'classifin' => $cfnome
            ];
        }
    }
    pg_free_result($resultado);
    $sql2 = "SELECT count(tonome) as total FROM tipooperacao WHERE nccodigo = $empresa AND tonome ILIKE '%$str' AND (tooperacao <> 'R') AND toativo != 'N'";
    $resultado2 = pg_query($DB_CONN, $sql2);
    while ($reg = pg_fetch_assoc($resultado2)) {
        $listatipooperacao[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }
    pg_free_result($resultado2);
    echo json_encode($listatipooperacao);
} else if ($op === 'ESP') {
    $listaEspecie = [];
    $sql = "SELECT espnome, espcodigo,espsigla FROM especie WHERE nccodigo = $empresa AND espnome ILIKE '%$str%'";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $espnome = $reg["espsigla"] . " - " . $reg["espnome"];
        $listaEspecie[] = [
            'label' => $espnome,
            'value' => $reg["espcodigo"]
        ];
    }

    pg_free_result($resultado);
    $sql2 = "SELECT count(espnome) as total FROM especie esp WHERE esp.nccodigo = $empresa AND esp.espnome  LIKE '%$str'";
    $resultado2 = pg_query($DB_CONN, $sql2);

    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaEspecie[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }

    pg_free_result($resultado2);
    echo json_encode($listaEspecie);
}  else if ($op === 'CC') {
    $listaEspecie = [];
    $sql = "SELECT ccnome, cccodigo FROM centrocusto WHERE nccodigo = $empresa AND ccnome ILIKE '%$str%' AND ccativo = 'S'";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaEspecie[] = [
            'label' => $reg["ccnome"],
            'value' => $reg["cccodigo"]
        ];
    }

    pg_free_result($resultado);
    $sql2 = "SELECT count(ccnome) as total FROM centrocusto cc WHERE cc.nccodigo = $empresa AND cc.ccnome  LIKE '%$str%'";
    $resultado2 = pg_query($DB_CONN, $sql2);

    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaEspecie[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }

    pg_free_result($resultado2);
    echo json_encode($listaEspecie);
}

function getSugerePrecoCusto() {
    $tipoOperacao = filter_input(INPUT_GET, "to");
    if(!isset($tipoOperacao) || $tipoOperacao == '') {
        return false;
    }
    $oQuery = Query::getInstance();
    $SQL = "SELECT tosugerecusto
              FROM tipooperacao
             WHERE nccodigo = $1
               AND tocodigo = $2";
    $oQuery->prepare($SQL, [NossoCliente::getInstance()->getCodigo(), $tipoOperacao]);
    $sugereCusto = $oQuery->getFirstRow()->tosugerecusto;
    return $sugereCusto == 'S' ? true : false;
}

