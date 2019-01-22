<?php
require_once 'view/topo.php';

$empresa = $_SESSION['codigo'];
?>
<div class="container">
    <div class="page-header alteracao">
        <h3>NFE<small class="alteracao"> // Corrigir numero das notas</small></h3>
    </div>
    <div id="conteudo">
        <form id="formCentroCustoUpdate" method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-3">
                    <label>Venda*</label><input type="text" style="margin-bottom: 10px;" data-required="required" autocomplete="off" class="form-control col-md-4" id="vcodigo" name="vcodigo" value=""/>
                </div>
                <div class="col-md-3">
                    <label>Nro Nota (Zerar informe 0)</label><input type="text" style="margin-bottom: 10px;" maxlength="100"  autocomplete="off" class="form-control" id="vnfnumero" name="vnfnumero" value=""/>
                </div>
                <div class="col-md-3" id="divTipoOperacao">
                    <div id="msgSucessoTOP" style="display: none;">
                        <div style="float:right">
                            <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">                            
                            </span>
                            <span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>
                        </div> 
                        <div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>
                    </div>
                    <label>Tipo Operação</label>
                    <input id="tipooperacao" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control " name="tipooperacao" />
                    <input id="tipooperacaoid" type="text" style="margin-bottom: 10px;display: none;" autocomplete="off" class="form-control " name="tipooperacaoid"/>
                    <div id="labelAddtop" class="labelComplete-field" style="display: none;">
                        <span class="pull-left">Adicionar</span>
                        <span id="textComptipooperacao" class="text-complete"></span>
                        <button type='button' name='confirma' id='confirmatop' class='btn-add'>
                            <i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>                           
                        </button>
                    </div>
                    <div class="fa fa-refresh fa-spin autocompleteLoading" id="topload" style="display:none;"></div>
                </div>    
                <div class="col-md-3">
                    <label>Status Pendente</label>
                    <select name="status" class="form-control">
                        <option selected value="N">Não</option>
                       <option value="P">Sim</option> <!-- Pendente-->
                    </select>
                </div>                
            </div>

            <div class="row" style="margin-top: 25px">                
                <div class="col-xs-12 col-sm-4 col-md-9">
                    <button type="button" id="enviar" name="enviar" class="btn btn-navegacao btn-success">
                        <span class="mdi-navigation-check span-btn-align" style="font-size: 25px;"></span> <span class="span-btn-align-text">Salvar Cadastro</span>
                    </button>
                </div>
            </div>


        </form>
    </div>
</div>
<script>
    $(function () {

        //Tipo Operação
        $("#tipooperacao").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'TOP'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $('#labelAddtop').hide();
                                $("#tipooperacao").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#tipooperacaoid").val('');
                                $('#labelAddtop').show();
                                $("#tipooperacao").addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value,
                                cf: item.cf
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                if (!$('#labelAddtop').is(':visible')) {
                    $("#tipooperacaoid").val(ui.item.value);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $("#tipooperacao").val(ui.item.label);
                    if ($("#tipooperacao").val() == '') {
                        $("#tipooperacaoid").val(null);
                    } else {
                        $("#tipooperacaoid").val(ui.item.value);
                    }


                    $('#labelAddtop').hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (ui.content.length === 1) {
                    $('#labelAddtop').text('Registro não encontrado.');
                    existe = 'N';
                } else if (ui.content.length > 1) {
                    $('#topload').show();
                    existe = 'S';
                }
            },
            search: function (event, ui) {
                $('#tipooperacaoid').val('');
                $('#topload').show();
            },
            open: function (event, ui) {
                $('#topload').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };
        $("#tipooperacao").on('keyup', function () {
            var dado = $("#tipooperacao").val();
            $("#textComptipooperacao").text(dado);
            if (dado.length < 2) {
                $('#labelAddTOP').hide();
                $("#tipooperacaoid").val('');
                $("#tipooperacao").removeClass("adicionarRegistro");
            }

        });
        $("#tipooperacao").focus(function () {
            $("#tipooperacao").autocomplete("search", $("#tipooperacao").val());
        });




        var $formNome = "#" + $('form').attr('id');
        var $form = $($formNome);
        var erro = false;
        $("#enviar").on('click', function (e) {
            e.preventDefault();
            $($formNome + ' input').each(function () {
                if ($(this).val() === "" && $(this).data("required") === "required" && $(this).is(':visible')) {
                    $(this).addClass("error");
                    $(this).focus();
                    erro = true;
                    return false;
                } else if (!($(this).val() === "") && $(this).data("required") === "required" && $(this).is(':visible')) {
                    $(this).removeClass("error");
                    erro = false;
                    erro--;
                }
            });
            if (erro === -1) {
                $($formNome).submit();
            }
        });

        $form.submit(function (event) {
            jQuery.ajax({
                type: "POST",
                url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/alteranota/altera.php",
                data: $(this).serialize(),
                success: function () {
                    swal({
                        title: "Sucesso!",
                        text: "Alterado com sucesso!",
                        type: "success",
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        window.location.href = '<?php echo $URL_BASE_PATH ?>/modulos/venda/alteranota/alteranota.php';
                    }, 800);
                }
            });

            return false;
        });



    });
</script>
<?php
include_once 'view/rodape.php';
