<?php

require_once 'class/Formulario.php';
require_once 'class/CampoTexto.php';
require_once 'class/CampoInteiro.php';

$Cadastra = new cadastraAutoComplete($DB_CONN);

$Cadastra->formulario = new Formulario();

$Cadastra->dados = CampoTexto::of("dados");
$Cadastra->op = CampoTexto::of("op");

$Cadastra->formulario->addCampos($Cadastra->dados, $Cadastra->op);

iniciaTranzacao();
try {
    $Cadastra->cadastros();

    echo $Cadastra->codigo;

    confirmaTranzacao();
} catch (Exception $ex) {
    echo json_encode([
        "status" => "error",
        "erros" => $Cadastra->erros
    ]);

    reverterTranzacao();
}

class cadastraAutoComplete {

    public $dados;
    public $codigo; //pega um codigo e que será reutiliazada para outros códigos
    //Codigos
    public $clicodigo;

    public function cadastros() {
        //Pega a Operação
        $op = $this->op->getValor();
        if ($op === 'USU') {
            $this->cadastraUsu();
        } else if ($op === 'CB') {
            $this->cadastraCB();
        } else if ($op === 'PRD') {
            $this->cadastraServ();
        }
    }

    public function buscaProximoCodigoCliente() {
        $empresa = $_SESSION['codigo'];

        $sql = "SELECT COALESCE(MAX(clicodigo), 0) + 1 AS clicodigo FROM cliente WHERE nccodigo = $empresa";
        $resultado = executaSqlTransacao($sql);
        $this->clicodigo = getLinhaQuery($resultado)->clicodigo;
        limpaQuery($resultado);
        $this->codigo = $this->clicodigo;
    }

    public function cadastraUsu() {
        $empresa = $_SESSION['codigo'];
        $this->buscaProximoCodigoCliente();
        $sql = "INSERT INTO cliente (nccodigo, clicodigo, clinome, cliativo, clicliente, cliisentoinsc, clirginsc)
             values ($1,$2,$3,$4, $5,$6,$7)";
        executaSqlTransacao($sql, [
            $empresa,
            $this->clicodigo,
            $this->dados->getValor(),
            'S',
            'S',
            'S',
            'ISENTO'
        ]);
    }

    public function buscaProximoCodigoCB() {
        $empresa = $_SESSION['codigo'];

        $sql = "SELECT COALESCE(MAX(cbcodigo), 0) + 1 AS cbcodigo FROM contabancaria WHERE nccodigo = $empresa";
        $resultado = executaSqlTransacao($sql);
        $this->cbcodigo = getLinhaQuery($resultado)->cbcodigo;
        limpaQuery($resultado);
        $this->codigo = $this->cbcodigo;
    }

    public function cadastraCB() {
        $empresa = $_SESSION['codigo'];
        $this->buscaProximoCodigoCB();
        $sql = "INSERT INTO contabancaria (nccodigo, cbcodigo, cbnomeoriginal,cbativo)
             values ($1,$2,$3,$4)";
        executaSqlTransacao($sql, [
            $empresa,
            $this->cbcodigo,
            $this->dados->getValor(),
            'S'
        ]);
    }

    public function buscaProximoCodigoServico() {
        $empresa = $_SESSION['codigo'];

        $sql = "SELECT COALESCE(MAX(prodservcodigo), 0) + 1 AS prodservcodigo FROM produtoservico WHERE nccodigo = $empresa";
        $resultado = executaSqlTransacao($sql);
        $this->prodservcodigo = getLinhaQuery($resultado)->prodservcodigo;
        limpaQuery($resultado);
        $this->codigo = $this->prodservcodigo;
    }

    public function cadastraServ() {
        $empresa = $_SESSION['codigo'];
        $this->buscaProximoCodigoServico();
        $sql = "INSERT INTO produtoservico (nccodigo, prodservcodigo, prodservnome, prodservtipo, prodservativo, prodservatualizaestoque)
             values ($1,$2,$3,$4,$5,$6)";
        executaSqlTransacao($sql, [
            $empresa,
            $this->codigo,
            $this->dados->getValor(),
            'P',
            'S',
            'S'
        ]);
    }

}
