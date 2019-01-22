<?php

function montaWhere() {
    $periodo = $_POST['de'];
    $periodo2 = $_POST['ate'];
    $exibir = $_POST['exibir'];
    $busca = $_POST['busca'];
    $usuario = $_POST['usuario'];
    $empresa = $_SESSION["codigo"];
    $tipoUsu = $_SESSION['tipoUsuario'];

    $where = ["v.nccodigo = $empresa", "cli.nccodigo = $empresa", "v.vprodserv = 'P'", "v.vtipo = 'V'", 'v.vnfcenumero is null'];

    if ($exibir !== 'T' ) {
         if ($exibir == 'F' ) {
             $where[] = "v.vnfnumero > 0 and v.vnfstatus = 'A'";
         }else{
             $where[] = "((v.vnfnumero is null) or (v.vnfnumero > 0 and v.vnfstatus <> 'A')) ";
         }        
    }        
    
    if ($busca != '') {
        $where[] = "(LOWER(cli.clinome) ILIKE '%" . strtolower($busca) . "%')";
    }
   if ($tipoUsu === 'V') {
        $where[] = "v.vvendedorcod = " . $usuario;
    }
    if ($periodo != "" && $periodo2 != "") {
        $where[] = "(v.vdatapedido BETWEEN '" . $periodo . "' AND '" . $periodo2 . "' ) ";
    }   

    return " WHERE " . implode(" AND ", $where);
}

$where = montaWhere();
$sql = executaSql("SELECT SUM(v.vvalortotal) AS total FROM venda v LEFT JOIN cliente cli on v.vcodcli = cli.clicodigo $where");
//$total = getLinhaQuery($sql)->total;
echo json_encode(getLinhaQuery($sql)->total);
?>