<?php

require_once 'class/Alert.php';
require_once 'modulos/venda/excluirControle.php';

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