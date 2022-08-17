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
        $estado  = 'PE';
    }else{
        $usuario = NULL;
        $fecha   = NULL;
        $estado  = 'IN';
    }
                     
    $sqlActualiza = "UPDATE TB_PAGA_DET_CHEQUE
                     SET USUARIO_AUTORIZA = '". $usuario ."',
                     FECHA_AUTORIZA = ". $fecha .",
                     ESTADO = '". $estado ."'   
                     WHERE cod_registro||correlativo = ". $registro;                     
                                        
    $conActualiza = oci_parse($con,$sqlActualiza);
    $r = oci_execute($conActualiza);

}
    
?>