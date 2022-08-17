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
        $estado  = 'AU';
    }else{
        $usuario = NULL;
        $fecha   = 'NULL';
        $estado  = 'PA';
    }       

    $sqlActualiza = "UPDATE TB_PAGA_CH_PROVEEDOR
                     SET USUARIO_AUTORIZA = '". $usuario ."',
                     FECHA_AUTORIZA = ". $fecha .",
                     ESTADO = '". $estado ."'   
                     WHERE cod_registro = ". $registro;                     
                   
    $conActualiza = oci_parse($con,$sqlActualiza);
    $r = oci_execute($conActualiza);

}
    
?>