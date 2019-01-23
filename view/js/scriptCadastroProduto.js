
$(function () {
    $('#formProduto').submit(function() {
        $.ajax({
            type: "POST",
            url: "http://localhost/vendas/controller/ControllerProduto.php",
            data: $(this).serialize(),
            success: function() {
                alert('Produto cadastrado com sucesso!');
            },
            error: function() {
                alert('Erro ao cadastrar produto!');
            }
        });
    });
});



