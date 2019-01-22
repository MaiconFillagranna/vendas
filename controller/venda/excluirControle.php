<?php

require_once 'class/Funcoes.php';
require_once 'class/Alert.php';
require_once 'class/Botao.php';
require_once 'class/CampoInteiro.php';

// variaveis globais utilizadas para esta acao
$EMPRESA = $_SESSION['codigo'];

$BOTAO_EXCLUIR = new Botao('excluir');

/**
 * campos para o codigo
 * @var CampoInteiro[]
 */
$codigos = CampoInteiro::of(['nome' => 'id', 'obrigatorio' => true, 'multiplo' => true]);

/**
 * Exclui o registro pelo codigo
 */
function excluir($codigo) {
    global $EMPRESA;
    $dados = pg_query("SELECT finstatus as status, finnumboleto FROM financeiro WHERE nccodigo = " . $EMPRESA . " AND  fintipo = 'R' AND fcodvenda = " . $codigo);
    while ($verifica = pg_fetch_object($dados)) {
        $status = $verifica->status;
        if ($status === 'P') {
            throw new Exception('Não é possivel excluir uma conta paga!');
        }
        if ($verifica->finnumboleto > 0){
             throw new Exception('Não é possivel excluir uma conta com boleto emitido!');
        }
    }
    $sql = executaSql("SELECT top.tomovimentaestoque FROM venda v LEFT JOIN tipooperacao top ON v.vnftipooperacao = top.tocodigo WHERE v.nccodigo = $1 AND top.nccodigo = $1 AND v.vcodigo = $2", [$EMPRESA, $codigo]);
    $geraEstoque = getLinhaQuery($sql)->tomovimentaestoque;

    deletaDados("vendaitens", ["nccodigo = $1", "vcodigo = $2"], [$EMPRESA, $codigo]);
    deletaDados("vendafinanceiro", ["nccodigo = $1", "vcodigo = $2"], [$EMPRESA, $codigo]);
    deletaDados("venda", ["nccodigo = $1", "vcodigo = $2"], [$EMPRESA, $codigo]);
    deletaDados("filhofinanceiro", [" nccodigo = $1", "ffcodvenda = $2"], [$EMPRESA, $codigo]);
    deletaDados("financeiro", ["nccodigo = $1", "fcodvenda = $2", "fintipo = $3"], [$EMPRESA, $codigo, 'R']);
}

$BOTAO_EXCLUIR->onClick(function() {
    global $codigos;

    try {
        // verifica se foram enviados os codigos
        if (count($codigos) == 0) {
            flash('statusMessage', Alert::WARNING, 'Problema!', 'Selecione pelo menos uma Venda.');
            return;
        }

        // inicia a transacao com o banco de dados
        iniciaTranzacao();

        try {
            // exclui as unidades de medida
            foreach ($codigos as $codigo) {
                if (!$codigo->isValido()) {
                    throw new Exception("Não foi possível identificar o código de uma Venda selecionada.");
                }

                excluir($codigo->getValor());
            }

            // confirma a transacao no banco de dados
            confirmaTranzacao();

            // mensagem do usuario
            flash('statusMessage', Alert::SUCCESS, 'Sucesso!', 'Venda(s) excluida(s) com sucesso!.');
        } catch (Exception $ex) {
            // mensagem do usuario
            flash('statusMessage', Alert::DANGER, 'Erro ao Excluir a(s) Vendas(s)!', $ex->getMessage());

            // reverte a transacao no banco de dados
            reverterTranzacao();
        }
    } catch (Exception $ex) {
        // mensagem do usuario
        flash('statusMessage', Alert::DANGER, 'Erro Fatal!', $ex->getMessage());
    }
});
