<?php
require_once 'header.php';
?>

<html>
    <body>
        <div class="row col-md-12">
            <h3 class="textos">Cadastro de Venda</h3>
            <div class="col-md-2">
            <label>Venda Atual:</label>
            <input type="text" class="form-control" id="vendaAtual" name="vendaAtual" disabled><br>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="table col-md-6">
                    <thead>
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Preço</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>
            <div class="form-group col-md-2">
                <label>Produto:</label>
                <input type="text" class="form-control" id="idProduto" name="idProduto"><br>
                <button class="btn btn-success" id="adicionarProduto">Adicionar</button>
            </div>
        </div>
        <div class="row col-md-6">
            <button class="btn btn-success" id="salvarVenda" style="float: right">Confirmar</button>
            <button class="btn btn-danger" id="cancelarVenda">Cancelar</button>
        </div>
        <script type="text/javascript" src="js/scriptCadastroVenda.js"></script>
    </body>
</html>

