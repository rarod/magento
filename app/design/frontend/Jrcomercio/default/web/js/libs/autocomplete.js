require(["jquery", "mask", "maskFields"], function ($) {
    // CEP - Checkout e Pagina do Usuario
    jQuery(document).ready(function ($) {
        var estadosarray = [];
        estadosarray[485] = "AC";
        estadosarray[486] = "AL";
        estadosarray[487] = "AP";
        estadosarray[488] = "AM";
        estadosarray[489] = "BA";
        estadosarray[490] = "CE";
        estadosarray[491] = "ES";
        estadosarray[492] = "GO";
        estadosarray[493] = "MA";
        estadosarray[494] = "MT";
        estadosarray[495] = "MS";
        estadosarray[496] = "MG";
        estadosarray[497] = "PA";
        estadosarray[498] = "PB";
        estadosarray[499] = "PR";
        estadosarray[500] = "PE";
        estadosarray[501] = "PI";
        estadosarray[502] = "RJ";
        estadosarray[503] = "RN";
        estadosarray[504] = "RS";
        estadosarray[505] = "RO";
        estadosarray[506] = "RR";
        estadosarray[507] = "SC";
        estadosarray[508] = "SP";
        estadosarray[509] = "SE";
        estadosarray[510] = "TO";
        estadosarray[511] = "DF";
        var validacep = /^[0-9]{8}$/;

        function limpa_formulário_cep() {
            // Limpa valores do formulário de cep.
            $("#zip").val("");
            $("#street_1").val(""); // endereço
            $("#street_2").val(""); // bairro
            $("#uf").val("");
            $("#ibge").val("");
            $(".modal-content input[name='postcode']").val(""); // CEP
            $(".modal-content input[name='street[0]']").val(""); // Endereço
            $(".modal-content input[name='street[1]']").val(""); // Bairro
            $(".modal-content select[name='region_id']").val(""); // UF
        }
        //Quando o campo cep perde o foco.
        $("#zip").blur(function () {
            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, "");

            //Verifica se campo cep possui valor informado.
            if (cep != "") {
                //Valida o formato do CEP.
                if (validacep.test(cep)) {
                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#street_1").val("...");
                    $("#street_4").val("...");
                    $("#city").val("...");
                    let estado = $("#region_id").val();

                    //Consulta o webservice viacep.com.br/
                    $.getJSON(
                        "https://viacep.com.br/ws/" + cep + "/json/?callback=?",
                        function (dados) {
                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#street_1").val(dados.logradouro);
                                $("#street_4").val(dados.bairro);
                                $("#city").val(dados.localidade);
                                var estadoAtivo = dados.uf;
                                $("#region_id option").each(function () {
                                    if (
                                        estadosarray[jQuery(this).val()] ==
                                        estadoAtivo
                                    ) {
                                        jQuery("#region_id").val(
                                            jQuery(this).val()
                                        );
                                    }
                                });
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        }
                    );
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        });
        // checkout Validação

        $(document).on(
            "blur",
            "#shipping-new-address-form [name='postcode']",
            function () {
                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, "");

                // Seta Pais para Brasil
                $("select[name='country_id']").val("BR").trigger("change");

                //Verifica se campo cep possui valor informado.
                if (cep != "") {
                    //Valida o formato do CEP.
                    if (validacep.test(cep)) {
                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("[name='street[0]']").val("...");
                        $("[name='street[3]']").val("...");
                        $("[name='city']").val("...");
                        let estado = $("select[name='region_id']").val();

                        //Consulta o webservice viacep.com.br/
                        jQuery.getJSON(
                            "https://viacep.com.br/ws/" +
                                cep +
                                "/json/?callback=?",
                            function (dados) {
                                if (!("erro" in dados)) {
                                    //Atualiza os campos com os valores da consulta.
                                    $("[name='street[0]']")
                                        .val(dados.logradouro)
                                        .trigger("change");
                                    $("[name='street[3]']")
                                        .val(dados.bairro)
                                        .trigger("change");
                                    $("[name='city']")
                                        .val(dados.localidade)
                                        .trigger("change");
                                    var estadoAtivo = dados.uf;
                                    $("select[name='region_id'] option").each(
                                        function () {
                                            if (
                                                estadosarray[
                                                    jQuery(this).val()
                                                ] == estadoAtivo
                                            ) {
                                                $("select[name='region_id']")
                                                    .val($(this).val())
                                                    .trigger("change");
                                            }
                                        }
                                    );
                                } //end if.
                                else {
                                    //CEP pesquisado não foi encontrado.
                                    limpa_formulário_cep();
                                    alert("CEP não encontrado.");
                                }
                            }
                        );
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            }
        );

        $(document).on(
            "blur",
            "#billing-new-address-form [name='postcode']",
            function () {
                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, "");
                //Verifica se campo cep possui valor informado.
                if (cep != "") {
                    //Valida o formato do CEP.
                    if (validacep.test(cep)) {
                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("[name='street[0]']").val("...");
                        $("[name='street[3]']").val("...");
                        $("[name='city']").val("...");
                        let estado = $("select[name='region_id']").val();

                        //Consulta o webservice viacep.com.br/
                        jQuery.getJSON(
                            "https://viacep.com.br/ws/" +
                                cep +
                                "/json/?callback=?",
                            function (dados) {
                                if (!("erro" in dados)) {
                                    //Atualiza os campos com os valores da consulta.
                                    $("[name='street[0]']")
                                        .val(dados.logradouro)
                                        .trigger("change");
                                    $("[name='street[3]']")
                                        .val(dados.bairro)
                                        .trigger("change");
                                    $("[name='city']")
                                        .val(dados.localidade)
                                        .trigger("change");
                                    var estadoAtivo = dados.uf;
                                    $("select[name='region_id'] option").each(
                                        function () {
                                            if (
                                                estadosarray[
                                                    jQuery(this).val()
                                                ] == estadoAtivo
                                            ) {
                                                $("select[name='region_id']")
                                                    .val($(this).val())
                                                    .trigger("change");
                                            }
                                        }
                                    );
                                } //end if.
                                else {
                                    //CEP pesquisado não foi encontrado.
                                    limpa_formulário_cep();
                                    alert("CEP não encontrado.");
                                }
                            }
                        );
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            }
        );
    });
});
