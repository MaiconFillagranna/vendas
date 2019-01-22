<!DOCTYPE html>
<html>
    <head>
        <title>Vendas</title>
        <meta charset="utf-8"/>
        <link type="text/css" rel="stylesheet" href="css/bootstrap.css"/>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link type="text/css" rel="stylesheet" href="css/style.css"/>
        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>  
    </head>
    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="http://localhost/vendas/view/index.php">Vendas</a>
                </div>

                <div class="collapse navbar-collapse" style="height: 100px">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-copy"></span> Cadastros <span class="caret "></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="cadastroProduto.php">Cadastrar Produto</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="cadastroVenda.php">Cadastrar Venda</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-list-alt"></span> Consultas <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="consultaVenda.php">Vendas</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="login.html"><span class="glyphicon glyphicon-log-out"></span> Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </body>
</html>