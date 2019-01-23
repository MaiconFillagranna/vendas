$(function () {
    debugger;
    $.ajax({
        type: "GET",
        url: "http://localhost/vendas/controller/ControllerVenda.php",
        data: {
            'requisicao': 'consulta'
        },
        success: function(data) {
            let total = 0;
            $(data).each(function() {
                total = total + parseFloat(this.total);
                $('#tableBody').append('<tr><td>'+this.numero+'</td><td>'+this.total+'</td></tr>');
            });
            $('#valorTotal').html(total);
        },
        error: function() {
            alert('Ocorreu um erro ao buscar as vendas!');
        }
    });
});

