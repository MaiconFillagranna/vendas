$(function () {
    $.ajax({
        type: "GET",
        url: "http://localhost/vendas/controller/ControllerProduto.php",
        data: {
            'requisicao': 'consulta'
        },
        success: function(data) {
            debugger;
            $(data).each(function() {
                debugger;
                $('#tableBody').append('<tr><td>'+this.codigo+'</td><td>'+this.descricao+'</td><td>'+this.preco+'</td></tr>');
            });
        },
        error: function() {
            alert('Ocorreu um erro ao buscar os produtos!');
        }
    });
});

