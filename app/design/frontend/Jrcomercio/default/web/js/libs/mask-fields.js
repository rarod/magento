require(["jquery", "mask"], function ($, _) {
    jQuery(document).ready(function ($) {
        jQuery("#dob").mask("99/99/9999");
        jQuery("#zip").mask("99999-999");
        jQuery("[name=postcode]").mask("99999-999");
        jQuery("[name='street[1]']").mask("99999999");

        const SPMaskBehavior = function (val) {
                return val.replace(/\D/g, "").length === 11
                    ? "(00)00000-0000"
                    : "(00)00000-0009";
            },
            spOptions = {
                onKeyPress: function (val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                },
            };
        jQuery("#telephone").mask(SPMaskBehavior, spOptions);
        jQuery("[name=telephone]").mask(SPMaskBehavior, {
            clearIfNotMatch: true,
        });
        jQuery("[name=fax]").mask(SPMaskBehavior, {
            clearIfNotMatch: true,
        });

        $("#taxvat").on("keydown", function (e) {
            try {
                $("#taxvat").unmask();
            } catch (e) {}

            var code = e.keyCode || e.which;

            const tamanho = $("#taxvat").val().length;
            var verifyNumber = 11;

            if (code === 9) {
                verifyNumber = 12;
            }

            if (tamanho < verifyNumber) {
                $("#taxvat").mask("999.999.999-99");
            } else {
                $("#taxvat").mask("99.999.999/9999-99");
            }

            // ajustando foco
            const elem = this;
            setTimeout(function () {
                // mudo a posição do seletor
                elem.selectionStart = elem.selectionEnd = 10000;
            }, 0);
            // reaplico o valor para mudar o foco
            const currentValue = $(this).val();
            $(this).val("");
            $(this).val(currentValue);
        });
    });
});
