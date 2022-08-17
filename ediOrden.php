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
if (isset($_POST['registro'])){
    $no_registro=$_POST['registro'];            
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
         swal({type:'warning', title:'Error al Modificar el Registro...',  text:'ERROR', showConfirmButton: true, confirmButtonColor: "red", confirmButtonText:'Aceptar', timer: 1000},
         function(){        
            location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }   
      
        function alerta_acepta()
        {
          swal({type:'success', title:'El Registro Fue Modificado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
         function(){        
            location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }
    
    </script>  
	
</head>
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
//---------------------------------------------------------------------------------------------------------------------------

    $fact = date("d/m/Y");
    $fmax = date("Y-m-d");
    
    if (isset($_GET['noRegistro'])){
        $no_registro = base64_decode($_GET['noRegistro']);           
    }    
    
    if (isset($_GET['cod_registro'])){
        $no_registro = base64_decode($_GET['cod_registro']);           
    }
    
    if (isset($_GET['registro'])){
        $no_registro = base64_decode($_GET['registro']);           
    }          
        
    $sqlConsulta  = "SELECT * FROM TB_PAGA_CH_PROVEEDOR
                     WHERE cod_registro = " . $no_registro;
    $conConsulta = oci_parse($con,$sqlConsulta);
    oci_execute($conConsulta);
    $resConsulta = oci_fetch_object($conConsulta);
    
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
    $monto_cur      = number_format($resConsulta->MONTO_CUR,2,".",",");
    $monto_orden    = number_format($resConsulta->MONTO_ORDEN,2,".",",");
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
        $monto_cur      = $_POST['monto_cur'];
        $monto_cur1     = str_replace(",","",$monto_cur);
        
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
        //$cod_tipo       = 5;
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
                         NO_DAJ                 = ".  $no_daj . ",
                         MONTO_CUR              = ".  $monto_cur1 ."
                         WHERE COD_REGISTRO     = ".  $registro;
                         
        
        /*$file = fopen($_SERVER['DOCUMENT_ROOT'].'/fredy/Pagaduria/archivo.txt',"w");
        fwrite($file, $sqlModifica);        
        fclose($file);*/                                                 
             
        $conModifica = oci_parse($con,$sqlModifica);
        $r = oci_execute($conModifica);
           
        if (!$r) {
            echo '<script> alerta_error(); </script>';                            
        }else{
            echo '<script> alerta_acepta(); </script>';
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
        
        <!-- jQuery 3.5.1 JS -->
        <script src="js/jquery.min.js"></script>
        
        <!-- Popper 1.16.1 JS -->
        <script src="js/popper.min.js"></script>        
             
        <!-- Bootstrap 4.5.2 JS -->     
        <script src="js/bootstrap.min.js"></script>
        
        <!-- Select Bootstrap 1.13.14 JS -->               
        <script src="js/bootstrap-select.min.js"></script>
        
        
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
                <h1 class="titulo--interno">Modificación de Ordenes</h1>
                
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
                        <!--<div class="card-header"><i class="fa fa-list-alt fa-lg"></i> <strong>Datos CUR</strong>-->
                        <!--<button type="submit" name="ingreso" value="ingreso" id="ingreso" class="btn btn-success btn-block"><i class="fa fa-floppy-o fa-lg"></i> Grabar Registro</button>-->
                        <!--<div class="card-header"><i class="fa fa-fw fa-list-alt"></i> <strong>Agregar Personas</strong><a href="agrega_contrato.php" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-plus-circle"></i> Agregar Contrato</a></div>-->
                        
                        <!--<div class="card-header"><i class="fa fa-fw fa-list-alt"></i> <strong>Agregar Personas</strong><a href="agrega_contrato.php" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-plus-circle"></i> Agregar Contrato</a></div>-->
                        
                        <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-list-alt fa-stack-1x fa-inverse"></i></span><strong>Datos CUR</strong><a href="tblOrden.php?usuario=<?php echo base64_encode($usuario); ?>" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-search"></i> Consultar Orden de Compra</a></div>

                        <div class="card-body"> 
                        
                        <input type="hidden" name="registro" id="registro" value="<?php echo $no_registro; ?>" readonly=""/>
                        <input type="hidden" name="usuario" id="usuario" value="<?php echo $usuario; ?>" readonly=""/>                            
                                                
                            <div class="row">                        
                                <div class="col-sm-3">                                    
                                </div>
                    
                                <div class="col-sm-3">                                
                                    <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="radio" class="check" name="tipo_cur" id="tipo_cur" value="CC" <?php if(!empty($tipo_cur)) if($tipo_cur=='CC') echo 'checked'; ?> required=""/>
                                    CUR Contable
                                    </label>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3">
                                    <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="radio" class="check" name="tipo_cur" id="tipo_cur" value="CP" <?php if(!empty($tipo_cur)) if($tipo_cur=='CP') echo 'checked'; ?> required=""/>
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
                                        <input type="text" name="cur_actual" id="cur_actual" value="<?php if(empty($cur_actual)){echo '0';} else {echo $cur_actual;} ?>" class="form-control" placeholder="Ingrese CUR" maxlength="25" onkeypress="return soloNumeros(event);" autofocus="" required="" readonly=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">
                                    
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_actual" id="anio_cur_actual" value="<?php if(empty($anio_cur_actual)){echo '0';} else {echo $anio_cur_actual;}; ?>" class="form-control" placeholder="Ingrese Año" maxlength="4" onkeypress="return soloNumeros(event);" required="" readonly=""/>
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
                                        <input type="text" name="cur_nuevo" id="cur_nuevo" value="<?php if(!empty($cur_nuevo)) echo $cur_nuevo; ?>" class="form-control" placeholder="Ingrese CUR Actual" maxlength="25" onkeypress="return soloNumeros(event);" autofocus="" required=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">
                                    
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_nuevo" id="anio_cur_nuevo" value="<?php if(!empty($anio_cur_nuevo)) echo $anio_cur_nuevo; ?>" class="form-control" placeholder="Ingrese Año Actual" maxlength="4" onkeypress="return soloNumeros(event);" required=""/>
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
                                        <input type="date" name="fecha_cur" id="fecha_cur" value="<?php if(!empty($fecha_cur1)) echo $fecha_cur1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-5">        
                                    <div class="form-group">
                                        <input type="text" name="monto_cur" id="monto_cur" value="<?php if(!empty($monto_cur)) echo $monto_cur; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required="" />
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

                                <!--if(empty($no_daj)){echo "0";} else {echo echo $no_daj;}-->
                               <!-- if(!empty($no_daj)) echo $no_daj-->
                            
                                <div class="col-sm-7">                                    
                                    <div class="form-group">
                                        <input type="text" name="no_daj" id="no_daj" value="<?php if(empty($no_daj)){echo '0';} else {echo $no_daj;} ?>" class="form-control" placeholder="Ingrese No. DAJ" maxlength="25" onkeypress="return soloNumeros(event);" autofocus="" required="" readonly=""/>
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
                                    <input type="text" name="no_orden" id="no_orden" value="<?php if(empty($no_orden)){echo '0';} else {echo $no_orden;} ?>" class="form-control" placeholder="Ingrese No. de Orden" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Orden</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_orden" id="fecha_orden" value="<?php if(!empty($fecha_orden1)) echo $fecha_orden1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                </div>
                            </div>                              
                    
                            <div class="col-sm-3">
                                <label>Monto de Orden</label>
                                <div class="form-group">
                                    <input type="text" name="monto_orden" id="monto_orden" value="<?php if(!empty($monto_orden)) echo $monto_orden; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" onblur="enviarTexto();" required="" />
                                </div>                        
                            </div> 
                    
                            <div class="col-sm-3">
                                <label>No. de Solped</label>
                                <div class="form-group">
                                    <input type="text" name="no_solped" id="no_solped" value="<?php if(empty($no_solped)){echo '0';} else {echo $no_solped;} ?>" class="form-control" placeholder="Ingrese el No. de Solped" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div>
                  
                       </div>                        
                              
               
                        <div class="row">     
                            <div class="col-sm-9">
                                <label>Descripción de la Orden</label>
                                <div class="form-group">
                                    <input type="text" name="des_orden" id="des_orden" value="<?php if(!empty($des_orden)) echo $des_orden; ?>" class="form-control" placeholder="Ingrese Descripción de la Orden" maxlength="150" required=""/>
                                </div>                        
                            </div>    


                            <div class="col-sm-3">
                                <label>Tipo Cuenta</label>
                                <div class="form-group">             
                                                  
                                    <select class="form-control selectpicker show-tick" name="cod_tipo" id="cod_tipo" data-show-subtext="false" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Tipo..." required="">                               
                                        
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
                                    <select class="form-control selectpicker show-tick" name="cod_proveedor" id="cod_proveedor" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Proveedor..." required="">                                         
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
                                    <input type="text" name="fuente" id="fuente" value="<?php if(empty($fuente)){echo '0';} else {echo $fuente;} ?>" class="form-control" placeholder="Ingrese Fuente" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>
                            </div>
                    
                            <div class="col-sm-6">
                                <label>Renglón Presupuestario</label>
                                <div class="form-group">
                                    <input type="text" name="renglon" id="renglon" value="<?php if(empty($renglon)){echo '0';} else {echo $renglon;} ?>" class="form-control" placeholder="Ingrese Renglón" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div>                    
                        </div>    
                        
                         <div class="row">   
                            <div class="col-sm-12">
                                <label>Dependencia</label>
                                <div class="form-group">
                                                
                                    <select class="form-control selectpicker show-tick" name="cod_dependencia" id="cod_dependencia" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Dependencia..." required="">                                         
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
                                    <input type="text" name="no_factura" id="no_factura" value="<?php if(empty($no_factura)){echo '0';} else {echo $no_factura;} ?>" class="form-control" placeholder="Ingrese No. de Factura" maxlength="25" onkeyup="javascript:this.value=this.value.toUpperCase();" required=""/>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Factura</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_factura" id="fecha_factura" value="<?php if(!empty($fecha_factura1)) echo $fecha_factura1; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                </div>
                            </div>                                   
                    
                            <div class="col-sm-3">
                                <label>Monto de Factura</label>
                                <div class="form-group">
                                    <input type="text" name="monto_factura" id="monto_factura" value="<?php if(!empty($monto_factura)) echo $monto_factura; ?>"class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required="" readonly=""/>
                                </div>                        
                            </div> 
                            
                            <div class="col-sm-3">                       
                                
                            </div>                    
                                                  
                        </div>
                    </div> 
                </div>    
                <!--FIN DATOS DE LA FACTURA-->
        
                <!--INICIO BOTON GUARDAR DATOS-->
                <br />
                <div>
                    <button type="submit" name="modifica" value="modifica" id="modifica" class="btn btn-success btn-block"><i class="fa fa-save fa-lg"></i> Grabar Cambios</button>
                </div>
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
    
    <script>
        //Copiar texto a otro input
        function enviarTexto(){
            var texto=document.getElementById("monto_orden").value;
            document.getElementById("monto_factura").value=texto;
    
        }      
    </script>      

</html>
