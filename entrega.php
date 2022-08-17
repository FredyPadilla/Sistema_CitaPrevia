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
        $estado  = 'EN';
    }else{
        $usuario = NULL;
        $fecha   = 'NULL';
        $estado  = 'PE';
    }
                     
    $sqlActualiza = "UPDATE TB_PAGA_DET_CHEQUE
                     SET USUARIO_ENTREGA = '". $usuario ."',
                     FECHA_ENTREGA = ". $fecha .",
                     ESTADO = '". $estado ."'   
                     WHERE cod_registro||correlativo = ". $registro;                     
                     
                   
    $conActualiza = oci_parse($con,$sqlActualiza);
    $r = oci_execute($conActualiza);

}
    
?>