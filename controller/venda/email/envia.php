<?php

require_once 'class/CampoTexto.php';
require_once 'class/CampoInteiro.php';
require_once 'lib/Email.php';

$para = CampoTexto::of("para");
$comcopia = CampoTexto::of("cc");
$msg = CampoTexto::of("msg");
$assunto = CampoTexto::of("assunto");
$id = CampoInteiro::of("codigo");

$to = array_filter(explode(",", $para->getValor()));
$cc = array_filter(explode(",", $comcopia->getValor()));

$sql = executaSql("SELECT * FROM usuarionossocliente WHERE nccodigo = $1 AND unccodigo = $2", [$_SESSION["codigo"], $_SESSION["codigoUsuario"]]);
$dadosCliente = getLinhaQuery($sql);

$ch = curl_init('http://report.nxfacil.com.br/report?name=venda&type=pdf'.'&token='.$_SESSION['nctoken'].'&idEmpresa=' . $_SESSION["codigo"] . '&idUsuario=' . $_SESSION["codigoUsuario"] . '&codigoVenda=' . $id->getValor());
curl_setopt($ch, CURLOPT_USERPWD, $dadosCliente->uncemail . ":" . $dadosCliente->uncsenha);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$pdf = curl_exec($ch);
curl_close($ch);

$nomeaquivo = "venda_{$id->getValor()}.pdf";
$arquivo = fopen("temp/pdf/$nomeaquivo", "a+");
fwrite($arquivo, $pdf);
fclose($arquivo);

Email::setTo($to);
Email::setCC($cc);
Email::setSubject($assunto->getValor());
Email::setMessage($msg->getValor());
Email::setAttachment("temp/pdf/$nomeaquivo", $nomeaquivo);
try {
    Email::sendMail();
    echo json_encode([status => "success"]);
    unlink("temp/pdf/$nomeaquivo");
    executaSqlTransacao("UPDATE venda SET vemail = $1 WhERE nccodigo = $2 AND vcodigo = $3", [TRUE, $_SESSION["codigo"], $id->getValor()]);
} catch (Exception $ex) {
    echo json_encode([status => "error", message => $ex->getMessage()]);
}
