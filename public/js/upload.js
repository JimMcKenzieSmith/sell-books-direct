$(function(){
    $("#form").submit(function(){
        $("#upload").attr("disabled", "disabled");
        $("#upload").text("Please wait...");
        return true;
    });
});