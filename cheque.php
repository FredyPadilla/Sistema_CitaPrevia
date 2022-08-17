<?php
include "config/config.php";

if (isset($_POST))
{
    $usuario  = $_POST["usuario"];
    $registro = $_POST["registro"];
    $marca    = $_POST["marca"];
    
    if($marca==1){
        $usuario = $usuario;
        $fecha   = 'SYSDATE';
    }else{
        $usuario = 'NULL';
        $fecha   = 'NULL';
    }


    $sqlActualiza = "UPDATE TB_PAGA_CH_PROVEEDOR  
                     SET ESTADO = 'PA'   
                     WHERE cod_registro = '$registro'";                     
                     
                   
    $conActualiza = oci_parse($con,$sqlActualiza);
    $r = oci_execute($conActualiza);
}

   
?>