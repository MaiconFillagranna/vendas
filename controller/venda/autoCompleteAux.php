<?php

$empresa = $_SESSION['codigo'];
$str = $_GET['str'];
$op = $_GET['op'];
$to = $_GET['to'];

$sqlto = "SELECT tooperacao FROM tipooperacao WHERE nccodigo = $empresa AND tocodigo = $to AND toativo != 'N'";
$resultadoto = pg_query($DB_CONN, $sqlto);
while ($reg = pg_fetch_assoc($resultadoto)) {
    $tooperacao = $reg["tooperacao"];
}

if ($tooperacao == 'E'){
    $cftipo = 'D'; 
}else{
    $cftipo = 'R';
} 


if ($op === 'P') {
    $listaFornecedor = [];
    $sql = "SELECT clinome, clicodigo FROM cliente WHERE nccodigo = $empresa AND cliativo = 'S' AND clinome ILIKE '%$str%' order by clinome ASC";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaFornecedor[] = [
            'label' => $reg["clinome"],
            'value' => $reg["clicodigo"]
        ];
    }
    pg_free_result($resultado);
    $sql2 = "SELECT count(clinome) as total FROM cliente cli WHERE cli.nccodigo = $empresa AND cli.cliativo = 'S' AND cli.clinome LIKE '%$str'";
    $resultado2 = pg_query($DB_CONN, $sql2);
    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaFornecedor[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }
    pg_free_result($resultado2);
    echo json_encode($listaFornecedor);
} else if ($op === 'PS') {
    $sql = "SELECT prodservnome, prodservcodigo, prodservvalorvenda FROM produtoservico WHERE nccodigo = $empresa AND prodservtipo = 'P' AND prodservnome ILIKE '%$str%' AND prodservativo != 'N'";
    $resultado = pg_query($DB_CONN, $sql);
    $listaprodutoservico = [];

    while ($reg = pg_fetch_assoc($resultado)) {

        $listaprodutoservico[] = [
            'label' => $reg["prodservnome"],
            'value' => $reg["prodservcodigo"],
            'vunitario' => $reg["prodservvalorvenda"]
        ];
    }
    $sql2 = "SELECT count(prodservnome) as total FROM produtoservico WHERE nccodigo = $empresa AND prodservtipo = 'P' AND prodservnome LIKE '%$str' AND prodservativo != 'N'";
    $resultado2 = pg_query($DB_CONN, $sql2);
    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaprodutoservico[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }
    pg_free_result($resultado2);
    pg_free_result($resultado);
    echo json_encode($listaprodutoservico);
} else if ($op === 'TOP') {
    $listatipooperacao = [];
    $sql = "SELECT tonome, tocodigo, toclassificacaofinanceira FROM tipooperacao WHERE nccodigo = $empresa AND tonome ILIKE '%$str%' AND (tooperacao = 'E' OR tooperacao = 'V') AND toativo != 'N' order by tonome ASC";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listatipooperacao[] = [
            'label' => $reg["tonome"],
            'value' => $reg["tocodigo"],
            'cf' => $reg['toclassificacaofinanceira']
        ];
    }
    pg_free_result($resultado);
    $sql2 = "SELECT count(tonome) as total FROM tipooperacao WHERE nccodigo = $empresa AND tonome LIKE '%$str' AND (tooperacao = 'E' OR tooperacao = 'V') AND toativo != 'N'";
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
} else if ($op === 'CF') {
    $listaclassificacaofinanceira = [];
    $sql = "SELECT cfnome, cfcodigo FROM classificacaofinanceira where nccodigo = $empresa AND cftipo = '".$cftipo."' AND cfativo = 'S' AND cfnome ILIKE '%$str%'";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaclassificacaofinanceira[] = [
            'label' => $reg["cfnome"],
            'value' => $reg["cfcodigo"]
        ];
    }

    pg_free_result($resultado);
    $sql2 = "SELECT count(cfnome) as total FROM classificacaofinanceira cf WHERE cf.nccodigo = $empresa AND cf.cftipo = '".$cftipo."' AND cf.cfativo = 'S' AND cf.cfnome LIKE '%$str'";
    $resultado2 = pg_query($DB_CONN, $sql2);

    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaclassificacaofinanceira[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }

    pg_free_result($resultado2);
    echo json_encode($listaclassificacaofinanceira);
} else if ($op === 'CFE') {
    
//    $sqlto = "SELECT tooperacao FROM tipooperacao WHERE nccodigo = $empresa AND tocodigo = $to AND toativo != 'N'";
//    $resultadoto = pg_query($DB_CONN, $sqlto);
//    while ($reg = pg_fetch_assoc($resultadoto)) {
//        $tooperacao = $reg["tooperacao"];
//    }
//    
//    if ($tooperacao == 'E'){
//        $cftipo = 'D'; 
//    }else{
//        $cftipo = 'R';
//    }    
    
    $listaclassificacaofinanceira = [];
    $sql = "SELECT cfnome, cfcodigo FROM classificacaofinanceira where nccodigo = $empresa AND cftipo = '".$cftipo."' AND cfativo = 'S' AND cfnome ILIKE '%$str%'";
    $resultado = pg_query($DB_CONN, $sql);

    while ($reg = pg_fetch_assoc($resultado)) {
        $listaclassificacaofinanceira[] = [
            'label' => $reg["cfnome"],
            'value' => $reg["cfcodigo"]
        ];
    }

    pg_free_result($resultado);
    $sql2 = "SELECT count(cfnome) as total FROM classificacaofinanceira cf WHERE cf.nccodigo = $empresa AND cf.cftipo = '".$cftipo."' AND cf.cfativo = 'S' AND cf.cfnome LIKE '%$str'";
    $resultado2 = pg_query($DB_CONN, $sql2);

    while ($reg = pg_fetch_assoc($resultado2)) {
        $listaclassificacaofinanceira[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }

    pg_free_result($resultado2);
    echo json_encode($listaclassificacaofinanceira);
} else if ($op === 'CC') {
    $sql = "SELECT ccnome, cccodigo FROM centrocusto WHERE nccodigo = $empresa AND ccnome ILIKE '%$str%' AND ccativo != 'N'";
    $resultado = pg_query($DB_CONN, $sql);
    $listacentrocusto = [];

    while ($reg = pg_fetch_assoc($resultado)) {
        $listacentrocusto[] = [
            'label' => $reg["ccnome"],
            'value' => $reg["cccodigo"]
        ];
    }
    pg_free_result($resultado);
    $sql2 = "SELECT count(ccnome) as total FROM centrocusto cc WHERE cc.nccodigo = $empresa AND cc.ccnome LIKE '%$str' AND ccativo != 'N'";
    $resultado2 = pg_query($DB_CONN, $sql2);
    while ($reg = pg_fetch_assoc($resultado2)) {
        $listacentrocusto[] = [
            'label' => '',
            'value' => '',
            'total' => $reg["total"]
        ];
    }
    pg_free_result($resultado2);
    echo json_encode($listacentrocusto);
} else if ($op === 'getCF') {
    $sql = executaSql("SELECT * FROM classificacaofinanceira WHERE nccodigo = $1 AND cfcodigo = $2 AND cfativo != 'N'", [$empresa, $str]);
    echo json_encode(getLinhaQuery($sql)->cfnome);
}