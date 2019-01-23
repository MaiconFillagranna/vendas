<?php
require_once 'header.php';
?>

<html>
    <body>
        <div class="row col-md-6">
            <h3 class="textos">Consulta de Vendas</h3>
            <table class="table col-md-6">
                <thead>
                    <tr>
                        <th scope="col">Numero</th>
                        <th scope="col">Valor</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
                <tfoot>
                    <tr>
                        <th>Valor Total:</th>
                        <th scope="col" id="valorTotal"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script type="text/javascript" src="js/scriptConsultaVenda.js"></script>
    </body>
</html>