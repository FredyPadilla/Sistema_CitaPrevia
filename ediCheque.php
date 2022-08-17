<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/html; charset=utf-8');
header("Pragma: no-cache");

mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');

if (isset($_POST['usuario'])){
    $usuario=$_POST['usuario'];            
}

if (isset($_POST['no_registro'])){
    $no_registro=$_POST['no_registro'];            
}
if (isset($_POST['cod_registro'])){
    $no_registro=$_POST['cod_registro'];            
}

?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html" />
    
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />    
    
    <script src="js/sweetalert.js"> </script>   
    <link   rel="stylesheet" href="css/sweetalert.css" type="text/css"/>
        
      
  <script>  
  
    function alerta_error()
    {
     swal({type:'warning', title:'Error al Ingresar el Cheque...',  text:'ERROR', showConfirmButton: true, confirmButtonColor: "red", confirmButtonText:'Aceptar', timer: 1000},
     function(){
        location.href = 'tblCheque.php?usuario='+'<?php echo base64_encode($usuario); ?>';
     });     
    }   
  
    function alerta_acepta()
    {
      swal({type:'success', title:'El Cheque Fue Ingresado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
     function(){        
        location.href = 'tblCheque.php?usuario='+'<?php echo base64_encode($usuario); ?>';
     });     
    }
    
    function alerta_edita()
    {
      swal({type:'success', title:'El Registro Fue Modificado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
     function(){        
        location.href = 'tblCheque.php?usuario='+'<?php echo base64_encode($usuario); ?>';
     });     
    }
    
    function alerta_anula()
    {
      swal({type:'success', title:'El Registro Fue Anulado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "red", confirmButtonText:'Aceptar', timer: 1000},
     function(){        
        location.href = 'tblCheque.php?usuario='+'<?php echo base64_encode($usuario); ?>';
     });     
    }
    
    function alerta_cheque()
    {
      swal({type:'warning', title:'Limite de Monto de Cheque...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "orange", confirmButtonText:'Aceptar', timer: 1000},
     function(){        
        location.href = 'tblCheque.php?usuario='+'<?php echo base64_encode($usuario); ?>';
     });     
    }                  
    
  </script>
  
<style>
.parpadea {
  
    animation-name: parpadeo;
    animation-duration: 1s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;

    -webkit-animation-name:parpadeo;
    -webkit-animation-duration: 1s;
    -webkit-animation-timing-function:linear;
    -webkit-animation-iteration-count: infinite;
}
.text {
    color: red;  
    font-size:18px;  
}

@-moz-keyframes parpadeo{  
  0% { opacity: 1.0; }
  50% { opacity: 0.0; }
  100% { opacity: 1.0; }
}

@-webkit-keyframes parpadeo {  
  0% { opacity: 1.0; }
  50% { opacity: 0.0; }
   100% { opacity: 1.0; }
}

@keyframes parpadeo {  
  0% { opacity: 1.0; }
   50% { opacity: 0.0; }
  100% { opacity: 1.0; }
}
</style>  
  
	
</head>

<body>
</body>
</html>

<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/    
include "config/config.php";

//---------------------------------------------------------------------------------------------------------------------------
if (isset($_GET['usuario'])){
    $usuario = base64_decode($_GET['usuario']);           
}  

$sql1 ="SELECT n_persona_id ID, v_usuario_acceso USUARIO, INITCAP(v_primer_nombre||' '||v_primer_apellido) NOMBRE
        FROM TB_PAGA_USUARIO
        WHERE V_USUARIO_ACCESO = '$usuario'
        AND C_ESTADO = 'A'";
                        
$consql1 = oci_parse($con,$sql1);
oci_execute($consql1); 
$resql1 = oci_fetch_object($consql1);
$id     = $resql1->ID;    
$nombre = $resql1->NOMBRE;

$sql2 ="SELECT n_persona_id, n_rol_id, v_rol_desc descripcion, v_menu_link link, v_estado
        FROM TB_PAGA_MENU
        WHERE n_persona_id = '$id'
        AND V_ESTADO = 'A'";
                        
$consql2 = oci_parse($con,$sql2);
oci_execute($consql2); 

$sqlCuenta = "SELECT COD_BANCO, DES_BANCO, NO_CUENTA
              FROM TB_PAGA_CUENTA_BANCO
              ORDER BY 1";
$conCuenta = oci_parse($con,$sqlCuenta);
oci_execute($conCuenta);

$sqlCuenta1 = "SELECT COD_BANCO, DES_BANCO, NO_CUENTA
               FROM TB_PAGA_CUENTA_BANCO               
               ORDER BY 1";
$conCuenta1 = oci_parse($con,$sqlCuenta1);
oci_execute($conCuenta1);	


//---------------------------------------------------------------------------------------------------------------------------

    $fact = date("d/m/Y");
    $fmax = date("Y-m-d");    
        
    if (isset($_GET['noRegistro'])){
        $no_registro = base64_decode($_GET['noRegistro']);           
    }    
    
    if (isset($_GET['cod_registro'])){
        $no_registro = base64_decode($_GET['cod_registro']);           
    }        
    /*
    $file = fopen($_SERVER['DOCUMENT_ROOT'].'/fredy/Pagaduria/archivo.txt',"w");
    fwrite($file, $no_registro);        
    fclose($file);
    */
    $sqlConsulta  = "SELECT * FROM TB_PAGA_CH_PROVEEDOR
                     WHERE cod_registro = " . $no_registro;
                     
    $conConsulta = oci_parse($con,$sqlConsulta);
    oci_execute($conConsulta);
    $resConsulta = oci_fetch_object($conConsulta)    ;
    
    $tipo_cur       = $resConsulta->TIPO_CUR;    
    $no_daj         = $resConsulta->NO_DAJ;

    //Información CUR//
    $cur_actual     = $resConsulta->CUR_ANTERIOR;
    $anio_cur_actual= $resConsulta->ANIO_CUR_ANTERIOR;
    $cur_nuevo      = $resConsulta->CUR_ACTUAL;
    $anio_cur_nuevo = $resConsulta->ANIO_CUR_ACTUAL;
    $fecha_cur      = $resConsulta->FECHA_CUR;
    $fecha_cur1     = date("Y-m-d", strtotime($fecha_cur));
             
    //Información Orden de Compra//
    $no_orden       = $resConsulta->NO_ORDEN;    
    $fecha_orden    = $resConsulta->FECHA_ORDEN;
    $fecha_orden1   = date("Y-m-d", strtotime($fecha_orden));    
    $monto_orden    = number_format($resConsulta->MONTO_ORDEN,2,".",",");
    $monto_cur      = number_format($resConsulta->MONTO_CUR,2,".",",");
    $no_solped      = $resConsulta->NO_SOLPED;
    $des_orden      = $resConsulta->DES_ORDEN;
    $cod_tipo       = $resConsulta->COD_TIPO;
    
    $sqlTipo1 = "SELECT COD_TIPO, DES_TIPO 
                 FROM TB_PAGA_CAT_TIPO
                 WHERE cod_tipo = " . $cod_tipo;
    $conTipo1 = oci_parse($con,$sqlTipo1);
    oci_execute($conTipo1);    
    $resTipo1 = oci_fetch_object($conTipo1);    
    
    $cod_proveedor  = $resConsulta->COD_PROVEEDOR;
    
    $sqlProv1  = "SELECT COD_PROVEEDOR, DES_PROVEEDOR, NIT_PROVEEDOR 
                  FROM TB_PAGA_CAT_PROVEEDOR
                  WHERE cod_proveedor = " . $cod_proveedor;
    $conProv1 = oci_parse($con,$sqlProv1);
    oci_execute($conProv1);    
    $resProv1 = oci_fetch_object($conProv1);
    
    $fuente         = $resConsulta->FUENTE;
    $renglon        = $resConsulta->RENGLON;
    $cod_dependencia= $resConsulta->COD_DEPENDENCIA;    
    
    
    $sqlDepen1 = "SELECT COD_DEPENDENCIA, DES_DEPENDENCIA
                  FROM TB_PAGA_CAT_DEPENDENCIA
                  WHERE cod_dependencia = " . $cod_dependencia;
    $conDepen1 = oci_parse($con,$sqlDepen1);
    oci_execute($conDepen1);    
    $resDepen1 = oci_fetch_object($conDepen1);
    
    
    //Información de la Factura//
    $no_factura     = $resConsulta->NO_FACTURA;
    $fecha_factura  = $resConsulta->FECHA_FACTURA;
    $fecha_factura1 = date("Y-m-d", strtotime($fecha_factura));    
    $monto_factura  = number_format($resConsulta->MONTO_FACTURA,2,".",",");
    
//--------------------------------------------------------------------------------------------------------

    $sqlTipo = "SELECT COD_TIPO, DES_TIPO 
                FROM TB_PAGA_CAT_TIPO
                ORDER BY 1";
    $conTipo = oci_parse($con,$sqlTipo);
    oci_execute($conTipo);
    
    
    $sqlProv  = "SELECT COD_PROVEEDOR, DES_PROVEEDOR, NIT_PROVEEDOR 
                 FROM TB_PAGA_CAT_PROVEEDOR
                 ORDER BY 1";
    $conProv = oci_parse($con,$sqlProv);
    oci_execute($conProv);
    
    
    $sqlDepen = "SELECT COD_DEPENDENCIA, DES_DEPENDENCIA
                 FROM TB_PAGA_CAT_DEPENDENCIA
                 ORDER BY 1";
    $conDepen = oci_parse($con,$sqlDepen);
    oci_execute($conDepen);
    
    if(isset($_POST['modifica'])){
        
        $fhoy    = date("d/m/Y h:i A");
                
        $fecha_modifica  = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        $usuario_modifica= "USER";
        $nulo            = 'NULL';

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
        $cod_tipo       = $_POST['cod_tipo'];
        $cod_proveedor  = $_POST['cod_proveedor'];
        $fuente         = $_POST['fuente'];
        $renglon        = $_POST['renglon'];
        $cod_dependencia= $_POST['cod_dependencia'];
        
        //Información del Cheque//
        $no_cheque      = $_POST['no_cheque'];
        $fecha_cheque   = $_POST['fecha_cheque'];
        $fecha_cheque1  = date("d/m/Y", strtotime($fecha_cheque));     
        $fecha_cheque1  = "TO_DATE('" . $fecha_cheque1 . "','DD/MM/YYYY')";    
        $monto_cheque   = $_POST['monto_cheque'];
        $monto_cheque1  = str_replace(",","",$monto_cheque);
        /*$cuenta_bancaria= $_POST['cuenta_bancaria'];*/    
        
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
                         USUARIO_MODIFICA       = ".  $usuario_modifica .",
                         TIPO_CUR               = '". $tipo_cur ."',                         
                         NO_DAJ                 = ".  $no_daj ."
                         WHERE CUR_ANTERIOR     = ".  $cur_actual . "   
                         AND ANIO_CUR_ANTERIOR  = ".  $anio_cur_actual;
                         
        //echo $sqlModifica;                                      
        
         
        $conModifica = oci_parse($con,$sqlModifica);
        $r = oci_execute($conModifica);
        
       
        if (!$r) {
            echo '<script>
                    alerta_error();                                         
                  </script>';                            
        }else{
            echo '<script>
                    alerta_acepta();                  
                  </script>';
        }
    }   
    
    
    if(isset($_POST['cheque'])){
        
        $usuario = $_POST['usuario'];   
        $fhoy    = date("d/m/Y h:i A");
    
        $fecha_graba    = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        //$usuario_graba  = "USER";
        $usuario_graba  = $usuario;
        $nulo           = 'NULL';            
            
        $no_registro    = $_POST['no_registro'];
        $correlativo    = $_POST['correlativo'];
        $no_cheque      = $_POST['no_cheque'];                   
        $fecha_cheque   = $_POST['fecha_cheque'];     
        $fecha_cheque1  = date("d/m/Y", strtotime($fecha_cheque));     
        $fecha_cheque1  = "TO_DATE('" . $fecha_cheque1 . "','DD/MM/YYYY')";
        $monto_cheque   = $_POST['monto_cheque'];
        $monto_cheque1  = str_replace(",","",$monto_cheque);        
        $cuenta_bancaria= $_POST['cuenta_bancaria'];   
        $estado_cheque  = 'IN';
                
        $cadena = "" . $no_registro . "," .$correlativo . "," . $no_cheque . "," . $fecha_cheque1 . "," . $monto_cheque1 . "," . $cuenta_bancaria . 
                  ",'" . $usuario_graba . "'," . $fecha_graba . "," . $nulo . "," . $nulo . "," . $nulo . ",'" . $estado_cheque . 
                  "'," . $nulo . "," . $nulo . "," . $nulo .  "," . $nulo . "," . $nulo . "," . $nulo . "";
                  
        $sqlDif  = "SELECT COD_REGISTRO, MONTO_CUR, MONTO_CHEQUE, MONTO_CUR-MONTO_CHEQUE DIFERENCIA
                    FROM(
                    SELECT a.cod_registro, SUM(DISTINCT(NVL(a.monto_cur,0))) MONTO_CUR,
                    NVL(SUM(DECODE(d.estado,'IN', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'PE', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'AU', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'EN', d.monto_cheque)),0) MONTO_CHEQUE
                    FROM TB_PAGA_CH_PROVEEDOR a
                    LEFT JOIN TB_PAGA_DET_CHEQUE d
                    ON a.COD_REGISTRO = d.COD_REGISTRO
                    WHERE a.estado = 'AU'
                    AND a.correo = 'G'
                    AND a.cod_registro = " . $no_registro . "
                    GROUP BY a.cod_registro)";
                    
                    $conDif = oci_parse($con,$sqlDif);
                    oci_execute($conDif);    
                    $resDif = oci_fetch_object($conDif);
        
        $cheque = $resDif->MONTO_CHEQUE+$monto_cheque1;        
        $cur    = $resDif->MONTO_CUR;
        
        if($cheque>$cur){
            echo '<script>alerta_cheque();</script>';     
        }else{                 
                              
        $sqlInserta = "INSERT INTO TB_PAGA_DET_CHEQUE
                       VALUES($cadena)";                       
        $conInserta = oci_parse($con,$sqlInserta);                
        $r = oci_execute($conInserta);
        if (!$r) {
            echo '<script>
                    alerta_error();                                         
                  </script>';                                
        }else{
            echo '<script>
                    alerta_acepta();                  
                  </script>';            
        }
        
       } 
         
    }    
    
    
    if(isset($_POST['echeque'])){
        
        $usuario = $_POST['usuario'];
        
        $fhoy    = date("d/m/Y h:i A");
    
        $fecha_edita    = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        //$usuario_edita  = "USER";
        $usuario_edita  = $usuario;
        $nulo           = 'NULL';       
        $cod_registro   = $_POST['cod_registro'];
        $cod_correl     = $_POST['cod_correl'];
        $no_cheque      = $_POST['no_cheque'];                   
        $fecha_cheque   = $_POST['fecha_cheque'];     
        $fecha_cheque1  = date("d/m/Y", strtotime($fecha_cheque));     
        $fecha_cheque1  = "TO_DATE('" . $fecha_cheque1 . "','DD/MM/YYYY')";
        $monto_cheque   = $_POST['monto_cheque'];
        $monto_cheque1  = str_replace(",","",$monto_cheque);        
        $cuenta_bancaria= $_POST['cuenta_bancaria'];       
        
        
        $sqlDif = "SELECT COD_REGISTRO, MONTO_CUR, MONTO_CHEQUE, MONTO_CUR-MONTO_CHEQUE DIFERENCIA
                   FROM(
                   SELECT a.cod_registro, SUM(DISTINCT(NVL(a.monto_cur,0))) MONTO_CUR,
                   NVL(SUM(DECODE(d.estado,'IN', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'PE', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'AU', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'EN', d.monto_cheque)),0) MONTO_CHEQUE
                   FROM TB_PAGA_CH_PROVEEDOR a
                   LEFT JOIN TB_PAGA_DET_CHEQUE d
                   ON a.COD_REGISTRO = d.COD_REGISTRO
                   WHERE a.estado = 'AU'
                   AND a.correo = 'G'
                   AND a.cod_registro = " . $cod_registro . "                   
                   GROUP BY a.cod_registro)";
                    
                    $conDif = oci_parse($con,$sqlDif);
                    oci_execute($conDif);    
                    $resDif = oci_fetch_object($conDif);
        
        $cheque = $resDif->MONTO_CHEQUE+$monto_cheque1;        
        $cur    = $resDif->MONTO_CUR;

        /*
        $file = fopen($_SERVER['DOCUMENT_ROOT'].'/fredy/Pagaduria/archivo.txt',"w");
        fwrite($file, $cheque);        
        fclose($file);
        */
        
        if($cheque>$cur){
            echo '<script>alerta_cheque();</script>';     
        }else{         
        
            $sqlEdita   = "UPDATE TB_PAGA_DET_CHEQUE
                           SET NO_CHEQUE      = ".  $no_cheque .",                     
                           FECHA_CHEQUE       = ".  $fecha_cheque1 .",                     
                           MONTO_CHEQUE       = ".  $monto_cheque1 .",                     
                           COD_BANCO          = ".  $cuenta_bancaria .",
                           USUARIO_MODIFICA   = '". $usuario_edita ."',                      
                           FECHA_MODIFICA     = ".  $fecha_edita ."
                           WHERE COD_REGISTRO = ".  $cod_registro . "   
                           AND CORRELATIVO    = ".  $cod_correl;       
           
            $conEdita = oci_parse($con,$sqlEdita);
            $r = oci_execute($conEdita);
        
        if (!$r) {
            echo '<script>
                    alerta_error();                                         
                  </script>';                                
        }else{
            echo '<script>
                    alerta_edita();                  
                  </script>';            
        }
        
       } 
    }    
    
    
    if(isset($_POST['anular'])){
        
        $usuario = $_POST['usuario'];
                
        $fhoy    = date("d/m/Y h:i A");
    
        $fecha_anula    = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        //*$usuario_anula  = "USER";
        $usuario_anula  = $usuario;
        $nulo           = 'NULL';            
            
        $cod_registro    = $_POST['cod_registro'];
        $cod_correl      = $_POST['cod_correl'];
        $no_cheque       = $_POST['no_cheque'];                   
        $fecha_cheque    = $_POST['fecha_cheque'];     
        $fecha_cheque1   = date("d/m/Y", strtotime($fecha_cheque));     
        $fecha_cheque1   = "TO_DATE('" . $fecha_cheque1 . "','DD/MM/YYYY')";
        $monto_cheque    = $_POST['monto_cheque'];
        $monto_cheque1   = str_replace(",","",$monto_cheque);        
        $cuenta_bancaria = $_POST['cuenta_bancaria'];       
        $des_anula       = $_POST['des_anula'];
        $estado = 'AN';
        
        $sqlAnula = "UPDATE TB_PAGA_DET_CHEQUE
                     SET DES_ANULA      = '". $des_anula     ."',
                     USUARIO_ANULA      = '". $usuario_anula ."',                      
                     FECHA_ANULA        =  ". $fecha_anula   .",
                     ESTADO             = '". $estado        ."'
                     WHERE COD_REGISTRO =  ". $cod_registro  ."   
                     AND CORRELATIVO    =  ". $cod_correl;                     
                
                
        $conAnula = oci_parse($con,$sqlAnula);
        $r = oci_execute($conAnula);
            
        $conAnula = oci_parse($con,$sqlAnula);
        $r = oci_execute($conAnula);
        if (!$r) {
            echo '<script>
                    alerta_error();                                         
                  </script>';                                
        }else{
            echo '<script>
                    alerta_anula();                  
                  </script>';            
        }
    }       
        
    
?>

<!DOCTYPE HTML>
<html lang="es">

    <head>
    
        <meta http-equiv="content-type" content="text/html" charset="UTF-8"/>	            
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Edición de Datos</title>

        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
        
        <!-- Bootstrap 4.5.2 CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        
        <!-- Select Bootstrap 1.13.14 CSS -->        
        <link rel="stylesheet" href="css/bootstrap-select.min.css" />
        
        <!-- Font-Awesome 4.7.0 CSS -->    
        <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        
        <!-- Main CSS -->
        <link rel="stylesheet" type="text/css" href="css/main.css" />        
        
        <!-- DataTables 4 CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" />
        
        <!-- jQuery 3.5.1 JS -->
        <script src="js/jquery.min.js"></script>
        
        <!-- Popper 1.16.1 JS -->
        <script src="js/popper.min.js"></script>        
             
        <!-- Bootstrap 4.5.2 JS -->     
        <script src="js/bootstrap.min.js"></script>
        
        <!-- Select Bootstrap 1.13.14 JS -->               
        <script src="js/bootstrap-select.min.js"></script>
        
        <!-- DataTables 4 JQUERY -->               
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>        
        
        <!-- DataTables 4 JS -->               
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>        
        
        <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet"/>
        <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
        
       
    <style>
      input.check {
        width: 25px;
        height: 25px;
        padding: 0;
        margin:0;
        vertical-align: bottom;
        position: relative;
        top: -1px;  
        cursor: pointer;}
        
.navbar {
    background-color: #e3f2fd;
}
.navbar ul {
    list-style-type: none;
    padding: 0px;
    display: table;
    width: 100%;
    table-layout: fixed;
}
.navbar li{
    padding: 0px;
    display:table-cell;
    width: 0px; /* just for the browser to get idea that all cells are equal */
    text-align: center;
    border: 0px solid #e3f2fd;
}        
        
    </style>
        

    </head>

    <body class="container__body">
    <br /><br /><br />
    
        <header class="container__header">
            <div class="wrapper__logo--interno registro--logo">
                <img class="escudo" src="img/escudo-hdpi.png" alt="Escudo Municipalidad de Guatemala" />
            </div>
                <h1 class="titulo--interno">Ingreso de Cheques</h1>
                
            <div class="wrapper__logo--interno">
                <img class="logo" src="img/logo_informatica.png" alt="Logo Dirección de informática" />
            </div>
            
        </header>
        
        <!--INICIO MENU-->
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary">        
                    
            <?php
                $li='menu.php';
                $de=$nombre;
                echo '<a class="navbar-brand" href="'.$li.'?usuario='.base64_encode($usuario).'">'.$de."</a>";
            ?>         
          
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
          
            <ul class="navbar-nav mr-auto">            
            
                <?php        
                    while(($resql2 = oci_fetch_object($consql2)) != false){                    
                        $link = $resql2->LINK;
                        $desc = $resql2->DESCRIPCION;
                        echo "<li class='nav-item'>";                
                        echo '<a class="nav-link" href="'.$link.'?usuario='.base64_encode($usuario).'">'.$desc."<span class='sr-only'>(current)</span></a>";
                        echo "</li>";                                   
                    }
                ?>  
                
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Mantenimiento
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <!--<a class="dropdown-item" href="ingProveedor.php?usuario=">Proveedores</a>-->                  
                  <?php
                  $desc1 = 'Proveedores';
                  echo '<a class="dropdown-item" href="ingProveedor.php?usuario='.base64_encode($usuario).'">'.$desc1."<span class='sr-only'>(current)</span></a>";
                  ?>
                  <div class="dropdown-divider"></div>
                  <?php
                  $desc2 = 'Dependencias';
                  echo '<a class="dropdown-item" href="ingDependencia.php?usuario='.base64_encode($usuario).'">'.$desc2."<span class='sr-only'>(current)</span></a>";
                  ?>
                  <div class="dropdown-divider"></div>
                  <?php
                  $desc3 = 'Tipos';
                  echo '<a class="dropdown-item" href="ingTipos.php?usuario='.base64_encode($usuario).'">'.$desc3."<span class='sr-only'>(current)</span></a>";
                  ?>
                </div>
                </li>
                
                <li class="nav-item">
                  <?php
                  $desc4 = 'Consulta';
                  echo '<a class="nav-link" href="tblConsulta.php?usuario='.base64_encode($usuario).'">'.$desc4."<span class='sr-only'>(current)</span></a>";
                  ?>                        
                </li>                   
                              
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Cerrar Sesión</a>                        
                </li>              
        
            </ul>
            
          </div>
        </nav>
        <br />
        <br />
        <br />
        <!--FIN MENU-->             
        

            
        <!--INICIO DE CONTENEDOR MAESTRO-->        
        <div class="container">
     
        <div class="card-body">                        
            <form name="frmingreso" id="frmingreso" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">        
                                    
            <!--FECHA ACTUAL-->            
               <div style="text-align: left; color: black;"><?php echo "Fecha: " . $fact; ?></div>
               
                    <!--INICIO DATOS DEL CUR-->
                    <div class="card">
                        
                        <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-list-alt fa-stack-1x fa-inverse"></i></span><strong>Datos CUR</strong><a href="tblCheque.php?usuario=<?php echo base64_encode($usuario); ?>" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-search"></i> Consultar Cheque</a></div>

                        <div class="card-body">     
                        
                        <input type="hidden" name="no_registro" id="no_registro" value="<?php if(!empty($no_registro)) echo $no_registro; ?>" class="form-control"  disabled=""/>
                                                
                            <div class="row">                        
                                <div class="col-sm-3">                                    
                                </div>
                    
                                <div class="col-sm-3">                                
                                    <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="radio" class="check" name="tipo_cur" id="tipo_cur" disabled="" value="CC" <?php if(!empty($tipo_cur)) if($tipo_cur=='CC') echo 'checked'; ?> />
                                    CUR Contable
                                    </label>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3">
                                    <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="radio" class="check" name="tipo_cur" id="tipo_cur" disabled="" value="CP" <?php if(!empty($tipo_cur)) if($tipo_cur=='CP') echo 'checked'; ?> />
                                    CUR Presupuestario
                                    </label>
                                    </div>                                
                                </div>                  
                    
                                <div class="col-sm-3">
                                </div>                    
                            </div>    
                            
                            <hr />                      
                        
                        
                        <div class="row">                         
                        
                            <div class="col-sm-6">
                            <div class="card">
                            <div class="card-body">
                            <h6 class="card-title">No. CUR y Año</h6>    
                            
                            <div class="row">     
                            
                                <div class="col-sm-7">                                    
                                    <div class="form-group">
                                        <input type="text" name="cur_actual" id="cur_actual" value="<?php if(empty($cur_actual)){echo '0';} else {echo $cur_actual;} ?>" class="form-control" placeholder="Ingrese CUR" maxlength="25" onkeypress="return soloNumeros(event);" required="" disabled=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">
                                    
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_actual" id="anio_cur_actual" value="<?php if(empty($anio_cur_actual)){echo '0';} else {echo $anio_cur_actual;} ?>" class="form-control" placeholder="Ingrese Año" maxlength="4" onkeypress="return soloNumeros(event);" disabled=""/>
                                    </div>                        
                                </div>                             

                            
                            </div>                                
                            </div>
                            </div>
                            </div>
                            
                            <div class="col-sm-6">
                            <div class="card">
                            <div class="card-body">
                            <h6 class="card-title">No. CUR y Año Actual</h6>
                            
                            <div class="row">     
                            
                                <div class="col-sm-7">                                    
                                    <div class="form-group">
                                        <input type="text" name="cur_nuevo" id="cur_nuevo" value="<?php if(!empty($cur_nuevo)) echo $cur_nuevo; ?>" class="form-control" placeholder="Ingrese CUR Actual" maxlength="25" onkeypress="return soloNumeros(event);" disabled="" />
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">
                                    
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_nuevo" id="anio_cur_nuevo" value="<?php if(!empty($anio_cur_nuevo)) echo $anio_cur_nuevo; ?>" class="form-control" placeholder="Ingrese Año Actual" maxlength="4" onkeypress="return soloNumeros(event);" disabled="" />
                                    </div>                        
                                </div>                             

                            
                            </div>                                                                  
                            </div>
                            </div>
                            </div> 
                        
                        </div>
                        
                                                
                        <div class="row">                         
                        
                            <div class="col-sm-6">
                            <div class="card">
                            <div class="card-body">
                            <h6 class="card-title">Fecha y Monto del CUR</h6>
                            <div class="row">     
                            
                                <div class="col-sm-7">                                    
                                    <div class="form-group">
                                        <input type="date" name="fecha_cur" id="fecha_cur" value="<?php if(!empty($fecha_cur1)) echo $fecha_cur1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" disabled=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">        
                                    <div class="form-group">
                                        <input type="text" name="monto_cur" id="monto_cur" value="<?php if(!empty($monto_cur)) echo $monto_cur; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" disabled="" />
                                    </div>
                                </div>  
                            </div>         
                                                   
                            </div>
                            </div>
                            </div>
                            
                            <div class="col-sm-6">
                            <div class="card">
                            <div class="card-body">
                            <h6 class="card-title">No. DAJ</h6>
                            
                            <div class="row">     
                            
                                <div class="col-sm-7">                                    
                                    <div class="form-group">
                                        <input type="text" name="no_daj" id="no_daj" value="<?php if(!empty($no_daj)) echo $no_daj; ?>" class="form-control" placeholder="Ingrese No. DAJ" maxlength="25" onkeypress="return soloNumeros(event);" disabled=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">                    
                                </div>                             

                            
                            </div>                                                                  
                            </div>
                            </div>
                            </div> 
                        
                        </div>
                                                
                        

                                 
                        </div>    
                        
                    </div>            
             
                <!--FIN DATOS DEL CUR-->
        
                <!--INICIO DATOS DE LA ORDEN DE COMPRA-->
                
                <div class="card">            
                
                    <!--<div class="card-header"><i class="fa fa-shopping-bag fa-lg"></i> <strong>Datos Orden de Compra</strong></div>-->                                        
                    <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-shopping-bag fa-stack-1x fa-inverse"></i></span><strong>Datos Orden de Compra</strong></div>
                    
                
                    <div class="card-body">         
                            
                        <div class="row">                        
                            <div class="col-sm-3">
                                <label>No. de Orden</label>
                                <div class="form-group">
                                    <input type="text" name="no_orden" id="no_orden" value="<?php if(!empty($no_orden)) echo $no_orden; ?>" class="form-control" placeholder="Ingrese No. de Orden" maxlength="25" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Orden</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_orden" id="fecha_orden" value="<?php if(!empty($fecha_orden1)) echo $fecha_orden1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" disabled="" />
                                </div>
                            </div>                              
                    
                            <div class="col-sm-3">
                                <label>Monto de Orden</label>
                                <div class="form-group">
                                    <input type="text" name="monto_orden" id="monto_orden" value="<?php if(!empty($monto_orden)) echo $monto_orden; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>                        
                            </div> 
                    
                            <div class="col-sm-3">
                                <label>No. de Solped</label>
                                <div class="form-group">
                                    <input type="text" name="no_solped" id="no_solped" value="<?php if(!empty($no_solped)) echo $no_solped; ?>" class="form-control" placeholder="Ingrese el No. de Solped" maxlength="25" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>                        
                            </div>
                  
                       </div>                        
                              
               
                        <div class="row">     
                            <div class="col-sm-9">
                                <label>Descripción de la Orden</label>
                                <div class="form-group">
                                    <input type="text" name="des_orden" id="des_orden" value="<?php if(!empty($des_orden)) echo $des_orden; ?>" class="form-control" placeholder="Ingrese Descripción de la Orden" maxlength="150" disabled=""/>
                                </div>                        
                            </div>    


                            <div class="col-sm-3">
                                <label>Tipo Cuenta</label>
                                <div class="form-group">             
                                                  
                                    <select class="form-control selectpicker show-tick" name="cod_tipo" id="cod_tipo" data-show-subtext="false" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Tipo..." disabled="">                               
                                        
                                        <?php                  
                                            echo '<option value="'.$resTipo1->COD_TIPO.'" selected="">'.$resTipo1->DES_TIPO.' </option>';                                                                                        
                                            while(($resTipo = oci_fetch_object($conTipo)) != false){                                                
                                                echo '<option value="'.$resTipo->COD_TIPO.'">'.$resTipo->DES_TIPO.' </option>';
                                        }?>
                                        
                                    </select>                         
                                </div>
                            </div>  
                        </div>
                        
                         <div class="row">   
                            <div class="col-sm-12">
                                <label>Proveedor</label>
                                <div class="form-group">
                                                
                                    <select class="form-control selectpicker show-tick" name="cod_proveedor" id="cod_proveedor" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Proveedor..." disabled="" >                                         
                                        <?php               
                                            echo '<option  data-subtext='.'NIT:'.$resProv1->NIT_PROVEEDOR.' value="'.$resProv1->COD_PROVEEDOR.'" selected="">'.$resProv1->DES_PROVEEDOR.' </option>';                                                       
                                            while(($resProv = oci_fetch_object($conProv)) != false){
                                                echo '<option data-subtext='.'NIT:'.$resProv->NIT_PROVEEDOR.' value="'.$resProv->COD_PROVEEDOR.'">'.$resProv->DES_PROVEEDOR.' </option>';                                                                                       
                                        }?>
                                    </select>
                                    
                                </div>
                            </div>                              
                         
                         </div>
                        
                        <div class="row">                        
                            <div class="col-sm-6">
                                <label>Fuente Financiamiento</label>
                                <div class="form-group">
                                    <input type="text" name="fuente" id="fuente" value="<?php if(!empty($fuente)) echo $fuente; ?>" class="form-control" placeholder="Ingrese Fuente" maxlength="25" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>
                            </div>
                    
                            <div class="col-sm-6">
                                <label>Renglón Presupuestario</label>
                                <div class="form-group">
                                    <input type="text" name="renglon" id="renglon" value="<?php if(!empty($renglon)) echo $renglon; ?>" class="form-control" placeholder="Ingrese Renglón" maxlength="25" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>                        
                            </div>                    
                        </div>    
                        
                         <div class="row">   
                            <div class="col-sm-12">
                                <label>Dependencia</label>
                                <div class="form-group">
                                                
                                    <select class="form-control selectpicker show-tick" name="cod_dependencia" id="cod_dependencia" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Dependencia..." disabled="" >                                         
                                        <?php                                 
                                            echo '<option value="'.$resDepen1->COD_DEPENDENCIA.'" selected="">'.$resDepen1->DES_DEPENDENCIA.' </option>';                                 
                                            while(($resDepen = oci_fetch_object($conDepen)) != false){
                                                echo '<option value="'.$resDepen->COD_DEPENDENCIA.'">'.$resDepen->DES_DEPENDENCIA.' </option>';                                                                                       
                                        }?>
                                    </select>
                                </div>
                            </div>                              
                         
                         </div>
                                                                 
                        
                    </div>
                </div>
                <!--FIN DATOS DE LA ORDEN DE COMPRA-->        
                
        
                <!--INICIO DATOS DE LA FACTURA-->                
                <div class="card">
            
                    <!--<div class="card-header"><i class="fa fa-id-card fa-lg"></i> <strong>Datos de la Factura</strong></div>-->
                    <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-id-card fa-stack-1x fa-inverse"></i></span><strong>Datos de la Factura</strong></div>
    
                    <div class="card-body">

                        <div class="row">                        
                            <div class="col-sm-3">
                                <label>No. de Factura</label>
                                <div class="form-group">
                                    <input type="text" name="no_factura" id="no_factura" value="<?php if(!empty($no_factura)) echo $no_factura; ?>" class="form-control" placeholder="Ingrese No. de Factura" maxlength="25" onkeyup="javascript:this.value=this.value.toUpperCase();" disabled="" />
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Factura</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_factura" id="fecha_factura" value="<?php if(!empty($fecha_factura1)) echo $fecha_factura1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" disabled="" />
                                </div>
                            </div>                                   
                    
                            <div class="col-sm-3">
                                <label>Monto de Factura</label>
                                <div class="form-group">
                                    <input type="text" name="monto_factura" id="monto_factura" value="<?php if(!empty($monto_factura)) echo $monto_factura; ?>"class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" disabled="" />
                                </div>                        
                            </div> 
                            
                            <div class="col-sm-3">                       
                                
                            </div>                    
                                                  
                        </div>
                    </div> 
                </div>    
                <!--FIN DATOS DE LA FACTURA-->
                
                <!--INICIO DATOS DEL CHEQUE-->                
                <div class="card">
                            
                
            <?php
                    
                $sqlDif  = "SELECT COD_REGISTRO, MONTO_CUR, MONTO_CHEQUE, MONTO_CUR-MONTO_CHEQUE DIFERENCIA
                            FROM(
                            SELECT a.cod_registro, SUM(DISTINCT(NVL(a.monto_cur,0))) MONTO_CUR,
                            NVL(SUM(DECODE(d.estado,'IN', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'PE', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'AU', d.monto_cheque)),0) +NVL(SUM(DECODE(d.estado,'EN', d.monto_cheque)),0) MONTO_CHEQUE
                            FROM TB_PAGA_CH_PROVEEDOR a
                            LEFT JOIN TB_PAGA_DET_CHEQUE d
                            ON a.COD_REGISTRO = d.COD_REGISTRO
                            WHERE a.estado = 'AU'
                            AND a.correo = 'G'
                            AND a.cod_registro = " . $no_registro . "
                            GROUP BY a.cod_registro)";
                            
                            $conDif = oci_parse($con,$sqlDif);
                            oci_execute($conDif);    
                            $resDif = oci_fetch_object($conDif);
            
                $suma       = number_format($resDif->MONTO_CHEQUE,2,".",",");
                $diferencia = $resDif->DIFERENCIA;
                
            
                if($diferencia==0){
                    echo "<div class='card-header'><span class='fa-stack fa-lg'><i class='fa fa-circle fa-stack-2x'></i><i class='fa fa-money fa-stack-1x fa-inverse'></i></span><strong>Datos del Cheque</strong><a class='float-right btn btn-outline-danger disabled' ><i class='fa fa-fw fa-warning fa-lg'></i> <label class='parpadea text'>Ha llegado al Límite de Cheques</label></a></div>";                     
                }else{  
                    echo "<div class='card-header'><span class='fa-stack fa-lg'><i class='fa fa-circle fa-stack-2x'></i><i class='fa fa-money fa-stack-1x fa-inverse'></i></span><strong>Datos del Cheque</strong><a data-toggle='modal' data-target='#agrCheque' data-no_registro='$no_registro' class='float-right btn btn-secondary btn-sm'><i class='fa fa-fw fa-plus'></i> Agregar Cheque</a></div>";
                }      
            ?>
            
                    <div class="card-body">
                            
                             
             <!--INICIO DE TABLA-->
                        <?php         
                        
                            //echo $no_registro;                        
                        
                            $sqlCheque = "SELECT a.cod_registro, a.correlativo, a.no_cheque, a.fecha_cheque, a.monto_cheque, b.cod_banco, b.no_cuenta||' / '||b.des_banco cuenta_bancaria, 
                                          DECODE(a.estado,'IN','INGRESADO','PE','POR ENTREGAR','AU','AUTORIZADO','AN','ANULADO','EN','ENTREGADO') estado
                                          FROM TB_PAGA_DET_CHEQUE a, TB_PAGA_CUENTA_BANCO b
                                          WHERE a.cod_banco = b.cod_banco
                                          AND a.cod_registro = " . $no_registro . "
                                          ORDER BY 1";
                                          
                            $conCheque = oci_parse($con,$sqlCheque);
                            oci_execute($conCheque);
                                       
                            echo '<table id="dTable" class="table table-striped table-bordered table-hover dt-boostrap" style="width:100%">
                                  <thead  style="background-color: #cccccc">
                                    <tr>       
                                        <th>No.</th>              
                                        <th>No. Cheque</th>
                                        <th>Fecha Cheque</th>                                                                                
                                        <th>Monto Cheque</th>
                                        <th>Cuenta Bancaria</th>
                                        <th>Estado</th>
                                        <th>Editar</th>
                                        <th>Anular</th>
                                    </tr>
                                    </thead>
                                    <tbody>';             
                                    
                                    while(($resCheque = oci_fetch_object($conCheque)) != false){      
                                        
                                        if($resCheque->ESTADO=='INGRESADO'){
                                            $color = '#f8504b';
                                        }
                                        if($resCheque->ESTADO=='POR ENTREGAR'){
                                            $color = '#ffff84';
                                        }                                                                                            
                                        if($resCheque->ESTADO=='ENTREGADO'){
                                            $color = '#4efd54';
                                                         
                                        }                                  
                                        if($resCheque->ESTADO=='ANULADO'){
                                            $color = '#999999';                                           
                                        }
                                        
                                        echo '<tr>';
                                        echo '<td style="text-align: center;">'; 
                                            $cod_registro = $resCheque->COD_REGISTRO;
                                            echo $resCheque->CORRELATIVO;
                                            $cod_correl = $resCheque->CORRELATIVO;
                                        echo '</td>';                        
                                        echo '<td style="text-align: center;">'; 
                                            echo $resCheque->NO_CHEQUE;   
                                            $no_cheque = $resCheque->NO_CHEQUE;                                         
                                        echo '</td>';     
                                        echo '<td style="text-align: center;">'; 
                                            //echo $resCheque->FECHA_CHEQUE;
                                            $fecha_cheque   = $resCheque->FECHA_CHEQUE;
                                            $fecha_cheque1  = date("d/m/Y", strtotime($fecha_cheque));
                                            echo $fecha_cheque1;
                                            $fecha_cheque2 = date("Y-m-d", strtotime($fecha_cheque));                                                                                    
                                        echo '</td>';
                                        echo '<td style="text-align: right;">'; 
                                            $monto_cheque   = number_format($resCheque->MONTO_CHEQUE,2,".",",");
                                            echo $monto_cheque;                                            
                                        echo '</td>';
                                        echo '<td style="text-align: left;">'; 
                                            echo $resCheque->CUENTA_BANCARIA;
                                            $cuenta = $resCheque->CUENTA_BANCARIA;                                            
                                            $cuenta_bancaria = $resCheque->COD_BANCO;
                                        echo '</td>';                        
                                        echo '<td style="text-align: center; background-color:' . $color . '">'; 
                                            echo $resCheque->ESTADO;                                            
                                        echo '</td>';                                                         
                                        echo '<td style="text-align: center;">';    
                                        //DECODE(estado,'IN','INGRESADO','PA','POR AUTORIZAR','AU','AUTORIZADO','AN','ANULADO','EN','ENTREGADO') estado
                                        if ($resCheque->ESTADO=="POR ENTREGAR" OR $resCheque->ESTADO=="AUTORIZADO" 
                                        OR $resCheque->ESTADO=="ANULADO" OR $resCheque->ESTADO=="ENTREGADO"){
                                            echo "<a data-toggle='modal' data-target='#ediCheque' title='Anular Orden de Compra' class='btn btn-outline-secondary btn-sm disabled'><i class='fa fa-edit'></i></a>";
                                        } else{                                                
                                            echo "<a data-toggle='modal' data-target='#ediCheque' 
                                                     data-cod_registro='$cod_registro' 
                                                     data-cod_correl='$cod_correl' 
                                                     data-no_cheque='$no_cheque'
                                                     data-fecha_cheque='$fecha_cheque2'
                                                     data-monto_cheque='$monto_cheque'
                                                     data-cuenta_bancaria='$cuenta_bancaria'                                                     
                                                     title='Editar Cheque' class='btn btn-outline-success btn-sm'><i class='fa fa-edit'></i></a>";
                                            }        
                                        echo '</td>';        
                                        echo '<td style="text-align: center;">';
                                        if ($resCheque->ESTADO=="POR ENTREGAR" OR $resCheque->ESTADO=="AUTORIZADO" 
                                        OR $resCheque->ESTADO=="ANULADO" OR $resCheque->ESTADO=="ENTREGADO"){
                                            echo "<a data-toggle='modal' data-target='#anuCheque' title='Anular Registro' class='btn btn-outline-secondary btn-sm disabled'><i class='fa fa-ban'></i></a>";
                                        } else{                                                
                                            echo "<a data-toggle='modal' data-target='#anuCheque' 
                                                     data-cod_registro='$cod_registro' 
                                                     data-cod_correl='$cod_correl' 
                                                     data-no_cheque='$no_cheque'
                                                     data-fecha_cheque='$fecha_cheque2'
                                                     data-monto_cheque='$monto_cheque'
                                                     data-cuenta='$cuenta'                                                      
                                                     title='Anular Cheque' class='btn btn-outline-danger btn-sm'><i class='fa fa-ban'></i></a>";
                                            }        
                                        echo '</td>';   
                                        
                                                                                                                                                                     
                                        echo '</tr>'; 
                                        
                                        }             
                                echo '
                                </tbody>
                                  <tfoot style="background-color: #cccccc">
                                    <tr>       
                                        <th></th>              
                                        <th></th>
                                        <th style="text-align: right;"></th>';                                                                                
                                        echo '<th style="text-align: right;">' . 'TOTAL: ' . $suma . '</th>';
                                        echo '<th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>                                    
                            </table>';                
                        ?>
                    <!--FIN DE TABLA-->
                    </div>
                </div>
                <!--FIN DATOS DEL CHEQUE-->    
                
                
        <!-- Modal Agregar Cheque-->
        <div class="modal fade" id="agrCheque" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Datos del Cheque</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              
              <form name="frmcheque" id="frmcheque" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">                            
              
                <?php
                
                        $sqlMax ="SELECT NVL(MAX(CORRELATIVO),0)+1 correlativo
                        FROM TB_PAGA_DET_CHEQUE
                        WHERE COD_REGISTRO = " . $no_registro;
                    
                        $conMax = oci_parse($con,$sqlMax);
                        oci_execute($conMax); 
                        $resMax = oci_fetch_object($conMax);    
                        $correlativo = $resMax->CORRELATIVO;
                                     
                ?>
                            <input type="hidden" name="usuario" id="usuario" value="<?php echo $usuario; ?>" readonly=""/>
                            <input type="hidden" name="no_registro" id="no_registro" value="no_registro" class="form-control" readonly=""/>
                            <input type="hidden" name="correlativo" id="correlativo" value="<?php if(!empty($correlativo)) echo $correlativo; ?>" class="form-control" readonly=""/>
              
                            <div class="container">     
                            
                        <div class="row">                                                 
                                <div class="col-sm-4">
                                    <label>No. Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="no_cheque" id="no_cheque" class="form-control" placeholder="Ingrese Número Cheque" maxlength="25" required="" autofocus=""/>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <label>Fecha Cheque</label>
                                    <div class="form-group">                            
                                        <input type="date" name="fecha_cheque" id="fecha_cheque" class="form-control"  placeholder="Ingrese Fecha de Cheque" maxlength="10" min="1920-01-01" title="Ingrese una Fecha Valida" required=""/>
                                    </div>
                                </div>                                  
                    
                                <div class="col-sm-4">
                                    <label>Monto Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="monto_cheque" id="monto_cheque" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required=""/>
                                    </div>                        
                                </div>
                         </div>       
<!--
                                <div class="col-sm-12">
                                    <label>Cuenta Bancaria</label>
                                    <div class="form-group">
                                        <input type="text" name="cuenta_bancaria" id="cuenta_bancaria" class="form-control" placeholder="Ingrese Cuenta Bancaria" maxlength="25" required=""/>
                                    </div>                        
                                </div>
-->

                             <div class="row">   
                                <div class="col-sm-12">
                                    <label>Cuenta Bancaria</label>
                                    <div class="form-group">                                                    
                                        <select class="form-control selectpicker show-tick" name="cuenta_bancaria" id="cuenta_bancaria" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Cuenta..." required="">                                         
                                            <?php                                                                  
                                                while(($resCuenta = oci_fetch_object($conCuenta)) != false){                                             
                                                    if(isset($_POST["cuenta_bancaria"]) AND $_POST["cuenta_bancaria"]==$resCuenta->COD_BANCO)
                                                        echo '<option value="'.$resCuenta->COD_BANCO.'" selected="">'.$resCuenta->DES_BANCO.' </option>';
                                                    else
                                                        echo '<option data-subtext='.'Cuenta:'.$resCuenta->NO_CUENTA.' value="'.$resCuenta->COD_BANCO.'">'.$resCuenta->DES_BANCO.' </option>';                                                                                       
                                            }?>
                                        </select>                                    
                                    </div>
                                </div>  
                             </div>
                                   
                            </div>
                            
                            <hr />                            
                            
                            <div class="float-right">                              
                            <button type="submit" name="cheque" value="cheque" id="cheque" class="btn btn-success"><i class="fa fa-save"></i> Grabar</button>
                            <button type="submit" class="btn btn-info" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            </div>
                    </div>
                                  
              </form>
              
            </div>
          </div>
        </div>               
<!-- Fin Agregar Cheque-->
        
  <!-- Modal Editar Cheque-->
        <div class="modal fade" id="ediCheque" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edición de Datos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">              
              
              <form name="frmcheque" id="frmcheque" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
              
                            <input type="hidden" name="usuario" id="usuario" value="<?php echo $usuario; ?>" class="form-control" readonly=""/>
                            <input type="hidden" name="cod_registro" id="cod_registro" value="cod_registro" class="form-control" readonly=""/>
                            <input type="hidden" name="cod_correl" id="cod_correl" value="cod_correl" class="form-control" readonly=""/>                                                        
                            <input type="hidden" name="codigo_banco" id="codigo_banco" value="codigo_banco" class="form-control" readonly=""/>

                            <div class="container">     
                            
                        <div class="row">                                                 
                                <div class="col-sm-4">
                                    <label>No. Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="no_cheque" id="no_cheque" class="form-control" placeholder="Ingrese Número Cheque" maxlength="25" required="" autofocus=""/>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <label>Fecha Cheque</label>
                                    <div class="form-group">                            
                                        <input type="date" name="fecha_cheque" id="fecha_cheque" class="form-control"  placeholder="Ingrese Fecha de Cheque" maxlength="10" min="1920-01-01" title="Ingrese una Fecha Valida" required=""/>
                                    </div>
                                </div>                                  
                    
                                <div class="col-sm-4">
                                    <label>Monto Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="monto_cheque" id="monto_cheque" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required=""/>
                                    </div>                        
                                </div>
                         </div>       
  
                             <div class="row">   
                                <div class="col-sm-12">
                                    <label>Cuenta Bancaria</label>
                                    <div class="form-group">                      
                                        <select class="form-control selectpicker show-tick" name="cuenta_bancaria" id="cuenta_bancaria" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Cuenta..." required="">                                                                                     
                                            <?php                                                      
                                                /*echo '<option  data-subtext='.'Cuenta:'.$resCuenta1->NO_CUENTA.' value="'.$resCuenta1->COD_BANCO.'" selected="">'.$resCuenta1->DES_BANCO.' </option>';*/                                                
                                                
                                                while(($resCuenta1 = oci_fetch_object($conCuenta1)) != false){
                                                    echo '<option  data-subtext='.'Cuenta:'.$resCuenta1->NO_CUENTA.' value="'.$resCuenta1->COD_BANCO.'" selected="">'.$resCuenta1->DES_BANCO.' </option>';                                                                                       
                                            }?>
                                        </select>                         
                                    </div>
                                </div>  
                             </div>                               
                               

                                   
                            </div>
        
                            
                            <hr />                            
                            
                            <div class="float-right">                              
                            <button type="submit" name="echeque" value="echeque" id="echeque" class="btn btn-success"><i class="fa fa-save"></i> Grabar</button>
                            <button type="submit" class="btn btn-info" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            </div>
                    </div>
              </form>
              
            </div>
          </div>
        </div>       
        
  <!-- Modal Anular Cheque-->
        <div class="modal fade" id="anuCheque" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Anulación de Datos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
              
              <!--<form name="form1" id="form1" method="post" action="anularRegistro.php">-->
              <form name="frmanula" id="frmanula" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">                            

              
                            <input type="hidden" name="usuario" id="usuario" value="<?php echo $usuario; ?>" readonly=""/>
                            <input type="hidden" name="cod_registro" id="cod_registro" value="cod_registro" class="form-control" readonly=""/>
                            <input type="hidden" name="cod_correl" id="cod_correl" value="cod_correl" class="form-control" readonly=""/>
                            
              
                            <div class="container">                        
                        <div class="row">                                                 
                                <div class="col-sm-4">
                                    <label>No. Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="no_cheque" id="no_cheque" class="form-control" placeholder="Ingrese Número Cheque" maxlength="25" readonly="" />
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <label>Fecha Cheque</label>
                                    <div class="form-group">                            
                                        <input type="date" name="fecha_cheque" id="fecha_cheque" class="form-control"  placeholder="Ingrese Fecha de Cheque" maxlength="10" min="1920-01-01" title="Ingrese una Fecha Valida" readonly="" />
                                    </div>
                                </div>                                  
                    
                                <div class="col-sm-4">
                                    <label>Monto Cheque</label>
                                    <div class="form-group">
                                        <input type="text" name="monto_cheque" id="monto_cheque" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" readonly="" />
                                    </div>                        
                                </div>
                         </div>       
  
                             <div class="row">   
                                <div class="col-sm-12">
                                    <label>Cuenta Bancaria</label>
                                    <div class="form-group">                      
                                        <input type="text" name="cuenta" id="cuenta" class="form-control" maxlength="25" readonly="" />                     
                                    </div>
                                </div>  
                             </div>    
                             
                              <div class="row"> 
                                                  
                                <div class="col-sm-12">
                                    <label>Descripción de la Anulación</label>
                                    <div class="form-group">                            
                                        <!--<input type="textarea" name="des_anula" id="des_anula" value="des_anula" class="form-control"  maxlength="150" title="Ingrese Motivo de la Anulación" required="" autofocus=""/>-->                                        
                                        <textarea class="form-control" name="des_anula" id="des_anula" rows="6" style="resize: none;" placeholder="Ingrese Motivo de la Anulación" maxlength="150" required=""></textarea>
                                    </div>
                                </div>
                                </div>
                                                            

                                   
                            </div>
        
                            
                            <hr />                            
                            
                            <div class="float-right">                              
                            <button type="submit" name="anular" value="anular" id="anular " class="btn btn-success"><i class="fa fa-save"></i> Grabar</button>
                            <button type="submit" class="btn btn-info" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            </div>
                    </div>
              </form>
              
            </div>
          </div>
        </div>                  
        
                <!--INICIO BOTON GUARDAR DATOS-->
<!--
                <br />
                <div>
                    <button type="submit" name="modifica" value="modifica" id="modifica" class="btn btn-success btn-block"><i class="fa fa-save fa-lg"></i> Grabar Cambios</button>
                </div>
-->
                <!--FIN BOTON GUARDAR DATOS-->                         
            </form>
        </div>

        <footer class="container__footer">            
            <p>21 Calle 6-77 Zona 1, Centro Cívico, Palacio Municipal. Ciudad de Guatemala, Guatemala, Centroamérica.</p>
            <p>Powered by I<sup>2</sup>+D Informática</p>               
        </footer> 

        <?php oci_close($con); ?>

    </body>

<script>
$(document).ready(function(){
    $("#ediCheque").on('shown.bs.modal', function(){
        $(this).find('#no_cheque').focus();        
    });
    
    $("input[type=text]").focus(function(){	   
        this.select();
    });    
    
});

$(document).ready(function(){
    $("#agrCheque").on('shown.bs.modal', function(){
        $(this).find('#no_cheque').focus();
    });
});

$(document).ready(function(){
    $("#anuCheque").on('shown.bs.modal', function(){
        $(this).find('#des_anula').focus();
    });
});
</script>
    
    
    <script>
        $('#agrCheque').on('show.bs.modal', function(e)
        {
            var no_registro   = $(e.relatedTarget).data('no_registro');            

            $(e.currentTarget).find('input[name="no_registro"]').val(no_registro);            

        });
    </script>        
    
    <script>
        $('#ediCheque').on('show.bs.modal', function(e)
        {
            var cod_registro    = $(e.relatedTarget).data('cod_registro');            
            var cod_correl      = $(e.relatedTarget).data('cod_correl');
            var no_cheque       = $(e.relatedTarget).data('no_cheque');
            var fecha_cheque    = $(e.relatedTarget).data('fecha_cheque');
            var monto_cheque    = $(e.relatedTarget).data('monto_cheque');
            var cuenta_bancaria = $(e.relatedTarget).data('cuenta_bancaria');
            var codigo_banco    = $(e.relatedTarget).data('cuenta_bancaria');

            $(e.currentTarget).find('input[name="cod_registro"]').val(cod_registro);            
            $(e.currentTarget).find('input[name="cod_correl"]').val(cod_correl);
            $(e.currentTarget).find('input[name="no_cheque"]').val(no_cheque);
            $(e.currentTarget).find('input[name="fecha_cheque"]').val(fecha_cheque);
            $(e.currentTarget).find('input[name="monto_cheque"]').val(monto_cheque);
            $(e.currentTarget).find('select[name="cuenta_bancaria"]').val(cuenta_bancaria);
            $(e.currentTarget).find('input[name="codigo_banco"]').val(cuenta_bancaria);    		
                        
        });
    </script>     
    
    <script>
        $('#anuCheque').on('show.bs.modal', function(e)
        {
            var cod_registro    = $(e.relatedTarget).data('cod_registro');            
            var cod_correl      = $(e.relatedTarget).data('cod_correl');
            var no_cheque       = $(e.relatedTarget).data('no_cheque');
            var fecha_cheque    = $(e.relatedTarget).data('fecha_cheque');
            var monto_cheque    = $(e.relatedTarget).data('monto_cheque');
            var cuenta_bancaria = $(e.relatedTarget).data('cuenta_bancaria');            
            var cuenta          = $(e.relatedTarget).data('cuenta');

            $(e.currentTarget).find('input[name="cod_registro"]').val(cod_registro);            
            $(e.currentTarget).find('input[name="cod_correl"]').val(cod_correl);
            $(e.currentTarget).find('input[name="no_cheque"]').val(no_cheque);
            $(e.currentTarget).find('input[name="fecha_cheque"]').val(fecha_cheque);
            $(e.currentTarget).find('input[name="monto_cheque"]').val(monto_cheque);
            $(e.currentTarget).find('select[name="cuenta_bancaria"]').val(cuenta_bancaria);
            $(e.currentTarget).find('input[name="cuenta"]').val(cuenta);
            

            

        });
    </script>      

    <script>    
  
        $(document).ready(function() 
        {$('#dTable').DataTable
            ({
                //dom: 'Bfrtip',
                dom: 'frtip',  
                //dom: 'Bfrltip',                      
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language:{'url': 'js/spanish.json'},  
                responsive: true,      
                order: [[0, "asc"]],
                columnDefs: [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false                    
                    
                },                                                                                                                                     
            ]                             
            });
        });     
    
    </script>
      

    <script>
    
    $("input[data-type='currency']").on({
        keyup: function() {
          formatCurrency($(this));
        },
        blur: function() { 
          formatCurrency($(this), "blur");
        }
    });    
    
    function formatNumber(n) {
      // format number 1000000 to 1,234,567
      return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }    
    
    function formatCurrency(input, blur) {
      // appends $ to value, validates decimal side and puts cursor back in right position. get input value
      var input_val = input.val();
      
      // don't validate empty input
      if (input_val === "") { return; }
      
      // original length
      var original_len = input_val.length;
    
      // initial caret position 
      var caret_pos = input.prop("selectionStart");
        
      // check for decimal
      if (input_val.indexOf(".") >= 0) {
    
        // get position of first decimal this prevents multiple decimals from being entered
        var decimal_pos = input_val.indexOf(".");
    
        // split number by decimal point
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);
    
        // add commas to left side of number
        left_side = formatNumber(left_side);
    
        // validate right side
        right_side = formatNumber(right_side);
        
        // On blur make sure 2 numbers after decimal
        if (blur === "blur") {
          right_side += "00";
        }
        
        // Limit decimal to only 2 digits
        right_side = right_side.substring(0, 2);
    
        // join number by Q.
        input_val = "" + left_side + "." + right_side;
    
      } else {
        // no decimal entered add commas to number remove all non-digits Q
        input_val = formatNumber(input_val);
        input_val = "" + input_val;
        
        // final formatting
        if (blur === "blur") {
          input_val += ".00";
        }
      }
      
      // send updated string to input
      input.val(input_val);
    
      // put caret back in the right position
      var updated_len = input_val.length;
      caret_pos = updated_len - original_len + caret_pos;
      input[0].setSelectionRange(caret_pos, caret_pos);
    }
    
    </script>    
    
    
    <script>
    //Idioma del select picker
    (function($){
        $.fn.selectpicker.defaults = {
            noneSelectedText: 'No hay selección',
            noneResultsText: 'No hay resultados {0}',            
            countSelectedText: 'Seleccionados {0} de {1}',
            maxOptionsText: ['Límite alcanzado ({n} {var} max)', 'Límite del grupo alcanzado({n} {var} max)', ['elementos', 'element']],
            multipleSeparator: ', ',
            selectAllText: 'Seleccionar Todos',
            deselectAllText: 'Desmarcar Todos'
            };
        })(jQuery);
    
    </script>    
        
    <script>
    //Cerrar alerta en 5 segundos         
        setTimeout(function(){
            $('.alert').alert('close'); 
        }, 3000); 
    </script>    
    
   
    
    <script>
    //Campo de texto acepta solo numeros
        function soloNumeros(e) {
            var keynum = window.event ? window.event.keyCode : e.which;
                if ((keynum == 8) || (keynum == 46))
                    return true;         
                    return /\d/.test(String.fromCharCode(keynum));
        }      
    </script>

</html>
