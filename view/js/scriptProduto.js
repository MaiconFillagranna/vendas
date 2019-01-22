
$(function () {
    $('#formProduto').submit(function() {
        $.ajax({
            type: "POST",
            url: "http://localhost/vendas-master/controller/ControllerProduto.php",
            data: $(this).serialize(),
            success: function() {
                debugger;
            },
            error: function() {
                debugger;
            },
            finally: function() {
                debugger;
            }
        }).done(function( msg ) {
            debugger;
          });
    });
});



