<?php

require_once 'class/CampoInteiro.php';

$venda = CampoInteiro::of("venda");

$email = [];

$sql = executaSql("SELECT cli.clinome as nome, clcemail as email FROM clientecontato clic LEFT JOIN venda ven ON ven.vcodcli = clic.clicodigo LEFT JOIN cliente cli ON cli.clicodigo = clic.clicodigo WHERE cli.nccodigo = $1 AND clic.nccodigo = $1 AND ven.nccodigo = $1 AND ven.vcodigo = $2 AND (clic.clcrecebeemail = 2 OR clic.clcrecebeemail = 4)", [$_SESSION["codigo"], $venda->getValor()]);
while ($dados = getLinhaQuery($sql)) {
    $email[] = $dados;
}
if (empty($email)) {
    $sql = executaSql("SELECT clinome as nome, cliemail as email FROM cliente cli LEFT JOIN venda ven ON ven.vcodcli = cli.clicodigo WHERE cli.nccodigo = $1 AND ven.nccodigo = $1 AND ven.vcodigo = $2", [$_SESSION["codigo"], $venda->getValor()]);
    $email[] = getLinhaQuery($sql);
}

echo json_encode($email);
