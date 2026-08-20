!(function($) {
    "use strict";

    // products slider
    $(".ic-best-products-items").slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        arrows: false,
        vertical: true,
        verticalSwiping: true
    });

    // Input radio
    $("#bank-info").hide();
    $('input:radio[name="payment_type"]').on("change", function() {
        if ($(this).val() == "bank") {
            $("#bank-info").show();
        } else {
            $("#bank-info").hide();
        }
    });
    // roll add checkbox
    $("#customCheck-all").on("click", function() {
        $("input:checkbox")
            .not(this)
            .prop("checked", this.checked);
        $("div .ic_div-show").toggle();
    });

    // role permission
    $(".ic-parent-permission").on("click", function() {
        let parent_id = $(this).val();
        $("#ic_parent-" + parent_id).toggle();

        if ($(`#customCheck-${parent_id}`).is(":checked")) {
            $(`.parent-identy-${parent_id}`).each(function() {
                $(this).prop("checked", true);
            });
        } else {
            $(`.parent-identy-${parent_id}`).each(function() {
                $(this).prop("checked", false);
            });
        }
    });

    // input felid
    if ($(".phone").length > 0) {
        // Bangladesh is the default country on every phone field. A field that
        // already holds a number keeps that number's own country code.
        $(".phone").intlTelInput({
            initialCountry: "bd",
            preferredCountries: ["bd"],
        });
    }

    // DataTable wrap
    $(document).ready(function() {
        $(".dataTable").wrap("<div class='table-responsive'></div>");
    });
    // select 2
    if ($(".select2").length > 0) {

        $(".select2").select2();
    }
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    // DatePicker js
    if ($(".datepicker-autoclose").length > 0) {
        $(".datepicker-autoclose").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
    }
    // datepicker conditional hide
    $(document).ready(function() {
        $("body").on("focus", "#from_date", function() {
            $(this)
                .datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: "yyyy-mm-dd"
                })
                .on("changeDate", function(e) {
                    $("#to_date").datepicker("remove");
                    $("#to_date").datepicker({
                        autoclose: true,
                        todayHighlight: true,
                        format: "yyyy-mm-dd",
                        startDate: e.date
                    });
                });
        });
    });

    // Hide notification message
    // $("div.alert").delay(5000).fadeOut(350);
    $("div.alert")
        // .not(".alert-important")
        .delay(5000)
        .fadeOut(350);

    //  barcode
    function processBarcode() {
        JsBarcode("#b-image-show", $("#barcode").val());
        $("#barcode-value").val($("#b-image-show").attr("src"));
    }
    processBarcode();
    $("#barcode").on("keyup", function() {
        processBarcode();
    });
    $('input:radio[name="tax_status"]').on("change", function() {
        if ($(this).val() == "included") {
            $("#custom-tax").show();
        } else {
            $("#custom-tax").hide();
        }
    });

    // purchase calculateSubtotal
    $(".ic-calculate-input").on("keyup", function() {
        let id = $(this).attr("rel");
        let order_quantity = $("#order_quantity_" + id).text();
        let receive_quantity = $("#receive_quantity_" + id).val();
        let available_qty = $("#available_qty_" + id).val();

        if (parseInt(receive_quantity) > parseInt(available_qty)) {
            $("#receive_quantity_" + id).val(available_qty);
            let receive_price = $("#receive_price_" + id).val();
            let sub_total = available_qty * receive_price;
            $("#receive_sub_total_" + id).val(sub_total.toFixed(2));
        } else {
            let receive_price = $("#receive_price_" + id).val();
            let sub_total = receive_quantity * receive_price;
            $("#receive_sub_total_" + id).val(sub_total.toFixed(2));
        }

        let total = 0;
        $(".sub_total").each(function() {
            total += $(this).val() > 0 ? parseFloat($(this).val()) : 0;
        });
        $(".total").val(total.toFixed(2));
    });

    // purchase Return calculateSubtotal
    $(".ic-return-calculate-input").on("keyup", function() {
        let id = $(this).attr("rel");
        let return_quantity = $("#return_quantity_" + id).val();
        let return_price = $("#return_price_" + id).val();

        if (parseInt(return_quantity)) {
            let sub_total = return_quantity * return_price;
            $("#return_sub_total_" + id).val(sub_total.toFixed(2));
        }

        let total = 0;
        $(".sub_total").each(function() {
            total += $(this).val() > 0 ? parseFloat($(this).val()) : 0;
        });
        $(".total").val(total.toFixed(2));
    });

    $(".ic-sale-return-qty").on("keyup", function() {
        let id = $(this).attr("rel");
        let return_quantity = $("#return_qty_" + id).val();
        let available_qty = $("#available_qty_" + id).val();

        if (parseInt(return_quantity) > parseInt(available_qty)) {
            $("#return_qty_" + id).val(available_qty);
            let return_price = $("#return_price_" + id).val();
            let sub_total = available_qty * return_price;
            $("#return_subtotal_" + id).val(sub_total.toFixed(2));
        } else {
            let return_price = $("#return_price_" + id).val();
            let sub_total = return_quantity * return_price;
            $("#return_subtotal_" + id).val(sub_total.toFixed(2));
        }

        let total = 0;
        $(".sub_total").each(function() {
            total += $(this).val() > 0 ? parseFloat($(this).val()) : 0;
        });
        $(".total").val(total.toFixed(2));
    });

    // ── Weight-based (barrel) rows ────────────────────────────────
    // Used on Purchase Receive, Purchase Return and Sale Return. The user
    // types the amount in KG; we convert to barrels for the hidden submit
    // fields (quantity in barrels, price per barrel) exactly like Add Purchase.
    //   .ic-wb-kg    -> visible KG input   (data-row, data-kgpb, data-avail)
    //   .ic-wb-perkg -> visible per-kg price input (data-row)
    //   .ic-wb-qty   -> hidden barrel quantity (submitted)
    //   .ic-wb-price -> hidden per-barrel price (submitted)
    //   .ic-wb-sub   -> sub total field (also carries .sub_total for the grand total)
    //   .ic-wb-barrel-> <span> that shows the converted barrel count
    //   .ic-wb-extra-kg -> extra KG input (Sale Return damage / lost) with its own .ic-wb-extra-qty
    function icWbGrandTotal() {
        var total = 0;
        $(".sub_total").each(function() {
            var v = parseFloat($(this).val());
            total += v > 0 ? v : 0;
        });
        $(".total").val(total.toFixed(2));
    }

    function icWbRecalcRow(row) {
        var $kg = $('.ic-wb-kg[data-row="' + row + '"]');
        if (!$kg.length) return;

        var kgpb = parseFloat($kg.data("kgpb")) || 0;
        var kg   = parseFloat($kg.val()) || 0;

        // Clamp the good-return / receive amount to what is available.
        // "avail" is stored in barrels, so compare in kg.
        var avail = $kg.data("avail");
        if (avail !== undefined && avail !== "" && kgpb > 0) {
            var availKg = parseFloat(avail) * kgpb;
            if (kg > availKg) { kg = availKg; $kg.val(kg); }
        }

        var barrels   = kgpb > 0 ? kg / kgpb : 0;
        var perkg     = parseFloat($('.ic-wb-perkg[data-row="' + row + '"]').val()) || 0;
        var perBarrel = perkg * kgpb;
        var sub       = kg * perkg;

        $('.ic-wb-qty[data-row="' + row + '"]').val(barrels);
        $('.ic-wb-price[data-row="' + row + '"]').val(perBarrel ? perBarrel.toFixed(4) : perBarrel);
        $('.ic-wb-sub[data-row="' + row + '"]').val(sub.toFixed(2));
        $('.ic-wb-barrel[data-row="' + row + '"]').text(Math.round(barrels * 10000) / 10000);

        icWbGrandTotal();
    }

    // Extra KG inputs that only need kg -> barrel conversion (Sale Return damage / lost).
    function icWbConvertExtra($el) {
        var kgpb = parseFloat($el.data("kgpb")) || 0;
        var kg   = parseFloat($el.val()) || 0;
        var barrels = kgpb > 0 ? kg / kgpb : 0;
        var target = $el.data("qtytarget");
        if (target) $("#" + target).val(barrels);
        // Live barrel-equivalent display (matches Purchase Receive/Return).
        var span = $el.data("barrelspan");
        if (span) $("#" + span).text(Math.round(barrels * 10000) / 10000);
    }

    $(document).on("keyup change", ".ic-wb-kg, .ic-wb-perkg", function() {
        icWbRecalcRow($(this).data("row"));
    });
    $(document).on("keyup change", ".ic-wb-extra-kg", function() {
        icWbConvertExtra($(this));
    });

    // Initial conversion for any pre-filled weight-based rows.
    $(function() {
        $(".ic-wb-kg").each(function() { icWbRecalcRow($(this).data("row")); });
        $(".ic-wb-extra-kg").each(function() { icWbConvertExtra($(this)); });
    });

    // all anchor tag prevent Default
    $(".ic-javascriptVoid").on("click", function(e) {
        e.preventDefault();
    });

    function readURL(input, image_for) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_' + image_for).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".image_pick").on("change", function () {
        var image_for = $(this).attr('data-image-for');
        readURL(this, image_for);
    });
    
    // Only initialize summernote if the library is loaded
    if ($.fn.summernote) {
        $('.summernote').summernote({
            tabsize: 2,
            height: 100
        });
        $(document).on('submit','form',function (){
            $.each($(".summernote"),function(){
                if($(this).summernote("codeview.isActivated"))
                {
                    $(this).summernote("codeview.deactivate");
                }
            })
        });
    }
})(jQuery);
