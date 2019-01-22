<?php

require_once 'class/Funcoes.php';
require_once 'class/Alert.php';
require_once 'class/Botao.php';
require_once 'class/CampoInteiro.php';
require_once 'modulos/especificos/locacaopallet/locacao/ControllerMaterialTerceiros.php';
require_once 'modulos/industrializacao/ControllerIndustrializacao.php';

// variaveis globais utilizadas para esta acao
$EMPRESA = $_SESSION['codigo'];

$BOTAO_EXCLUIR = new Botao('excluir');

/**
 * campos para o codigo
 * @var CampoInteiro[]
 */
$codigos = CampoInteiro::of(['nome' => 'id', 'obrigatorio' => true, 'multiplo' => true]);

function desatualizar($codigo) {
    global $EMPRESA;
    $dados = pg_query("SELECT finstatus as status FROM financeiro WHERE nccodigo = $EMPRESA AND fintipo = 'R' AND fcodvenda = $codigo");

    while ($verifica = pg_fetch_object($dados)) {
        $status = $verifica->status;

        if ($status === 'P') {
            throw new Exception('Não é possivel excluir uma Nota(s) com Parcela(s) paga(s)!');
        }
    }
    $sql = ("SELECT top.tomovimentaestoque, top.tooperacao, top.tooperacaoterceiros FROM venda v LEFT JOIN tipooperacao top ON v.vnftipooperacao = top.tocodigo WHERE v.nccodigo = " . $EMPRESA . " AND top.nccodigo = " . $EMPRESA . " AND v.vcodigo = " . $codigo);
    $resultado = executaSql($sql);

    while ($to = pg_fetch_object($resultado)) {
        $movestoque = $to->tomovimentaestoque;
        $tooperacao = $to->tooperacao;
        $operacaoTerceiros = $to->tooperacaoterceiros;
    }

    $sql = "SELECT vicodservproduto, viquantidade,vimovestoque FROM vendaitens WHERE vcodigo = $codigo AND nccodigo = $EMPRESA";
    $resultado = executaSql($sql);
    $quantidade = 0;
    while ($linha = pg_fetch_object($resultado)) {
        $quantidade = $linha->viquantidade;
        $estoque = buscaEstoque($linha->vicodservproduto);
        if ($tooperacao == 'S') {
            $novoEstoque = $estoque + $quantidade;
        } else {
            $novoEstoque = $estoque - $quantidade;
        }

        $codigoProduto = $linha->vicodservproduto;

        if ($linha->vimovestoque === 'S') {
            executaSql("UPDATE produtoservico SET prodservsaldoestoque = $1  WHERE nccodigo = $2 AND prodservcodigo = $3", [$novoEstoque, $EMPRESA, $codigoProduto]);
            deletaDados('movestoque', ["nccodigo = $1", "mecodvendacompra = $2", "mecodigoprod = $3"], [$EMPRESA, $codigo, $codigoProduto]);
        }
    }
    
    if($operacaoTerceiros == 'RL') {
        $MaterialTerceiros = new MaterialTerceirosVenda($codigo);
        $MaterialTerceiros->deletaFromVenda($codigo);
    }
    if($operacaoTerceiros == 'RI' || $operacaoTerceiros == 'DI') {
        $Industrializacao = new ControllerIndustrializacao();
        $Industrializacao->deletaRegistros('E'.$operacaoTerceiros, $codigo);
    }

    executaSql("UPDATE venda SET vatualizada = 'N' WHERE nccodigo = $EMPRESA AND vcodigo = $codigo");
    
    switch ($tooperacao) {
       case 'E'://Entrada  
             $finstatus = 'D';
             break;
       case 'S'://Saida
             $finstatus = 'R';
             break;
       case 'C':// Devolução para Fornecedor
             $finstatus = 'R';
             break;
       case 'V':// Devolução de Cliente
             $finstatus = 'D';
             break;
       case 'P':// Complementar de ICMS
             $finstatus = 'R';
             break;
       case 'A':// Ajuste Entrada
             $finstatus = 'D';
             break;
       case 'B':// Ajuste Saída
             $finstatus = 'R';
             break;
       case 'R':// Serviço
             $finstatus = 'R';
             break;
    }      
    
//    deletaDados('financeiro', ['nccodigo = $1', 'fcodvenda = $2'], [$EMPRESA, $codigo]);
    deletaDados('financeiro', ['nccodigo = $1', 'fcodvenda = $2', 'fintipo = $3'], [$EMPRESA, $codigo, $finstatus]);
//    if ($movestoque === 'S') {
//        deletaDados('movestoque', ["nccodigo = $1", "mecodvendacompra = $2", "mecodigoprod = $3"], [$EMPRESA, $codigo, $codigoProduto]);
//    }
    deletaDados("filhofinanceiro", ["nccodigo = $1", "ffcodvenda = $2"], [$EMPRESA, $codigo]);
}

$BOTAO_EXCLUIR->onClick(function() {
    global $codigos;
    try {
        if (count($codigos) == 0) {
            flash('statusMessage', Alert::WARNING, 'Problema!', 'Selecione pelo menos uma Compra.');
            return;
        }
        iniciaTranzacao();
        try {
            foreach ($codigos as $codigo) {
                if (!$codigo->isValido()) {
                    throw new Exception("Não foi possível identificar o código da Compra selecionado.");
                }

                desatualizar($codigo->getValor());
            }
            confirmaTranzacao();
            flash('statusMessage', Alert::SUCCESS, 'Sucesso!', 'Compra(s) Desatualizada(s).');
        } catch (Exception $ex) {
            flash('statusMessage', Alert::DANGER, 'Erro ao Desatualizar a(s) Compra(s)!', $ex->getMessage());

            reverterTranzacao();
        }
    } catch (Exception $ex) {
        flash('statusMessage', Alert::DANGER, 'Erro Fatal!', $ex->getMessage());
    }
});

header('Content-Type: application/json');

if ($statusMessage = flash("statusMessage")) {
    echo json_encode([
        'tipo' => $statusMessage->getTipo(),
        'assunto' => $statusMessage->getAssunto(),
        'mensagem' => $statusMessage->getMensagem()
    ]);
} else {
    http_response_code(STATUS_CODE_BAD_REQUEST);
}
    