<?php

require_once 'class/Formulario.php';
require_once 'class/CampoTexto.php';
require_once 'class/CampoInteiro.php';

$Altera = new AlteraNFE($DB_CONN);

$Altera->formulario = new Formulario();

//Formulário basico.
$Altera->vcodigo = CampoInteiro::of('vcodigo');
$Altera->vnftipooperacao = CampoInteiro::of('tipooperacaoid');
$Altera->vnfnumero = $_POST["vnfnumero"];
$Altera->tipooperacao = CampoTexto::of('tipooperacao');
$Altera->status = CampoTexto::of('status');

$Altera->formulario->addCampos($Altera->vcodigo, $Altera->vnfnumero, $Altera->status);

iniciaTranzacao();
try {
    $Altera->AlterarNF();
    echo json_encode([
        "status" => "success"
    ]);
    confirmaTranzacao();
} catch (Exception $exc) {
    echo json_encode([
        "status" => "error",
        "erros" => $Altera->erros
    ]);
    reverterTranzacao();
}

class AlteraNFE {

    public $vcodigo;
    public $vnftipooperacao;
    public $tipooperacao;
    public $vnfnumero;
    public $status;
    public $dbConn;

    public function __construct($dbConn) {
        $this->dbConn = $dbConn;
    }

    public function AlterarNF() {
        $empresa = $_SESSION['codigo'];
        if ($this->vnfnumero <> null) {
            if ($this->vnfnumero == 0) {
                $this->vnfnumero = null;
            }
            $sql = "UPDATE venda SET vnfnumero = $1 WHERE nccodigo = $2 and vcodigo = $3";
            executaSqlTransacao($sql, [
                $this->vnfnumero,
                $empresa,
                $this->vcodigo->getValor()
            ]);
        }
        if ($this->tipooperacao->getValor() <> null) {
            $sql = "UPDATE venda SET vnftipooperacao = $1 WHERE nccodigo = $2 and vcodigo = $3";
            executaSqlTransacao($sql, [
                $this->vnftipooperacao->getValor(),
                $empresa,
                $this->vcodigo->getValor()
            ]);
        }
        if ($this->status->getValor() != "N") {
            $sql = "UPDATE venda SET vnfstatus = $1 WHERE nccodigo = $2 and vcodigo = $3";
            executaSqlTransacao($sql, [
                $this->status->getValor(),
                $empresa,
                $this->vcodigo->getValor()
            ]);
        }
    }

}
