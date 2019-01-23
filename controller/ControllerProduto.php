<?php

require_once 'Query.php';

header('Content-type: application/json');

$Controller = new ControllerProduto();

switch(filter_input(INPUT_SERVER, "REQUEST_METHOD")) {
    case "POST":
        $Controller->save();
        break;
    case "GET":
        if(filter_input(INPUT_GET, "requisicao") == 'consulta') {
            $Controller->getAllProdutos();
        }
        else{
            $Controller->search();
        }
        break;
}

class ControllerProduto {
    
    public function save() {
        $codigo = filter_input(INPUT_POST, "codigo");
        $descricao = filter_input(INPUT_POST, "descricao");
        $preco = filter_input(INPUT_POST, "preco");
        $SQL = "INSERT INTO produto VALUES (null, '$codigo', '$descricao', $preco)";
        $Query = Query::getInstance();
        if($Query->save($SQL)) {
            echo(json_encode("success"));
        }
        else {
            echo(json_encode("error"));
        }
    }
    
    public function getAllProdutos() {
        $SQL = "SELECT * FROM produto";
        echo json_encode(Query::getInstance()->getAllRows($SQL));
    }
    
    public function search() {
        $codigo = filter_input(INPUT_GET, "codigo");
        $venda = filter_input(INPUT_GET, "venda");
        $SQL = "SELECT * 
                  FROM produto 
                 WHERE codigo LIKE '$codigo' 
                   AND id NOT IN (SELECT id 
                                    FROM item 
                                   WHERE documento_id = $venda 
                                     AND produto_id = produto.id)";
        $Query = Query::getInstance();
        $Produto = $Query->getFirstRow($SQL);
        if(!empty($Produto)) {
            require_once 'ControllerVenda.php';
            $controllerVenda = new ControllerVenda();
            $controllerVenda->adicionaProduto($venda, $Produto);
            echo(json_encode($Produto));
        }
        else {
            echo(json_encode('nenhum'));
        }
    }
    
}
