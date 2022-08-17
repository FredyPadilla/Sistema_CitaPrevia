<?php
    include "config/config.php";

    $registro = $_POST['registro'];   
    $usuario  = $_POST['usuario'];
    
    $fhoy    = date("d/m/Y h:i A");                
    $fecha_modifica  = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
    
    $usuario_modifica = $usuario;
    $nulo           = 'NULL';
    
    $tipo_cur       = $_POST['tipo_cur'];   
    $no_daj         = $_POST['no_daj'];
    
    //Información CUR//    
    $cur_actual     = $_POST['cur_actual'];
    $anio_cur_actual= $_POST['anio_cur_actual'];
    $cur_nuevo      = $_POST['cur_nuevo'];
    $anio_cur_nuevo = $_POST['anio_cur_nuevo'];           
    $fecha_cur      = $_POST['fecha_cur'];     
    $fecha_cur1     = date("d/m/Y", strtotime($fecha_cur));     
    $fecha_cur1     = "TO_DATE('" . $fecha_cur1 . "','DD/MM/YYYY')";
    
    //Información Orden de Compra//
    $no_orden       = $_POST['no_orden'];
    $fecha_orden    = $_POST['fecha_orden'];
    $fecha_orden1   = date("d/m/Y", strtotime($fecha_orden));     
    $fecha_orden1   = "TO_DATE('" . $fecha_orden1 . "','DD/MM/YYYY')";
    $monto_orden    = $_POST['monto_orden'];
    $monto_orden1   = str_replace(",","",$monto_orden);
    $no_solped      = $_POST['no_solped'];
    $des_orden      = $_POST['des_orden'];
    /*$cod_tipo       = $_POST['cod_tipo'];*/
    $cod_tipo       = 5;
    $cod_proveedor  = $_POST['cod_proveedor'];
    $fuente         = $_POST['fuente'];
    $renglon        = $_POST['renglon'];
    $cod_dependencia= $_POST['cod_dependencia'];
    
    //Información de la Factura//
    $no_factura     = $_POST['no_factura'];
    $fecha_factura  = $_POST['fecha_factura'];
    $fecha_factura1 = date("d/m/Y", strtotime($fecha_factura));     
    $fecha_factura1 = "TO_DATE('" . $fecha_factura1 . "','DD/MM/YYYY')";        
    $monto_factura  = $_POST['monto_factura'];
    $monto_factura1 = str_replace(",","",$monto_factura);
                
    $sqlModifica  = "UPDATE TB_PAGA_CH_PROVEEDOR
                     SET CUR_ACTUAL         = ".  $cur_nuevo .",                     
                     ANIO_CUR_ACTUAL        = ".  $anio_cur_nuevo .",                     
                     FECHA_CUR              = ".  $fecha_cur1 .",                     
                     NO_ORDEN               = ".  $no_orden .",
                     FECHA_ORDEN            = ".  $fecha_orden1 .",
                     MONTO_ORDEN            = ".  $monto_orden1 .",
                     NO_SOLPED              = ".  $no_solped .",
                     DES_ORDEN              = '". $des_orden ."',
                     COD_TIPO               = ".  $cod_tipo .",
                     COD_PROVEEDOR          = ".  $cod_proveedor .",
                     FUENTE                 = ".  $fuente .",
                     RENGLON                = ".  $renglon .",
                     COD_DEPENDENCIA        = ".  $cod_dependencia .",                         
                     NO_FACTURA             = '". $no_factura ."',
                     FECHA_FACTURA          = ".  $fecha_factura1 .",
                     MONTO_FACTURA          = ".  $monto_factura1 .",
                     USUARIO_MODIFICA       = '". $usuario_modifica ."',
                     FECHA_MODIFICA         = ".  $fecha_modifica .",
                     TIPO_CUR               = '". $tipo_cur ."',                         
                     NO_DAJ                 = ".  $no_daj ."
                     WHERE COD_REGISTRO     = ".  $registro;                         
         
        $conModifica = oci_parse($con,$sqlModifica);
        $r = oci_execute($conModifica);
       
        if (!$r) {
            echo 'ERROR';                            
        }else{
            echo 'REGISTRO ACTUALIZADO';
        }    

?>
