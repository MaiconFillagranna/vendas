<?php

require_once 'Query.php';

header('Content-type: application/json');

$Controller = new ControllerProduto();

switch(filter_input(INPUT_SERVER, "REQUEST_METHOD")) {
    case "POST":
        $Controller->save();
        break;
    case "GET":
        $Controller->search();
}

class ControllerProduto {
    
    public function save() {
        $descricao = filter_input(INPUT_POST, "descricao");
        $preco = filter_input(INPUT_POST, "preco");
        $SQL = "INSERT INTO produto VALUES (null, $descricao, $preco)";
        $Query = Query::getInstance();
        if($Query->save($SQL)) {
            echo(json_encode("success"));
        }
        else {
            echo(json_encode("error"));
        }
    }
    
    public function search() {
        $search = filter_input(INPUT_POST, "search");
        $SQL = "SELECT * FROM produto WHERE descricao ILIKE '$search'";
        $Query = Query::getInstance();
        $produto = $Query->getFirstRow($SQL);
        if(!empty($produto)) {
            echo(json_encode($produto));
        }
        else {
            echo(json_encode('nenhum'));
        }
    }
    
}
