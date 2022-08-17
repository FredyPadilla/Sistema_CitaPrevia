/*-- https://codeseven.github.io/toastr/ --*/
/*-- https://codeseven.github.io/toastr/demo.html --*/
$(document).ready(function(){ 
    $("#btn1").click(function(){
        //tipos de mensajes success, info, warning, error
        //titulo y mensaje de texto
        toastr["error"]("Ya no se pueden crear más cheques", "Mensaje de Advertencia")
        
    
    });
    

        toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": false,
          "progressBar": true,
          "positionClass": "toast-center-center",
          "preventDuplicates": true,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
    
    /*
    toastr.options = { 
        //primeras opciones
        "closeButton": false, //boton cerrar
        "debug": false,
        "newestOnTop": false, //notificaciones mas nuevas van en la parte superior
        "progressBar": true, //barra de progreso hasta que se oculta la notificacion
        "preventDuplicates": false, //para prevenir mensajes duplicados
        
        "onclick": null,
        
        //Posición de la notificación
        //toast-top-right, toast-bottom-right, toast-bottom-left, toast-top-left, toast-top-full-width, toast-bottom-full-width, toast-top-center, toast-bottom-center,
          toast-center-center, toast-center-full-width, toast-center-right, toast-center-left 
        "positionClass": "toast-top-right",
                
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    };*/
    
    
    
    
    
});	    