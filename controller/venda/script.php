<?php
require_once 'modulos/pessoa/autocompleteScripts.php';
$usuario = $_SESSION['flagCadastro']; //Flag do Usuário
$codUsu = $_SESSION['codigoUsuario'];
$tipoUsu = $_SESSION['tipoUsuario'];
$retornoIndustrializacao = isset($dadosRetorno) ? 'true' : 'false';
?>
<script type="text/javascript">
    var usuario = '<?php echo $usuario ?>', codUsu = '<?php echo $codUsu ?>', tipoUsu = '<?php echo $tipoUsu ?>',
            espcodto = '<?php echo isset($espcodto) ? $espcodto : "" ?>', espnometo = '<?php echo isset($espnometo) ? $espnometo : "" ?>';

    //Pessoa
    $(function () {
        if (tipoUsu === 'V') {
            $('#divVendedor').hide();
            $('#vendedorId').val(codUsu);
        }

        $("#pessoa").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'P'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $('#labelAdd').hide();
                                $("#pessoa").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#pessoaId").val('');
                                $('#labelAdd').show();
                                $("#pessoa").addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value,
                                toID: item.toID,
                                toNome: item.toNome,
                                clienteExterior: item.clienteExterior
                            };
                        }));
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            focus: function (event, ui) {
                if (!$('#labelAdd').is(':visible')) {
                    $("#pessoaId").val(ui.item.value);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $("#pessoa").val(ui.item.label);
                    $("#pessoaId").val(ui.item.value);
                    if (ui.item.toNome !== null) {
                        $("#tipooperacao").val(ui.item.toNome);
                        $("#tipooperacaoid").val(ui.item.toID);
                    }
                    $("#clienteExterior").val(ui.item.clienteExterior);
                    $('#labelAdd').hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $('#pload').hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $('#pload').show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $('#labelAdd').text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $('#pload').show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $('#pessoaId').val('');
                $('#pload').show();
            },
            open: function (event, ui) {
                $('#pload').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        $("#pessoa").on('keydown', function (e) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key === 9 && existe === 'N' && usuario === 'S') {
                var pessoa = $('#pessoa').val();
                if (pessoa.length > 1) {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                        data: {
                            dados: pessoa,
                            op: 'USU'
                        },
                        success: function (resposta) {
                            $('#pessoaId').val(resposta);
                            $('#pessoa').removeClass("adicionarRegistro");
                            $('#labelAdd').hide(0);
                            $('#pload').hide();
                            $('#msgSucessoFor').show("fast");
                            $("#divFornecedor").addClass("has-success");
                            $("#cbdatasaldo").focus();
                            setTimeout(function () {
                                $('#msgSucessoFor').hide();
                                $('#divFornecedor').removeClass("has-success");
                            }, 3000);
                        }
                    });
                }
            }

        });
        $("#pessoa").on('keyup', function () {
            var dado = $("#pessoa").val();
            $("#textCompFornecedor").text(dado);
            if (dado.length < 2) {
                $('#labelAdd').hide();
                $("#pessoaId").val('');
                $("#pessoa").removeClass("adicionarRegistro");
            }

        });
        $("#pessoa").focus(function () {
            $("#pessoa").autocomplete("search", $("#pessoa").val());
        });
        $("#confirma").on('click', function () {
            var dados = jQuery('#pessoa').val();
            if (dados.length > 1) {
                jQuery.ajax({
                    type: "POST",
                    minLength: 1,
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                    data: {
                        dados: dados,
                        op: 'USU'
                    },
                    success: function (resposta) {
                        $('#pessoaId').val(resposta);
                        $('#pessoa').removeClass("adicionarRegistro");
                        $('#labelAdd').hide(0);
                        $('#pload').hide();
                        $('#msgSucessoFor').show("fast");
                        $("#divFornecedor").addClass("has-success");
                        $("#cbdatasaldo").focus();
                        setTimeout(function () {
                            $('#msgSucessoFor').hide();
                            $('#divFornecedor').removeClass("has-success");
                        }, 3000);
                    }
                });
            }
        });
        //Autocomplete espécie
        $("#esp").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'ESP'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $("#esp").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#espId").val('');
                                $("#esp").addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            select: function (event, ui) {
                event.stopPropagation();
                if (ui.item.label !== '') {
                    $("#esp").val(ui.item.label);
                    $("#espId").val(ui.item.value);
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $('#loadesp').hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $('#esplabel').text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $('#espId').val('');
                $('#loadesp').show();
            },
            open: function (event, ui) {
                $('#loadesp').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        //AutoComplete Centro de Custo
        $("#cc").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'CC'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $("#cc").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#ccId").val('');
                                $("#cc").addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            select: function (event, ui) {
                event.stopPropagation();
                if (ui.item.label !== '') {
                    $("#cc").val(ui.item.label);
                    $("#ccId").val(ui.item.value);
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $('#loadcc').hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $('#cclabel').text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $('#ccId').val('');
                $('#loadcc').show();
            },
            open: function (event, ui) {
                $('#loadcc').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        //Auto Complete Classificação Financeira
//Com os AutosCompletes
        var existe = '';
        $(function () {
            $("#ClassiFin").autocomplete({
                source: function (request, response) {
                    jQuery.ajax({
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteAux.php",
                        dataType: "json",
                        cache: false,
                        data: {
                            str: request.term,
                            op: 'CF',
                            to: $("#tipooperacaoid").val()
                        },
                        success: function (data) {
                            response($.map(data, function (item) {
                                return {
                                    label: item.label,
                                    value: item.value
                                };
                            }));
                        }
                    });
                },
                minLength: 0,
                autoFocus: true,
                focus: function (event, ui) {
                    if (!$('#labelCF').is(':visible')) {
                        $("#ClassiFinId").val(ui.item.value);
                    }
                    return false;
                },
                select: function (event, ui) {
                    if (ui.item.label !== '') {
                        $("#ClassiFin").val(ui.item.label);
                        $("#ClassiFinId").val(ui.item.value);
                        $('#labelCF').hide();
                    }
                    return false;
                },
                response: function (event, ui) {
                    if (usuario === 'S') {
                        if (ui.content.length === 1) {
                            $('#cfload').hide();
                            existe = 'N';
                        } else if (ui.content.length > 1) {
                            $('#cfload').show();
                            existe = 'S';
                        }
                    } else if (usuario === 'N') {
                        if (ui.content.length === 1) {
                            $('#labelCF').text('Registro não encontrado.');
                            existe = 'N';
                        } else if (ui.content.length > 1) {
                            $('#cfload').show();
                            existe = 'S';
                        }
                    }
                },
                search: function (event, ui) {
                    $('#ClassiFinId').val('');
                    $('#cfload').show();
                },
                open: function (event, ui) {
                    $('#cfload').hide();
                }

            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
            };
            
            $("#ClassiFin").on('keyup', function () {
                var dado = $("#ClassiFin").val();
                $("#textCompCF").text(dado);
                if (dado.length < 2) {
                    $('#labelCF').hide();
                    $("#ClassiFinId").val('');
                    $("#ClassiFin").removeClass("adicionarRegistro");
                }
            });
            $("#ClassiFin").focus(function () {
                $("#ClassiFin").autocomplete("search", $("#ClassiFin").val());
            });
        });

        $(document).ready(function () {

            if ($('#vavistaprazo').val() == "P") {
                $('#optionsRadios2').attr('checked', true);
                $('#optionsRadios1').attr('checked', false);
//            $('#divCF').hide();
                $('#Parcelas').show(500);
            } else {
                $('#optionsRadios1').attr('checked', true);
                $('#optionsRadios2').attr('checked', false);
//            $('#divCF').show();
                $('#Parcelas').hide("slow");
            }
        });


//-------


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
                                gerafin: item.gerafin,
                                classifid: item.classifid,
                                classifin: item.classifin,
                                espnome: item.espnome,
                                espcod: item.espcod
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
                    $("#ClassiFinId").val(ui.item.classifid);
                    $("#ClassiFin").val(ui.item.classifin);
                    $("#esp").val(ui.item.espnome);
                    $("#espId").val(ui.item.espcod);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $("#tipooperacao").val(ui.item.label);
                    $("#tipooperacaoid").val(ui.item.value);
                    $('#labelAddtop').hide();
                    $("#esp").val(ui.item.espnome);
                    $("#espId").val(ui.item.espcod);
                }
                if (ui.item.gerafin === 'S') {
                    $('#divCB').show();
                    $('#divCF').show();
                    $('#DivFormPag').show();

                    $('#ContBanc').attr("data-required", "required");
                    $('#ContBanc').show();
                    $('#ClassiFin').attr("data-required", "required");

                    $('#ClassiFin').show();
                } else if (ui.item.gerafin === 'N') {
                    $('#divCB').hide();
                    $('#divCF').hide();
                    $('#DivFormPag').hide();


                    $('#ContBanc').removeAttr('data-required');
                    $('#ContBanc').removeClass("error");
                    $('#ContBanc').hide();
                    $('#ClassiFin').removeAttr('data-required');
                    $('#ClassiFin').removeClass("error");
                    $('#ClassiFin').hide();
                    $('#ContBancId').val(null);
                    $('#ContBanc').val(null);
                    $('#ClassiFinId').val(null);
                    $('#ClassiFin').val(null);
                }
                if (ui.item.label !== '' && ui.item.gerafin === 'S') {
//                if (ui.item.label !== '') {
                    $("#tipooperacao").val(ui.item.label);
                    $("#tipooperacaoid").val(ui.item.value);
                    jQuery.ajax({
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteAux.php",
                        dataType: "json",
                        cache: false,
                        data: {
                            str: ui.item.cf,
                            op: 'getCF',
                            to: $("#tipooperacaoid").val()
                        },
                        success: function (data) {

                            $('#Parcelas').find('[name="classfinP[]"]').val(data);
                            $('#Parcelas').find('[name="classfinIDP[]"]').val(ui.item.cf);

                            $('#Dados').find('[name="classfin"]').val(data);
                            $('#Dados').find('[name="classfinID"]').val(ui.item.cf);
                        }
                    });
                    $('#labelAddtop').hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (ui.content.length === 1) {
                    $('#labelAddtop').text('Registro nÃ£o encontrado.');
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
    });
    //Produtos
    function setupAutoCompleteProduto($nome, $load, $label, $completeNome, $codigo, $valorUnitario, $valorCusto, $quantidade, $total, $totalcusto, $sucess, $produto, $confirma, $porcentagemIPI) {
        $nome.autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        to: $('#tipooperacaoid').val(),
                        op: 'PS'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $label.hide();
                                $nome.removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $codigo.val('');
                                $label.show();
                                $nome.addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value,
                                vunitario: item.vunitario,
                                vcusto: item.vcusto,
                                porcentagemipi: item.porcentagemipi
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                if (!$label.is(':visible')) {
                    $codigo.val(ui.item.value);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $nome.val(ui.item.label);
                    $codigo.val(ui.item.value);
                    $valorUnitario.val(formatValorDinheiro4(ui.item.vunitario));
                    $valorCusto.val(formatValorDinheiro4(ui.item.vcusto));
                    $quantidade.val('1,0000');
                    $quantidade.attr('disabled', false);
                    $valorUnitario.attr('disabled', false);
                    $valorCusto.attr('disabled', false);
                    $total.val(formatValorDinheiro4(ui.item.vunitario));
                    $totalcusto.val(formatValorDinheiro4(ui.item.vunitario));
                    if($porcentagemIPI && ui.item.porcentagemipi) {
                        $porcentagemIPI.val(formatValorDinheiro(ui.item.porcentagemipi));
                    }                    
                    $label.hide();
                    calculaTotalProduto($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto);
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $load.hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $load.show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $label.text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $load.show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $codigo.val('');
                $load.show();
            },
            open: function (event, ui) {
                $load.hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        $nome.on('keydown', function (e) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key === 9 && existe === 'N' && usuario === 'S') {
                var produto = $nome.val();
                if (produto.length > 1) {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                        data: {
                            dados: produto,
                            op: 'PRD'
                        },
                        success: function (resposta) {
                            $codigo.val(resposta);
                            $nome.removeClass("adicionarRegistro");
                            $label.hide(0);
                            $load.hide();
                            $quantidade.val('1,00');
                            $quantidade.attr('disabled', false);
                            $valorUnitario.attr('disabled', false);
                            $valorCusto.attr('disabled', false);
                            $sucess.show("fast");
                            $produto.addClass("has-success");
                            $quantidade.focus();
                            calculaTotalProduto($quantidade, $valorUnitario, $valorCusto, $total);
                            setTimeout(function () {
                                $sucess.hide();
                                $produto.removeClass("has-success");
                            }, 3000);
                        }
                    });
                }
            }

        });
        $nome.on('keyup', function () {
            var dado = $nome.val();
            $completeNome.text(dado);
            if (dado.length < 2) {
                $label.hide();
                $codigo.val('');
                $nome.removeClass("adicionarRegistro");
            }

        });
        $nome.focus(function () {
            $nome.autocomplete("search", $nome.val());
        });
        $confirma.on('click', function () {
            var dados = $nome.val();
            if (dados.length > 1) {
                jQuery.ajax({
                    type: "POST",
                    minLength: 1,
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                    data: {
                        dados: dados,
                        op: 'PRD'
                    },
                    success: function (resposta) {
                        $codigo.val(resposta);
                        $nome.removeClass("adicionarRegistro");
                        $label.hide(0);
                        $load.hide();
                        $quantidade.val('1,00');
                        $quantidade.attr('disabled', false);
                        $valorUnitario.attr('disabled', false);
                        $sucess.show("fast");
                        $produto.addClass("has-success");
                        $quantidade.focus();
                        setTimeout(function () {
                            $sucess.hide();
                            $produto.removeClass("has-success");
                        }, 3000);
                    }
                });
            }
        });
    }
    //Conta Bancaria
    $(function () {
        $("#ContBanc").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'CB'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $('#labelCB').hide();
                                $("#ContBanc").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#ContBancId").val('');
                                $('#labelCB').show();
                                $("#ContBanc").addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                if (!$('#labelCB').is(':visible')) {
                    $("#ContBancId").val(ui.item.value);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $("#ContBanc").val(ui.item.label);
                    $("#ContBancId").val(ui.item.value);
                    $('#labelCB').hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $('#cbload').hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $('#cbload').show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $('#labelCB').text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $('#cbload').show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $('#ContBancId').val('');
                $('#cbload').show();
            },
            open: function (event, ui) {
                $('#cbload').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        $("#ContBanc").on('keydown', function (e) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key === 9 && existe === 'N' && usuario === 'S') {
                var ContBanc = $('#ContBanc').val();
                console.log(ContBanc);
                if (ContBanc.length > 1) {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                        data: {
                            dados: ContBanc,
                            op: 'CB'
                        },
                        success: function (resposta) {
                            $('#ContBancId').val(resposta);
                            $('#ContBanc').removeClass("adicionarRegistro");
                            $('#labelCB').hide(0);
                            $('#cbload').hide();
                            $('#msgSucessoCB').show("fast");
                            $("#divCB").addClass("has-success");
                            setTimeout(function () {
                                $('#msgSucessoCB').hide();
                                $('#divCB').removeClass("has-success");
                            }, 3000);
                        }
                    });
                }
            }
        });

        $("#ContBanc").on('keyup', function () {
            var dado = $("#ContBanc").val();
            $("#textCompCB").text(dado);
            if (dado.length < 2) {
                $('#labelCB').hide();
                $("#ContBancId").val('');
                $("#ContBanc").removeClass("adicionarRegistro");
            }

        });
        $("#ContBanc").focus(function () {
            $("#ContBanc").autocomplete("search", $("#ContBanc").val());
        });
    });


    $(document).ready(function () {
        $("#confirmaCB").on('click', function () {
            var dados = jQuery('#ContBanc').val();
            if (dados.length > 1) {
                jQuery.ajax({
                    type: "POST",
                    minLength: 1,
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/cadastraAutoComplete.php",
                    data: {
                        dados: dados,
                        op: 'CB'
                    },
                    success: function (resposta) {
                        $('#ContBancId').val(resposta);
                        $('#ContBanc').removeClass("adicionarRegistro");
                        $('#labelCB').hide(0);
                        $('#cbload').hide();
                        $('#msgSucessoCB').show("fast");
                        $("#divCB").addClass("has-success");
                        setTimeout(function () {
                            $('#msgSucessoCB').hide();
                            $('#divCB').removeClass("has-success");
                        }, 3000);
                    }
                });
            }
        });
    });
    //Vendedor
    $(function () {
        $("#vendedor").autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'T'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $("#vendedor").removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $("#vendedorId").val('');
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                $("#vendedorId").val(ui.item.value);
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $("#vendedor").val(ui.item.label);
                    $("#vendedorId").val(ui.item.value);
                    $('#labelAddVendedor').hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (ui.content.length === 0) {
                    $('#labelAddVendedor').show();
                    $('#labelAddVendedor').text('Registro não encontrado.');
                    existe = 'N';
                } else if (ui.content.length > 1) {
                    $('#tload').show();
                    existe = 'S';
                }
            },
            search: function (event, ui) {
                $('#vendedorId').val('');
                $('#tload').show();
            },
            open: function (event, ui) {
                $('#tload').hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };
        $("#vendedor").on('keyup', function () {
            var dado = $("#vendedor").val();
            $("#textCompFornecedor").text(dado);
            if (dado.length < 2) {
                $('#labelAddVendedor').hide();
                $("#vendedorId").val('');
                $("#vendedor").removeClass("adicionarRegistro");
            }

        });
        $("#vendedor").focus(function () {
            $("#vendedor").autocomplete("search", $("#vendedor").val());
        });
    });
    function addMsg(data) {
        if (jQuery.isArray(data.mensagem)) {
            jQuery.each(data.mensagem, function () {
                $('#mensagens').html('<div class="' + data.tipo + '" role="alert"><strong>' + data.assunto + '</strong> ' + this + '</div>');
            });
        } else {
            $('#mensagens').html('<div class="' + data.tipo + '" role="alert"><strong>' + data.assunto + '</strong> ' + data.mensagem + '</div>');
        }
    }

    function calculaTotal() {
        let descricao = $('#videscricaoitem').val();
        
        if (descricao == null || descricao == '') {
            let sum = 0;
            let sumIPI = 0;
            let sumcusto = 0;
            let sumDesconto = 0;
            let $total = $('#totalprodutos');
            let $totalcusto = $('#totalcustogeral');
            let $desconto = $('#desconto');
            let $descontoPorc = $('#descontoPorc');
            let $IPI = $('#valortotalipi');
            let $clienteExterior = $('#clienteExterior');
            let $produtos = $('[name="produtos[]"]');
            $produtos.each(function() {
             
                let $total = $(this).find('[name="total[]"]');
                let $quantidade = $(this).find('[name="viquantidade[]"]');
                let fQuantidade = parseValorDinheiro($quantidade.val());
                let $valorUnitario = $(this).find('[name="vivalorunitario[]"]');
                let fValorUnitario = parseValorDinheiro($valorUnitario.val());
                let $desconto = $(this).find('[name="vivalordesconto[]"]');
                let fDesconto = parseValorDinheiro($desconto.val());
                let fTotal = fQuantidade*fValorUnitario-fDesconto;                
                let $custo = $(this).find('[name="totalcusto[]"]');
                let fCusto = parseValorDinheiro($custo.val());
                let $porcentagemIPI = $(this).find('[name="porcentagemipi[]"]');
                let fPorcentagemIPI = parseValorDinheiro($porcentagemIPI.val());
                let $valorIPI = $(this).find('[name="valoripi[]"]');
                //Tratamentos do campo IPI necessário para não ter problemas com arredondamento
                let fValorIPI = fPorcentagemIPI*fTotal/100;
                let sValorIPI = formatValorDinheiro(fValorIPI);
                let fNovoValorIPI = parseValorDinheiro(sValorIPI);
                if($clienteExterior.val() == true) {
                    fValorIPI = 0;
                }
                $total.val(formatValorDinheiro(fTotal));
                $valorIPI.val(sValorIPI);                
                sum += fTotal;
                sumIPI += fNovoValorIPI;
                sumDesconto += fDesconto;
                sumcusto += fCusto; 
            });
            
            $IPI.val(formatValorDinheiro(sumIPI));
            $total.val(formatValorDinheiro(sum+sumDesconto));
            $totalcusto.val(formatValorDinheiro(sumcusto));
            
            if(sumDesconto > 0) {
                $desconto.val(formatValorDinheiro(sumDesconto));
                var descontoPorc = sumDesconto/(sum+sumDesconto)*100;
                $descontoPorc.val(formatValorDinheiro(descontoPorc));
            }
            else if($desconto.is('[readonly]')){
                $desconto.val(formatValorDinheiro(0));
                $descontoPorc.val(formatValorDinheiro(0));
            }          
        }
    }


    function calculaDesconto() {
        var res,
            total = parseValorDinheiro($('#totalprodutos').val()),
            desconto = parseValorDinheiro($("#desconto").val()),
            frete = parseValorDinheiro($("#vnfvalorfrete").val()),
            vvaloracrescimo = parseValorDinheiro($("#vvaloracrescimo").val()),
            IPI = parseValorDinheiro($("#valortotalipi").val()),
            $totalPagar = $('#totalpagar');

        res = total - desconto + frete + IPI + vvaloracrescimo;

        $totalPagar.val(formatValorDinheiro(res));
    }
    
    function onBlurDesconto() {
        rateiaDesconto();
        calculaTotalPagar();
    }
    
    function rateiaDesconto() {
        let $produtos = $('[name="produtos[]"]');
        let $desconto = $('#desconto');
        let fDesconto = parseValorDinheiro($desconto.val());
        let $descontoPorc = $('#descontoPorc');
        let fPercentualDesconto = parseValorDinheiro($descontoPorc.val());
        let aux = 0;
        
        if($desconto.is('[readonly]')) {
            return;
        }
        
        $produtos.each(function() {
            let $quantidade = $(this).find('[name="viquantidade[]"]');
            let fQuantidade = parseValorDinheiro($quantidade.val());
            let $valorUnitario = $(this).find('[name="vivalorunitario[]"]');
            let fValorUnitario = parseValorDinheiro($valorUnitario.val());
            let fTotal = fQuantidade*fValorUnitario;
            let $desconto = $(this).find('[name="vivalordesconto[]"]');
            let fValorDescontoItem = fTotal*fPercentualDesconto/100;
            let auxiliarArredontamento = formatValorDinheiro(fValorDescontoItem);
            aux += parseValorDinheiro(auxiliarArredontamento);
            $desconto.val(auxiliarArredontamento);
        });        
                
        if(aux != fDesconto) {
            let $descontos = $('[name="vivalordesconto[]"]');
            let $primeiroDesconto = $($descontos[0]);
            let sRestoDesconto = (fDesconto - aux).toFixed(2);
            let fRestoDesconto = parseFloat(sRestoDesconto);
            let fPrimeiroDesconto = parseValorDinheiro($primeiroDesconto.val());
            $primeiroDesconto.val(formatValorDinheiro(fPrimeiroDesconto+fRestoDesconto));
        }        
    }

    function calculaTotalPagar() {
        calculaTotal();
        calculaDesconto();
        calculaParcelamento();
    }

    function calculaTotalProduto($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto, $inputDesconto) {
        var quantidade = parseValorQuantidade($quantidade.val());
        var valorUnitario = parseValorDinheiro($valorUnitario.val());
        var valorCusto = parseValorDinheiro($valorCusto.val());

        var valorfinal = quantidade * valorUnitario;
        var valorfinalcusto = quantidade * valorCusto;
        
        if($inputDesconto) {
            valorfinal = valorfinal - parseValorDinheiro($inputDesconto.val());
        }

        $total.val(formatValorDinheiro(valorfinal));
        $totalcusto.val(formatValorDinheiro(valorfinalcusto));

        calculaTotalPagar();
    }

    function setupCalculoTotal($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto, $inputDesconto) {
        function calcula() {
            calculaTotalProduto($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto, $inputDesconto);
        }
        function calcula2() {
            calculaTotal();
        }

        $quantidade.blur(calcula);
        $totalcusto.blur(calcula2);
        $valorUnitario.blur(calcula);
    }

    function setupButtonRemove($btnRemoverCampo) {
        $btnRemoverCampo.bind("click", function () {
            var r = confirm("Confirma exclusão do produto?");
            if (r === true) {
                $btnRemoverCampo.parents(".linhas").remove();
                calculaTotalPagar();
            }
        });
    }

    function setupButtonRemoveParcela($btnRemoverParcela) {
        $btnRemoverParcela.bind("click", function () {
            var r = confirm("Confirma exclusão da parcela?");
            if (r === true) {
                $btnRemoverParcela.parents(".linhaParcela").remove();
            }
        });
    }


    $(function () {
        $('#optionsRadios1').click(function () {
            var isChecked = $('#optionsRadios1').is(':checked');
            $('#Parcelas').hide("slow");
            $('#divEspecie').show();
            $('#divCentroCusto').show();
            $('#divCF').show();
        });

        $('#optionsRadios2').click(function () {
            var isChecked = $('#optionsRadios2').is(':checked');
            $('#Parcelas').show(500);
            $('#divEspecie').hide("slow");
            $('#divCentroCusto').hide("slow");
            $('#divCF').hide();
        });

        $("#ocultar").click(function (event) {
            event.preventDefault();
            $("#BLOCO").hide("slow");
            $("#ocultar").hide();
            $("#mostrar").show();
        });

        $("#mostrar").click(function (event) {
            event.preventDefault();
            $("#BLOCO").show(500);
            $("#mostrar").hide();
            $("#ocultar").show();
        });
        $("#ocultar2").click(function (event) {
            event.preventDefault();
            $("#BLOCO2").hide("slow");
            $("#ocultar2").hide();
            $("#mostrar2").show();
        });

        $("#mostrar2").click(function (event) {
            event.preventDefault();
            $("#BLOCO2").show(500);
            $("#mostrar2").hide();
            $("#ocultar2").show();
        });

        setupMascaraData($('#datasaldo input'));
//        .datepicker({
//            format: "dd/mm/yyyy",
//            language: "pt-BR",
//            autoclose: true,
//            todayHighlight: true
//        });
    });

    $(function () {
        $(".adicionarCampo").click(function () {
            var $linha = $(".linhas:first").clone(),
                    $viNome = $linha.find('[name="vinome[]"]'),
                    $vicodservproduto = $linha.find('[name="vicodservproduto[]"]'),
                    $vivalorunitario = $linha.find('[name="vivalorunitario[]"]'),
                    $quantidade = $linha.find('[name="viquantidade[]"]'),
                    $valorUnitario = $linha.find('[name="vivalorunitario[]"]'),
                    $valorCusto = $linha.find('[name="vivalorvalorcusto[]"]'),
                    $total = $linha.find('[name="total[]"]'),
                    $totalcusto = $linha.find('[name="totalcusto[]"]'),
                    $btnRemove = $linha.find(".removerCampo");

            // limpa todos os campos da linha
            $linha.find("input").val("");

            // insere a linha no final da tabela
            $linha.insertAfter(".linhas:last");

            // adiciona os eventos nos componentes
            setupAutoCompleteProduto($viNome, $vicodservproduto, $vivalorunitario, $quantidade, $total, $totalcusto);
            setupMascaraValorQuantidade($quantidade);
            setupCalculoTotal($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto);
            setupButtonRemove($btnRemove);
            setupMascaraValorDinheiro($valorUnitario);
        });
    });

    function arredondaDinheiro(valor, decimal) {
        return Number(Math.round(valor + 'e' + decimal) + 'e-' + decimal);
    }

    function novaLinhaProduto(produto) {
        produto = produto || {};
        var $linhaProduto =
                $('<div class="linhas" id="produtos" name="produtos[]">' +
                    '<div style="padding-right: 5px;padding-left:0px;" class="col-md-5">' +
                    '<div id="msgSucessoProd" style="display: none;">' +
                    '<div style="float:right">' +
                    '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">' +
                    '</span>' +
                    '<span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>' +
                    '</div>' +
                    '<div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>' +
                    '</div>' +
                    '<label>Produto*</label>' +
                    '<input type="text" class="form-control" name="vinome[]" value="" data-required="required">' +
                    '<div class="fa fa-refresh fa-spin autocompleteLoading" id="psload" style="display:none; margin-top: -28px;"></div>' +
                    '<input type="hidden" class="form-control" name="vicodservproduto[]" value="" >' +
                    '<div id="labelComplete" class="labelComplete" style="display: none;"> ' +
                    '<span class="pull-left">Adicionar </span>' +
                    '<span id="completeNome" class="text-complete"></span>' +
                    '<button type="button" name="confirmaProd" id="confirmaProd" class="btn-add"> ' +
                    '<i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>' +
                    '</button>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div >' +
                    '<div style="padding-right: 5px;padding-left:0px;" class="col-md-1">' +
                    '<label>QTD*</label>' +
                    '<input type="text" disabled="true" class="form-control" name="viquantidade[]" value="" data-required="required">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div style="padding-right: 5px;padding-left:0px;" class="col-md-3">' +
                    '<div>' +
                    '<div class="col-md-6" style="padding-right: 5px;padding-left:0px;">' +
                    '<label>Valor Unit*</label>' +
                    '<input type="text" disabled="true" placeholder="R$" class="form-control" name="vivalorunitario[]" value="" data-required="required">' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="col-md-6" style="padding-right: 0px;padding-left:0px;">' +
                    '<label name="botaodesconto[]"><a class="glyphicon glyphicon-minus-sign" style="color:#d6243b;"></a><a style="color:#d6243b;"> Desconto</a></label>' +
                    '<input type="text" readonly="true" placeholder="R$" class="form-control" name="vivalordesconto[]" value="">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div style="padding-right: 5px;padding-left:0px;" class="col-md-2">' +
                    '<label>Total</label>' +
                    '<input type="text" disabled="true" placeholder="R$" class="form-control" name="total[]" value="">' +
                    '</div>' +
                    '<div>' +
                    '<div style="padding-right: 5px;padding-left:0px; display: none;" class="col-md-1">' +
                    '<label>Custo Tot</label>' +
                    '<input type="text" disabled="true" placeholder="R$" class="form-control" name="vivalorcusto[]" value="">' +
                    '</div>' +                        
                    '<div style="display: none">' +
                    '<div style="padding-right: 5px;padding-left:0px;" class="col-md-1">' +
                    '<label>Custo Tot</label>' +
                    '<input type="text" placeholder="R$" class="form-control" name="totalcusto[]" value="">' +
                    '</div>' +
                    '</div>' +
                    '<div class=""><button type="button" class="removerCampo btn btn-default" title="Remover linha"><i class="mdi-action-delete"></i></button></div>' +
                    '</div>' +
                    //Inputs do IPI do produto, escondidos
                    '<input type="text" class="form-control" name="porcentagemipi[]" value="" style="display: none">' +
                    '<input type="text" class="form-control" name="valoripi[]" value="" style="display: none">' +
                    //Inicio do Modal
                    '<div name="modal[]" class="modal" style="overflow: hidden; background: transparent; height: 400px; width: 400px">'+
                    '<div class="modal-content" style="text-align: center">'+
                    '<div class="row" style="margin-top: 15px;">'+
                    '<div >Total do Produto<br>'+
                    '<span name="totPedDesc[]" style="font-size: 35px"></span> '+           
                    '</div>'+
                    '</div>'+
                    '<div class="row" style="margin-top: 15px">'+
                    '<div>Valor do Desconto em % <input type="text" name="valorDescPorc[]" class="form-control" style="width: 300px;margin: auto;"></div>'+
                    '</div>'+
                    '<div class="row" style="margin-top: 15px">'+
                    '<div>Valor do Desconto em R$ <input type="text" name="valorDescR[]" class="form-control" style="width: 300px;margin: auto;"></div>'+
                    '</div>'+
                    '<button class=" modal-action modal-close close-btn btn btn-finalizar" data-dismiss="modal" aria-label="Close" name="aplicarDesconto[]" style="float: none;margin-top: 15px;margin-bottom: 5px;">Aplicar Desconto</button>'+
                    '<br>'+
                    '<div name="msgErroDesc[]" style="display: none;">'+
                    '<span style="margin-top: 20px;color: #9A1717;display: block">Valor de desconto maior que o valor total da venda!</span>'+
                    '</div>'+
                    '</div>'+
                    '</div>');

        let $btnRemove = $linhaProduto.find(".removerCampo"),
            $nome = $linhaProduto.find('[name="vinome[]"]').val(produto.vinome || ''),
            $load = $linhaProduto.find('[id="psload"]'),
            $label = $linhaProduto.find('[id="labelComplete"]'),
            $sucess = $linhaProduto.find('[id="msgSucessoProd"]'),
            $confirma = $linhaProduto.find('[id="confirmaProd"]'),
            $completeNome = $linhaProduto.find('[id="completeNome"]'),
            $codigo = $linhaProduto.find('[name="vicodservproduto[]"]').val(produto.vicodservproduto || ''),
            $ftotal = parseValorDinheiro(produto.total)-parseValorDinheiro(produto.vivalordesconto) > 0 ? parseValorDinheiro(produto.total)-parseValorDinheiro(produto.vivalordesconto) : '',            
            $total = $linhaProduto.find('[name="total[]"]').val(formatValorDinheiro($ftotal)),
            $totalcusto = $linhaProduto.find('[name="totalcusto[]"]').val(produto.totalcusto || produto.vivalorcusto),
            $valorUnitario = $linhaProduto.find('[name="vivalorunitario[]"]').val(produto.vivalorunitario || ''),
            $valorCusto = $linhaProduto.find('[name="vivalorcusto[]"]').val(produto.vivalorcusto || ''),
            $quantidade = $linhaProduto.find('[name="viquantidade[]"]').val(produto.viquantidade || ''),
            $valorDesconto = $linhaProduto.find('[name="vivalordesconto[]"]').val(produto.vivalordesconto || ''),
            $desconto = $('#desconto'),
            $descontoPorc = $('#descontoPorc'),
            $totalProdutos = $('#totalprodutos'),
            $totalCusto = $('#totalcustogeral'),
            $totalPagar = $('#totalpagar'),
            $produto = $('#produto'),
            $inputDesconto = $linhaProduto.find("[name='vivalordesconto[]']"),
            $porcentagemIPI = $linhaProduto.find("[name='porcentagemipi[]']").val(produto.vialiqipi || ''),
            $valorIPI = $linhaProduto.find("[name='valoripi[]']").val(produto.vivalipi || ''),
            $valorDescontoModal = $linhaProduto.find('[name="valorDescR[]"]').val(produto.vivalordesconto || '');
            
        if ($codigo.val()) {
            $valorUnitario.attr('disabled', false);
            $valorCusto.attr('disabled', false);
            $quantidade.attr('disabled', false);
        } else {
            $valorUnitario.attr('disabled', true);
            $valorCusto.attr('disabled', true);
            $quantidade.attr('disabled', true);
        }

        setupButtonRemove($btnRemove);
        setupMascaraValorDinheiro($total);
        setupMascaraValorDinheiro($totalcusto);
        setupMascaraValorDinheiro4($valorUnitario);
        setupMascaraValorDinheiro($valorCusto);
        setupMascaraValorQuantidade4($quantidade);

        //luis 13/11/2014
        setupMascaraValorDinheiro($desconto);
        setupMascaraValorDinheiro($descontoPorc);
        setupMascaraValorDinheiro($totalProdutos);
        setupMascaraValorDinheiro($totalCusto);
        setupMascaraValorDinheiro($totalPagar);
        setupAutoCompleteProduto($nome, $load, $label, $completeNome, $codigo, $valorUnitario, $valorCusto, $quantidade, $total, $totalcusto, $sucess, $produto, $confirma, $porcentagemIPI);
        setupCalculoTotal($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto, $inputDesconto);           
        setFuncoesDescontoLinha($linhaProduto);    
        
        return $linhaProduto;
    }
    
    function setFuncoesDescontoLinha($linhaProduto) {
        liberaClickDesconto($linhaProduto);
        onKeyUpCamposModal($linhaProduto);
        onClickAplicarDescontoModal($linhaProduto);        
    }
    
    function onClickAplicarDescontoModal($linhaProduto) {
        var $modal = $linhaProduto.find("[name='modal[]']");
        var $inputDesconto = $linhaProduto.find("[name='vivalordesconto[]']");
        var $total = $linhaProduto.find('[name="total[]"]');
        var $totalcusto = $linhaProduto.find('[name="totalcusto[]"]');
        var $valorUnitario = $linhaProduto.find('[name="vivalorunitario[]"]');
        var $valorCusto = $linhaProduto.find('[name="vivalorcusto[]"]');
        var $quantidade = $linhaProduto.find('[name="viquantidade[]"]');
        
        $modal.find("[name='aplicarDesconto[]']").click(function(e) {
            e.preventDefault();
            //Atualização dos dados da linha do grid
            var $fValorDesconto = parseValorDinheiro($modal.find('[name="valorDescR[]"]').val());
            var $sValorDesconto = formatValorDinheiro($fValorDesconto);
            $inputDesconto.val($sValorDesconto);            

            //Atualização dos dados da tela geral            
            calculaTotalProduto($quantidade, $valorUnitario, $valorCusto, $total, $totalcusto, $inputDesconto);
            desabilitaDescontoGeral();
        }); 
    }
    
    function liberaClickDesconto($linhaProduto) {
        var $botaoDesconto = $linhaProduto.find("[name='botaodesconto[]']");
        var $modal = $linhaProduto.find("[name='modal[]']");
        var $valorUnitario = $linhaProduto.find('[name="vivalorunitario[]"]');
        var $quantidade = $linhaProduto.find('[name="viquantidade[]"]');
        //Apenas funcionar o botão de desconto caso o produto ja foi selecionado               
        $botaoDesconto.click(function() {
            var $total = $linhaProduto.find('[name="total[]"]');
            var $desconto = $('#desconto');
            if(parseValorDinheiro($total.val()) > 0 && ($desconto.is('[readonly]') || parseValorDinheiro($desconto.val()) == 0)) {
                var fValorUnit = parseValorDinheiro($valorUnitario.val());
                var fQtd = parseValorDinheiro($quantidade.val());
                var ftotal = fValorUnit*fQtd;
                var $total = formatValorDinheiro(ftotal);
                $modal.openModal();
                $modal.find('[name="totPedDesc[]"]').text($total);
                $modal.find('[name="valorDescPorc[]"]').focus();
                var fValorDesconto = parseValorDinheiro($modal.find('[name="valorDescR[]"]').val());
                if(fValorDesconto > 0) {
                    var novoValorDesconto = fValorDesconto/ftotal*100;
                    $modal.find('[name="valorDescPorc[]"]').val(formatValorDinheiro(novoValorDesconto));
                }
            }                               
        });
    }
    
    /*
     * Funções onKeyUp dos Campos do Modal de Desconto
     * @param {type} $linhaProduto
     */
    function onKeyUpCamposModal($linhaProduto) {
        var $ValorDesconto = $linhaProduto.find('[name="valorDescR[]"]');
        var $PorcentagemDesconto = $linhaProduto.find('[name="valorDescPorc[]"]');
        var $ValorUnitario = $linhaProduto.find('[name="vivalorunitario[]"]');
        var $Quantidade = $linhaProduto.find('[name="viquantidade[]"]');
        
        $ValorDesconto.on('keyup', function () {
            var fTotalPedido = parseValorDinheiro($ValorUnitario.val())*parseValorDinheiro($Quantidade.val());
            var fValorDesconto = parseValorDinheiro($ValorDesconto.val());
            var fPorcentagemFinal = fValorDesconto/fTotalPedido*100;
            
            $PorcentagemDesconto.val(formatValorDinheiro(fPorcentagemFinal.toFixed(2)));
            if (fPorcentagemFinal > 100) {
                $linhaProduto.find("[name='aplicarDesconto[]']").addClass("disabled");
                $linhaProduto.find("[name='msgErroDesc[]']").show();
            } 
            else {
                $linhaProduto.find("[name='aplicarDesconto[]']").removeClass("disabled");
                $linhaProduto.find("[name='msgErroDesc[]']").hide();
            }
        });
        
        $PorcentagemDesconto.on('keyup', function () {
            var fTotalPedido = parseValorDinheiro($ValorUnitario.val())*parseValorDinheiro($Quantidade.val());
            var fPorcentagemDesconto = parseValorDinheiro($PorcentagemDesconto.val());
            var fValorFinal = fPorcentagemDesconto*fTotalPedido/100;
            
            $ValorDesconto.val(formatValorDinheiro(fValorFinal));

            if (parseValorDinheiro($PorcentagemDesconto.val()) > 100) {
                $linhaProduto.find("[name='aplicarDesconto[]']").addClass("disabled");
                $linhaProduto.find("[name='msgErroDesc[]']").show();
            } else {
                $linhaProduto.find("[name='aplicarDesconto[]']").removeClass("disabled");
                $linhaProduto.find("[name='msgErroDesc[]']").hide();
            }
        });
    }
    
    /**
     * Desabilita/Habilita o Campo de Desconto Geral conforme o valor dos campos de Desconto por Item
     */
    function desabilitaDescontoGeral() {
        var $descontoTotal = $("#desconto");
        var $descontoTotalPorc = $("#descontoPorc");        
        $descontoTotal.attr('readonly', false);
        $descontoTotalPorc.attr('readonly', false);
        
        var $descontos = $("[name='vivalordesconto[]']");
        $.each($descontos, function(index, value) {
            var valor = parseValorDinheiro(value.value);
            if(valor != 0 || valor != "") {
                $descontoTotal.attr('readonly', true);
                $descontoTotalPorc.attr('readonly', true);
            }
        });
    }

    $(function () {
        var produtos = <?php echo json_encode((isset($DADOS) ? $DADOS->produtos : [])) ?>,
            $gridProdutos = $('#gridProdutos'),
            $btnAddProduto = $("#btnAddProduto"),
            $totalProdutos = $('#totalprodutos'),
            $totalCusto = $('#totalcustogeral'),
            $desconto = $("#desconto"),
            $descontoPorc = $("#descontoPorc"),
            $vvaloracrescimo = $("#vvaloracrescimo"),
            $frete = $("#vnfvalorfrete");

        $btnAddProduto.click(function () {
            var $linhaProduto = novaLinhaProduto();
            $gridProdutos.append($linhaProduto);
            $linhaProduto.find('[name="vinome[]"]').focus();
        });       

        $desconto.blur(onBlurDesconto);
        $descontoPorc.blur(onBlurDesconto);
        $frete.blur(calculaTotalPagar);
        $vvaloracrescimo.blur(calculaTotalPagar);

        setupMascaraValorDinheiro($frete);
        setupMascaraValorDinheiro($vvaloracrescimo);
        for (var i in produtos) {
            var $linhaProduto = novaLinhaProduto(produtos[i]);
            $gridProdutos.append($linhaProduto);
        }

        if (produtos.length === 0) {
            var $linhaProduto = novaLinhaProduto();
            $gridProdutos.append($linhaProduto);
        }        
    });

    function novaLinhaParcela(parcela) {
        parcela = parcela || {};
        var $linha =
                $('<div class="linhaParcela">' +
                        '<div class="col-md-1" style="padding-left:0px;margin-right:5px;width:105px">' +
                        '<label>Data Vencimento</label>' +
                        '<input type="text" placeholder="dd/mm/aaaa" autocomplete="off" class="form-control" id="" name="dataparcela[]"/>' +
                        '</div>' +
                        '<div class="col-md-1" style="padding-left:0px;margin-right:5px;width:50px">' +
                        '<label>Dias</label>' +
                        '<input type="text" autocomplete="off" class="form-control" id="" name="diasparcela[]"/>' +
                        '</div>' +
                        '<div class="col-md-1" style="padding-left: 0px;margin-right:5px;width:110px">' +
                        '<label>Valor da Parcela</label>' +
                        '<input type="text" placeholder="R$" class="form-control" name="valorparcela[]">' +
                        '</div>' +
                        '<div class="col-md-1" id="divClassFin" style="width:115px;padding-left: 0px;margin-right:5px">' +
                        '<div id="msgSucessoFor" style="display: none;">' +
                        '<div style="float:right">' +
                        '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" style="top: 0px;">' +
                        '</span>' +
                        '<span id="inputSuccess2Status" class="sr-only" style="top: 0px;">(success)</span>' +
                        '</div>' +
                        '<div class="form-control-feedback" style="width: 200px;top: 0px;">Inserido com sucesso!</div>' +
                        '</div>' +
                        '<label>CF*</label>' +
                        '<input type="text" placeholder="" class="form-control" name="classfinP[]" data-required="required">' +
                        '<div id="labelAddCF" class="labelComplete" style="display: none;">' +
                        '<span class="pull-left">Adicionar</span>' +
                        '<span id="textCompCF" class="text-complete"></span>' +
                        '<button type="button" name="confirma" id="confirmaCF" class="btn-add">' +
                        '<i class="mdi-content-add-circle-outline" style="font-size: 21px;color: #fff;padding-right: 8px;"></i>' +
                        '</button>' +
                        '</div>' +
                        '<div class="fa fa-refresh fa-spin autocompleteLoading" id="CFload" style="display:none;"></div>' +
                        '<input type="text" placeholder="" class="form-control" name="classfinIDP[]" style="display: none;">' +
                        '</div>' +
                        '<div class="col-md-1" style="padding-left: 0px;margin-right:5px">' +
                        '<label>Custo</label>' +
                        '<input type="text" placeholder="" class="form-control" name="valorcusto[]">' +
                        '</div>' +
                        '<div class="col-md-1" style="width:115px;padding-left: 0px;margin-right:5px">' +
                        '<label>Centro de Custo</label>' +
                        '<input type="text" id="centrocustoId" style="display:none" style="margin-bottom: 10px;" autocomplete="off" class="form-control span4" name="centrocustocod[]" value=""/>' +
                        '<input id="centrocusto" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="centrocusto[]"  value=""/>' +
                        '<div class="fa fa-refresh fa-spin autocompleteLoading" id="loadcc" style="display: none;"></div>' +
                        '<div id="cclabel" class="labelComplete-field" style="display: none;"></div>' +
                        '</div>' +
                        '<div class="col-md-1" style="width:115px;padding-left: 0px;margin-right:5px">' +
                        '<label>Espécie</label>' +
                        '<input type="text" id="especieId" style="display:none;margin-bottom: 10px" class="form-control" name="especiecod[]"/>' +
                        '<input id="especie" type="text" style="margin-bottom: 10px;" autocomplete="off" class="form-control" name="especie[]"/>' +
                        '<div class="fa fa-refresh fa-spin autocompleteLoading" id="espload" style="display: none;"></div>' +
                        '<div id="labelEsp" class="labelComplete-field" style="display: none;">' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-1" style="width: 120px;padding-left:0px;margin-right:5px">' +
                        '<label>Observação</label>' +
                        '<input type="text" placeholder="" class="form-control" name="obsparcela[]">' +
                        '</div>' +
                        '<div>' +
                        '<button type="button" class="removerParcela btn btn-default" title="Remover linha">' +
                        '<i class="mdi-action-delete"></i>' +
                        '</a>' +
                        '</div>' +
                        '</div>');
                
        var $data = $("#cbdatasaldo");
        if(parcela.vfdias > 0) {
            var $dataFormatada = $data.val() ? moment(inverteData("/", $data.val())) : moment();
            parcela.vfdatavencimento = $dataFormatada.add(parcela.vfdias, 'days').format('DD/MM/YYYY');
        }
        
        var $btnRemoveParcela = $linha.find(".removerParcela"),
            $btnAddParcela = $("#btnAddParcela"),
            $diasparcela = $linha.find('[name="diasparcela[]"]').val(parcela.vfdias || ''),
            $dataparcela = $linha.find('[name="dataparcela[]"]').val(parcela.vfdatavencimento || ''),
            $valorparcela = $linha.find('[name="valorparcela[]"]').val(parcela.vfvalor || ''),
            $valorcusto = $linha.find('[name="valorcusto[]"]').val(parcela.vfcusto || ''),
            $obsparcela = $linha.find('[name="obsparcela[]"]').val(parcela.vfobs);
        var $ClassiFin = $('#ClassiFin').val();
        var $ClassiFinId = $('#ClassiFinId').val();
        var $espnome = $('#esp').val();
        var $espcod = $('#espId').val();
        var $ccnome = $('#cc').val();
        var $cccod = $('#ccId').val();

        if (espcodto.length < 0) {
            espcodto = "";
        }
        var $cfnome = $linha.find('[name="classfinP[]"]').val(parcela.cfnome || $ClassiFin),
                $cfcodigo = $linha.find('[name="classfinIDP[]"]').val(parcela.vfclassifin || $ClassiFinId),
                $cfload = $linha.find('[id="CFload"]'),
                $cflabel = $linha.find('[id="labelAddCF"]'),
                $cfsucesso = $linha.find('[id="msgSucessoCF"]'),
                $cfconfirma = $linha.find('[id="confirmaCF"]'),
                $cftextonome = $linha.find('[id="textCompCF"]'),
                especie = $linha.find('[id="especie"]').val(parcela.espnome || $espnome || espnometo || ""),
                especieId = $linha.find('[id="especieId"]').val(parcela.espcod || $espcod || espcodto || ""),
                especieLoad = $linha.find('[id="espload"]'),
                especieLabel = $linha.find('[id="labelEsp"]'),
                centrocusto = $linha.find('[id="centrocusto"]').val(parcela.ccnome || $ccnome || ""),
                centrocustoId = $linha.find('[id="centrocustoId"]').val(parcela.cccod || $cccod || ""),
                centrocustoLoad = $linha.find('[id="loadcc"]'),
                centrocustoLabel = $linha.find('[id="cclabel"]');

        setupAutoCompleteClassFin($cfnome, $cfload, $cflabel, $cftextonome, $cfcodigo, $cfsucesso, $cfconfirma);

        setupButtonRemoveParcela($btnRemoveParcela);
        setupMascaraValorDinheiro($valorparcela);
        setupMascaraData($dataparcela);
        setupMascaraValorDinheiro($valorcusto);
        setupAutoCompleteEspecie(especie, especieId, especieLoad, especieLabel);
        setupAutoCompleteCentroCusto(centrocusto, centrocustoId, centrocustoLoad, centrocustoLabel);
        return $linha;
    }
    
    function calculaParcelamento() {
        var $gridParcelas = $('#gridParcelas'),
                $campoQuantidade = $("#parcelamento"),
                parcelamento = $campoQuantidade.val().toLowerCase(),
                $qtdParcelas = 0;
        if (parcelamento) {
            $gridParcelas.find('.linhaParcela').remove();
            parcelas = parcelamento.split(" ");
            if (parcelamento.indexOf("x") > 0) {
                parcelas.map(function (dado) {
                    if (parseInt(dado) === 0) {
                        var data = $("#cbdatasaldo").val() ? moment(inverteData("/", $("#cbdatasaldo").val())) : moment();
                        $qtdParcelas++;
                        $linha = novaLinhaParcela();
                        $linha.find('[name="dataparcela[]"]').datepicker("setDate", data.format('DD/MM/YYYY'));
                        $gridParcelas.append($linha);
                    }
                    for ($i = 0; $i < parseInt(dado); $i++) {
                        var data = $("#cbdatasaldo").val() ? moment(inverteData("/", $("#cbdatasaldo").val())) : moment();
                        $qtdParcelas++;
                        var oneDay = 24*60*60*1000;
                        if ($gridParcelas.find('[name="dataparcela[]"]').last().length) {
                            $linha = novaLinhaParcela();
                            var $dataAntigaMoment = data;
                            var oneDay = 24*60*60*1000;
                            var firstDate = new Date(data);
                            var $dataParcela = $linha.find('[name="dataparcela[]"]');
                            $dataParcela.datepicker("setDate", $dataAntigaMoment.add($i+1, 'month').format('DD/MM/YYYY'));
                            var secondDate =  new Date(inverteData("/", $dataParcela.val()));

                            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
                            $linha.find('[name="diasparcela[]"]').val(diffDays);
                            
                            $gridParcelas.append($linha);
                        } else {
                            $linha = novaLinhaParcela();
                            var $dataParcela = $linha.find('[name="dataparcela[]"]');
                            var novadata = data;
                            var firstDate = new Date(novadata);
                            $dataParcela.datepicker("setDate", novadata.add('1', 'month').format('DD/MM/YYYY'));
                            var secondDate =  new Date(inverteData("/", $dataParcela.val()));
                            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
                            $linha.find('[name="diasparcela[]"]').val(diffDays);
                            $gridParcelas.append($linha);
                        }
                    }
                });
            } else {
                parcelas.map(function (dado) {
                    var data = $("#cbdatasaldo").val() ? moment(inverteData("/", $("#cbdatasaldo").val())) : moment();
                    $qtdParcelas++;
                    $linha = novaLinhaParcela();
                    $linha.find('[name="dataparcela[]"]').datepicker("setDate", data.add(dado, 'days').format('DD/MM/YYYY'));
                    $linha.find('[name="diasparcela[]"]').val(dado);
                    $gridParcelas.append($linha);
                });
            }
            geraValores($qtdParcelas, $gridParcelas);
            geraValoresCusto($qtdParcelas, $gridParcelas);
        }
    }

    function setupAutoCompleteEspecie(especie, especieId, load, label) {
        //Autocomplete espécie
        $(especie).autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'ESP'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $(especie).removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $(especieId).val('');
                                $(especie).addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                $(especieId).val(ui.item.value);
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $(especie).val(ui.item.label);
                    $(especieId).val(ui.item.value);
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $(load).hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $(load).show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $(label).text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $(load).show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $(especieId).val('');
                $(load).show();
            },
            open: function (event, ui) {
                $(load).hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };
    }

    function setupAutoCompleteCentroCusto(cc, ccId, loadcc, cclabel) {
        $(cc).autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteSQL.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'CC'
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $(cc).removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $(ccId).val('');
                                $(cc).addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            select: function (event, ui) {
                event.stopPropagation();
                if (ui.item.label !== '') {
                    $(cc).val(ui.item.label);
                    $(ccId).val(ui.item.value);
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $(loadcc).hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $(cclabel).text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
//                        $('#loadesp').show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $(ccId).val('');
                $(loadcc).show();
            },
            open: function (event, ui) {
                $(loadcc).hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };
    }


    $(function () {
        var parcelas = <?php echo json_encode((isset($DADOS->financeiro) ? $DADOS->financeiro : [])) ?>,
                $gridParcelas = $('#gridParcelas'),
                $btnAddParcela = $("#btnAddParcela"),
                $btnGerarParcelas = $("#btnGerarParcelas");

        $btnAddParcela.click(function () {
            var $linhaParcela = novaLinhaParcela();
            $gridParcelas.append($linhaParcela);
        });

        for (var i in parcelas) {
            var $linhaParcela = novaLinhaParcela(parcelas[i]);
            $gridParcelas.append($linhaParcela);
        }

        var obsempresa = $("#vobsempresa").val();
        if (obsempresa !== '')
        {
            $("#BLOCO").show();
            $("#mostrar").hide();
            $("#ocultar").show();
        }

        $("#parcelamento").blur(function () {
            calculaParcelamento();
        });

        $btnGerarParcelas.click(function () {
            calculaParcelamento();
        });
        
        
    });

    function setupAutoCompleteClassFin($cfnome, $cfload, $cflabel, $cftextonome, $cfcodigo, $cfsucesso, $cfconfirma) {
        $cfnome.autocomplete({
            source: function (request, response) {
                jQuery.ajax({
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/venda/autoCompleteAux.php",
                    dataType: "json",
                    cache: false,
                    data: {
                        str: request.term,
                        op: 'CFE',
                        to: $("#tipooperacaoid").val()
                    },
                    success: function (data) {
                        response($.map(data, function (item) {

                            if (item.total > 0) {
                                $cflabel.hide();
                                $cfnome.removeClass("adicionarRegistro");
                            }
                            if (item.total < 1) {
                                $cfcodigo.val('');
                                $cflabel.show();
                                $cfnome.addClass("adicionarRegistro");
                            }
                            return {
                                label: item.label,
                                value: item.value,
                                vunitario: item.vunitario
                            };
                        }));
                    }
                });
            },
            minLength: 0,
            autoFocus: true,
            focus: function (event, ui) {
                if (!$cflabel.is(':visible')) {
                    $cfcodigo.val(ui.item.value);
                }
                return false;
            },
            select: function (event, ui) {
                if (ui.item.label !== '') {
                    $cfnome.val(ui.item.label);
                    $cfcodigo.val(ui.item.value);
                    $cflabel.hide();
                }
                return false;
            },
            response: function (event, ui) {
                if (usuario === 'S') {
                    if (ui.content.length === 1) {
                        $cfload.hide();
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $cfload.show();
                        existe = 'S';
                    }
                } else if (usuario === 'N') {
                    if (ui.content.length === 1) {
                        $cflabel.text('Registro não encontrado.');
                        existe = 'N';
                    } else if (ui.content.length > 1) {
                        $cfload.show();
                        existe = 'S';
                    }
                }
            },
            search: function (event, ui) {
                $cfcodigo.val('');
                $cfload.show();
            },
            open: function (event, ui) {
                $cfload.hide();
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                    .data("item.autocomplete", item)
                    .append("<a>" + item.label + "</a>")
                    .appendTo(ul);
        };

        $cfnome.on('keydown', function (e) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key === 9 && existe === 'N' && usuario === 'S') {
                var produto = $cfnome.val();
                if (produto.length > 1) {
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo $URL_BASE_PATH ?>/modulos/compra/cadastraAutoComplete.php",
                        data: {
                            dados: produto,
                            op: 'CF'
                        },
                        success: function (resposta) {
                            $cfcodigo.val(resposta);
                            $cfnome.removeClass("adicionarRegistro");
                            $cflabel.hide(0);
                            $cfload.hide();
                            $cfsucesso.show("fast");
                            setTimeout(function () {
                                $cfsucesso.hide();
//                                $produto.removeClass("has-success");
                            }, 3000);
                        }
                    });
                }
            }

        });
        $cfnome.on('keyup', function () {
            var dado = $cfnome.val();
            $cftextonome.text(dado);

            if (dado.length < 2) {
                $cflabel.hide();
                $cfcodigo.val('');
                $cfnome.removeClass("adicionarRegistro");
            }

        });
        $cfnome.focus(function () {
            $cfnome.autocomplete("search", $cfnome.val());
        });
        $cfconfirma.on('click', function () {
            var dados = $cfnome.val();
            if (dados.length > 1) {
                jQuery.ajax({
                    type: "POST",
                    minLength: 1,
                    url: "<?php echo $URL_BASE_PATH ?>/modulos/compra/cadastraAutoComplete.php",
                    data: {
                        dados: dados,
                        op: 'CF'
                    },
                    success: function (resposta) {
                        $cfcodigo.val(resposta);
                        $cfnome.removeClass("adicionarRegistro");
                        $cflabel.hide(0);
                        $cfload.hide();
                        $cfsucesso.show("fast");
//                        $produto.addClass("has-success");
                        setTimeout(function () {
                            $cfsucesso.hide();
//                            $produto.removeClass("has-success");
                        }, 3000);
                    }
                });
            }
        });
    }
    
    $(function () {   
        $('.ui-autocomplete').css('max-width', '875px');
        var $desconto = $('#desconto');
        var $descontoPorc = $('#descontoPorc');
        if(parseValorDinheiro($desconto.val()) > 0) {
            $desconto.attr('readonly', true);
            $descontoPorc.attr('readonly', true);
        }
        onKeyUpCamposDescontoTotal();     
    });
    
    /*
     * Funções onKeyUp dos Campos de Desconto
     */
    function onKeyUpCamposDescontoTotal() {
        var $ValorDesconto = $("#desconto");
        var $PorcentagemDesconto = $("#descontoPorc");
        var $total = $("#totalprodutos");         
        
        $ValorDesconto.on('keyup', function () {
            var ftotal = parseValorDinheiro($total.val());            
            if(ftotal > 0) {
                var fValorDesconto = parseValorDinheiro($ValorDesconto.val());
                var fPorcentagemFinal = fValorDesconto/ftotal*100;
                $PorcentagemDesconto.val(formatValorDinheiro(fPorcentagemFinal));
            }
            
        });
        
        $PorcentagemDesconto.on('keyup', function () {
            var ftotal = parseValorDinheiro($total.val());
            if(ftotal > 0) {
                var fPorcentagemDesconto = parseValorDinheiro($PorcentagemDesconto.val());
                var fValorFinal = fPorcentagemDesconto*ftotal/100;            
                $ValorDesconto.val(formatValorDinheiro(fValorFinal));
            }            
        });
    }
    
    $(function () {
        let retorno = <?= $retornoIndustrializacao ?>;
        if(retorno == true) {
            $('#pessoa').attr('readonly', true);
            $('[name="vinome[]"]').each(function() {
                $(this).attr('readonly', true);
            });
            calculaTotalPagar();
            $('#tipooperacao').focus();
        }
        else {
            $('#pessoa').focus();
        }
    });
    

</script>