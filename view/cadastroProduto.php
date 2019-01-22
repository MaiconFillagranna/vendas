<?php
require_once 'header.php';
?>

<html>
    <body>
        <div class="centro">
            <h3 class="textos">Cadastro de Produto</h3><br>
            <div class="form-group">
                <form method="post" id="formProduto">
                    <label>Descrição:</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required><br>
                    <label>Preço:</label>
                    <input type="number" class="form-control" id="preco" name="preco"><br>
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="js/scriptProduto.js"></script>
    </body>
</html>

