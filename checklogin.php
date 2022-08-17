<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/html; charset=utf-8');
header("Pragma: no-cache");

mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');

$usu     = $_POST['usuario'];
$usuario = strtoupper($usu);

?>
<!DOCTYPE html>
<html>
<head>

    <meta http-equiv="content-type" content="text/html" />
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="img/favicon.ico" type="image/x-icon" />    
    
    <script src="js/sweetalert.js"> </script>   
    <link   rel="stylesheet" href="css/sweetalert.css" type="text/css"/>     

    <title>Verificando Acceso</title>

<script>
function ErrorIngreso()
    {
      swal({title:"Clave y/o Usuario Incorrecto!", type:"error", showConfirmButton:false, text:"ACCESO DENEGADO", timer:'1000'}, 

      function () 
    {
      location.href = "index.php"; 
    });
    }

function IngresoAceptado()
    {
      swal({title:"Usuario Aceptado!", type:"success", showConfirmButton:false, text:"ACCESO CORRECTO", timer:'1000'}, 
      function () 
    {       
      location.href = "menu.php?usuario=<?php echo base64_encode($usuario); ?>";
    });
    }
</script> 

</head>
<body>
<?php

    include "config/config.php";

    $usuario = $_POST['usuario'];
    $clave   = $_POST['clave'];    
    $usuario = strtoupper($usuario);
    $clave   = base64_encode($clave);    
    
    $sqlLogin = "SELECT v_usuario_acceso USUARIO, v_contrasena_acceso CONTRASENA 
                 FROM TB_PAGA_USUARIO
                 WHERE V_USUARIO_ACCESO  = '" . $usuario . "'
                 AND V_CONTRASENA_ACCESO = '" . $clave . "'";                 
    $conLogin = oci_parse($con,$sqlLogin);
    oci_execute($conLogin);    
    $resLogin = oci_fetch_object($conLogin);    

    if(!empty($resLogin)){
        $db_user = $resLogin->USUARIO;    
        $db_pass = $resLogin->CONTRASENA;
        
        if($usuario==$db_user AND $clave==$db_pass){
            echo "<script>IngresoAceptado();</script>";  
        }
    }elseif(empty($resLogin)){
        echo "<script>ErrorIngreso();</script>"; 
    }
      
    oci_close($con);

?>
</body>
</html>