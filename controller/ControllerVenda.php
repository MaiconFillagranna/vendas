<?php

require_once 'Query.php';

header('Content-type: application/json');

$Controller = new ControllerVenda();

$method = filter_input(INPUT_SERVER, "REQUEST_METHOD") == "GET" ? 1 : 0;
$requisicao = filter_input($method, "requisicao");
$venda = filter_input($method, "venda");
switch($requisicao) {
    case "vendaAtual":
        $Controller->getVendaAtual();
        break;
    case "insert":
        $Controller->save($venda);
        break;
    case "delete":
        $Controller->delete($venda);
        break;
    case "consulta":
        $Controller->getAllVendas();
        break;
}

class ControllerVenda {
    
    public function getVendaAtual() {
        $SQL = "SELECT COALESCE(MAX(numero)+1,1) as numero FROM documento";
        $Query = Query::getInstance();
        $numero = $Query->getFirstRow($SQL)->numero;
        echo json_encode($numero);
    }
    
    public function save($venda) {
        $SQL = "UPDATE documento SET confirmado = 1 WHERE numero = $venda";
        echo json_encode(Query::getInstance()->execute($SQL));
    }
    
    public function getAllVendas() {
        $SQL = "SELECT * FROM documento WHERE confirmado = 1";
        echo json_encode(Query::getInstance()->getAllRows($SQL));
    }
    
    public function delete($venda) {
        $SQL = "DELETE FROM documento WHERE numero = $venda";
        echo json_encode(Query::getInstance()->execute($SQL));
    }
    
    public function adicionaProduto($venda, $Produto) {
        $this->insertVendaIfNotExists($venda, $Produto);
        $this->insertItem($venda, $Produto);
    }
    
    private function insertVendaIfNotExists($venda, $Produto) {
        $SQL = "SELECT EXISTS (SELECT numero FROM documento WHERE numero = $venda) as existe";
        $Query = Query::getInstance();
        if($Query->getFirstRow($SQL)->existe == 0) {
            $SQL2 = "INSERT INTO documento VALUES ($venda, $Produto->preco, 0)";
            $Query->save($SQL2);
        }
    }
    
    private function insertItem($venda, $Produto) {
        $SQL = "INSERT INTO item VALUES ($venda, $Produto->id)";
        Query::getInstance()->save($SQL);
        $this->updateTotalVenda($venda, $Produto);
    }
    
    private function updateTotalVenda($venda, $Produto) {
        $SQL = "UPDATE documento SET total = total+$Produto->preco WHERE numero = $venda";
        Query::getInstance()->execute($SQL);
    }
    
}
