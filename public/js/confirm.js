$(function(){
    $("#form").submit(function(){

        // this can be undefined, if they do not include minimum prices in their file (and the view checks the hasMinimumSellPrices boolean)
        if($("#filterPriceVariance").val() != 'undefined') {
            // trim
            $("#filterPriceVariance").val($.trim($("#filterPriceVariance").val()));

            // check for not a number
            if(isNaN($("#filterPriceVariance").val()) && $('#filterByMinimumPrice').is(':checked') == true) {
                $("span#filterPriceVarianceError").html('<br />&quot;' + $("#filterPriceVariance").val() + '&quot; is not a number. Please enter a valid price for the filter.');
                return false;
            }
            // check for empty string
            if($("#filterPriceVariance").val() == '' && $('#filterByMinimumPrice').is(':checked') == true) {
                $("span#filterPriceVarianceError").html('<br />Please enter a valid price for the filter.');
                return false;
            }
            // clear the error, if there is one:
            $("span#filterPriceVarianceError").html('');
            // parse the float... the isNaN check above should insure a good parse
            $("#filterPriceVariance").val(parseFloat($("#filterPriceVariance").val()).toFixed(2));
        }

        // this can be undefined, if they do not include minimum prices in their file (and the view checks the hasMinimumSellPrices boolean)
        if($("#filterPctVariance").val() != 'undefined') {
            // trim
            $("#filterPctVariance").val($.trim($("#filterPctVariance").val()));

            // check for not a number
            if(isNaN($("#filterPctVariance").val()) && $('#filterByMinimumPct').is(':checked') == true) {
                $("span#filterPctVarianceError").html('<br />&quot;' + $("#filterPctVariance").val() + '&quot; is not a number. Please enter a valid percentage for the filter.');
                return false;
            }
            // check for empty string
            if($("#filterPctVariance").val() == '' && $('#filterByMinimumPct').is(':checked') == true) {
                $("span#filterPctVarianceError").html('<br />Please enter a valid percentage for the filter.');
                return false;
            }
            // clear the error, if there is one:
            $("span#filterPctVarianceError").html('');
            // parse the float... the isNaN check above should insure a good parse
            $("#filterPctVariance").val(parseFloat($("#filterPctVariance").val()).toFixed(1));
        }

        $("#confirm").attr("disabled", "disabled");
        $("#confirm").text("Please wait...");
        return true;
    });
});