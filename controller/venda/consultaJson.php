<?php

require_once 'modulos/venda/consultaControle.php';

header('Content-Type: application/json');

if ($errorConsulta = flash("errorConsulta")) {
    http_response_code(STATUS_CODE_INTERNAL_SERVER_ERROR);

    echo json_encode([
        'tipo' => $errorConsulta->getTipo(),
        'assunto' => $errorConsulta->getAssunto(),
        'mensagem' => $errorConsulta->getMensagem()
    ]);
} else {
    echo json_encode([
        'total' => $TOTAL_LINHAS,
        'rows' => $LINHAS_CONSULTA
    ]);
}
