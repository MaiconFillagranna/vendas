<?php
require_once 'header.php';
?>

<html>
    <body>
        <div class="row col-md-6">
            <h3 class="textos">Consulta de Produtos</h3>
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
        <script type="text/javascript" src="js/scriptConsultaProduto.js"></script>
    </body>
</html>