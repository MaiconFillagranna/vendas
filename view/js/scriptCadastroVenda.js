
$(function () {
    $.ajax({
        type: "GET",
        url: "http://localhost/vendas/controller/ControllerVenda.php",
        data: {
            'requisicao': 'vendaAtual'
        },
        success: function(data) {
            $('#vendaAtual').val(data);
        },
        error: function() {
            alert('Ocorreu um erro ao buscar o numero da venda!');
        }
    });
    
    $('#adicionarProduto').click(function() {
        $.ajax({
            type: "GET",
            url: "http://localhost/vendas/controller/ControllerProduto.php",
            data: {
                'codigo': $('#idProduto').val(),
                'venda': $('#vendaAtual').val()
            },
            success: function(data) {
                if(data === 'nenhum') {
                    alert('Nenhum produto encontrado, ou o produto já foi adicionado na venda.');
                }
                else {
                    $('#tableBody').append('<tr><td>'+data.codigo+'</td><td>'+data.descricao+'</td><td>'+data.preco+'</td></tr>');
                }
            },
            error: function() {
                alert('Erro ao buscar produto!');
            }
        });
    });
    
    $('#cancelarVenda').click(function() {
        $.ajax({
            type: "POST",
            url: "http://localhost/vendas/controller/ControllerVenda.php",
            data: {
                'venda': $('#vendaAtual').val(),
                'requisicao': 'delete'
            },
            success: function() {
                alert('Venda cancelada com sucesso!');
                location.reload();
            },
            error: function() {
                alert('Erro ao deletar venda!');
            }
        });
    });
    
    $('#salvarVenda').click(function() {
        $.ajax({
            type: "POST",
            url: "http://localhost/vendas/controller/ControllerVenda.php",
            data: {
                'venda': $('#vendaAtual').val(),
                'requisicao': 'insert'
            },
            success: function() {
                alert('Venda salva com sucesso!');
                location.reload();
            },
            error: function() {
                alert('Erro ao salvar venda!');
            }
        });
    });
    
});



