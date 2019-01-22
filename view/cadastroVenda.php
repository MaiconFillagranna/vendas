<?php
require_once 'header.php';
?>

<!DOCTYPE html>
<html>
    <body>
        <div class="centro">
            <h3 class="textos">Cadastro de Venda</h3><br>
            <div class="form-group">
                <form action="../controller/ControllerProduto.php" method="post">
                    <label>Venda Atual:</label><br>
                    <label>Produto:</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required><br>
                    <label>Preço:</label>
                    <input type="number" class="form-control" id="preco" name="preco"><br>
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </form>
            </div>
        </div>
    </body>
</html>

