// Spoiler
$(document).ready(function() {
    $("#config").click(function() {
        $("#confignav").slideToggle("normal");
    });
});


// PopUp Cuadro
$("#abrir_box").click(function(){
    $("#cont_box").css("display", "block");
  });

  $("#cerrar_box").click(function(){
    $("#cont_box").css("display", "none");
  }); 

//Menu Tabla
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});