<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/html; charset=utf-8');
header("Pragma: no-cache");

mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');

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
     swal({type:'warning', title:'El Registro Ya Existe...',  text:'ADVERTENCIA', showConfirmButton: true, confirmButtonColor: "orange", confirmButtonText:'Aceptar', timer: 1000},
     function(){
        document.getElementById("cur_actual").focus();
     });     
    }   
  
    function alerta_acepta()
    {
      swal({type:'success', title:'El Registro Fue Grabado...', text:'INFORMACIÓN', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
     function(){
        document.getElementById("cur_actual").focus();
     });     
    }
    
  </script>
  
  
  <script>
      function limpia(){
        
        document.getElementById("cur_actual").value = "";    
        document.getElementById("anio_cur_actual").value = "";
        document.getElementById("cur_nuevo").value = "";
        document.getElementById("anio_cur_nuevo").value = "";
        document.getElementById("fecha_cur").value = "";
        
        document.getElementById("no_orden").value = "";    
        document.getElementById("fecha_orden").value = "";
        document.getElementById("monto_orden").value = "";
        document.getElementById("no_solped").value = "";
        document.getElementById("des_orden").value = "";
        
        $('#cod_tipo').selectpicker('val', 'Seleccione Tipo...');
        $('#cod_proveedor').selectpicker('val', 'Seleccione Proveedor...');
        
        document.getElementById("fuente").value = "";    
        document.getElementById("renglon").value = "";
        document.getElementById("monto_orden").value = "";
        document.getElementById("no_solped").value = "";
        document.getElementById("des_orden").value = "";    
        
        $('#cod_dependencia').selectpicker('val', 'Seleccione Dependencia...');
        
        document.getElementById("no_cheque").value = "";    
        document.getElementById("fecha_cheque").value = "";
        document.getElementById("monto_cheque").value = "";
        document.getElementById("cuenta_bancaria").value = "";
        
        document.getElementById("no_factura").value = "";    
        document.getElementById("fecha_factura").value = "";
        document.getElementById("monto_factura").value = "";
        
        document.getElementById("cur_actual").focus();  
            
      }
  </script>
  

  
	
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

    $fact = date("d/m/Y");
    $fmax = date("Y-m-d");
    
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
    

    if(isset($_POST['ingreso'])){
        
        $fhoy    = date("d/m/Y h:i A");
        $fec     = date("m/d/Y");
        $aniomes = date("Ym", strtotime($fec)); 
        
        $sqlMax ="SELECT NVL(MAX(COD_REGISTRO),0)+1 registro
                  FROM TB_PAGA_CH_PROVEEDOR";
                    
        $conMax = oci_parse($con,$sqlMax);
        oci_execute($conMax); 
        $resMax = oci_fetch_object($conMax);    
        $registro = $resMax->REGISTRO;    
        
        $fecha_graba    = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        $usuario_graba  = "USER";
        $nulo           = 'NULL';
            
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
        $cuenta_bancaria= $_POST['cuenta_bancaria'];    
        
        //Información de la Factura//
        $no_factura     = $_POST['no_factura'];
        $fecha_factura  = $_POST['fecha_factura'];
        $fecha_factura1 = date("d/m/Y", strtotime($fecha_factura));     
        $fecha_factura1 = "TO_DATE('" . $fecha_factura1 . "','DD/MM/YYYY')";        
        $monto_factura  = $_POST['monto_factura'];
        $monto_factura1 = str_replace(",","",$monto_factura);

        
        $cadena = "" . $registro . "," .$cur_actual . "," . $anio_cur_actual . "," . $cur_nuevo . "," . $anio_cur_nuevo . "," . $fecha_cur1 . ",
                   " . $no_orden . "," . $fecha_orden1 . "," . $monto_orden1 . "," . $no_solped . ",'" . $des_orden . "'," . $cod_tipo . "," . $cod_proveedor . "," . $fuente . "," . $renglon . "," . $cod_dependencia . ",
                   " . $no_cheque . "," . $fecha_cheque1 . "," .  $monto_cheque1 . "," . $cuenta_bancaria . ",
                  '" . $no_factura . "'," .  $fecha_factura1 . "," . $monto_factura1 . ",
                   " . $nulo . "," . $nulo . "," . $nulo . "," . $nulo . "," . $nulo . "," . $usuario_graba . "," . $fecha_graba . "," . $nulo . "," . $nulo . "";
               
               
        $sqlInserta = "INSERT INTO TB_PAGA_CH_PROVEEDOR
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
    
 

?>

<!DOCTYPE HTML>
<html lang="es">

    <head>
    
        <meta http-equiv="content-type" content="text/html" charset="UTF-8"/>	            
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Ingreso de Datos</title>

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
        
        
        
        

    </head>

    <body class="container__body">
    
        <header class="container__header">
            <div class="wrapper__logo--interno registro--logo">
                <img class="escudo" src="img/escudo-hdpi.png" alt="Escudo Municipalidad de Guatemala" />
            </div>
                <h1 class="titulo--interno">Pagaduría de Cheque a Proveedores <br/> Ingreso de Datos</h1>
                
            <div class="wrapper__logo--interno">
                <img class="logo" src="img/logo_informatica.png" alt="Logo Dirección de informática" />
            </div>
            
        </header>
        

            
        <!--INICIO DE CONTENEDOR MAESTRO-->        
        <div class="container">
     
        <div class="card-body"> 
            <button name="limpiar" value="limpiar" id="limpiar" class="limpiar float-right btn btn-info" onclick="limpia()"><i class="fa fa-file-text fa-lg"></i> Limpiar Formulario</button>
        </div>    
        <br />
            <!--<form name="form1" id="form1" method="post" action="grabaRegistro.php">-->
            <form name="frmingreso" id="frmingreso" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">        
            <!--FECHA ACTUAL-->            
               <div style="text-align: left; color: black;"><?php echo "Fecha: " . $fact; ?></div>
               
                    <!--INICIO DATOS DEL CUR-->
                    <div class="card">            
                        <!--<div class="card-header"><i class="fa fa-list-alt fa-lg"></i> <strong>Datos CUR</strong>-->
                        <!--<button type="submit" name="ingreso" value="ingreso" id="ingreso" class="btn btn-success btn-block"><i class="fa fa-floppy-o fa-lg"></i> Grabar Registro</button>-->
                        <!--<div class="card-header"><i class="fa fa-fw fa-list-alt"></i> <strong>Agregar Personas</strong><a href="agrega_contrato.php" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-plus-circle"></i> Agregar Contrato</a></div>-->
                        
                        <!--<div class="card-header"><i class="fa fa-fw fa-list-alt"></i> <strong>Agregar Personas</strong><a href="agrega_contrato.php" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-plus-circle"></i> Agregar Contrato</a></div>-->
                        
                        <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-list-alt fa-stack-1x fa-inverse"></i></span><strong>Datos CUR</strong><a href="index.php" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-search"></i> Consultar Datos</a></div>


                        <div class="card-body">     
                        
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>No. CUR Actual</label>
                                    <div class="form-group">
                                        <input type="text" name="cur_actual" id="cur_actual" value="<?php if(!empty($cur_actual)) echo $cur_actual; ?>" class="form-control" placeholder="Ingrese CUR Actual" maxlength="25" onkeypress="return soloNumeros(event);" autofocus="" required=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3">
                                    <label>Año CUR Actual</label>
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_actual" id="anio_cur_actual" value="<?php if(!empty($anio_cur_actual)) echo $anio_cur_actual; ?>" class="form-control" placeholder="Ingrese Año Actual" maxlength="4" onkeypress="return soloNumeros(event);" required=""/>
                                    </div>                        
                                </div> 
                    
                                <div class="col-sm-3">
                                    <label>No. CUR Nuevo</label>
                                    <div class="form-group">
                                        <input type="text" name="cur_nuevo" id="cur_nuevo" value="<?php if(!empty($cur_nuevo)) echo $cur_nuevo; ?>" class="form-control" placeholder="Ingrese CUR Nuevo" maxlength="25" onkeypress="return soloNumeros(event);"/>
                                    </div>                        
                                </div>                  
                    
                                <div class="col-sm-3">                        
                                    <label>Año CUR Nuevo</label>
                                    <div class="form-group">
                                        <input type="text" name="anio_cur_nuevo" id="anio_cur_nuevo" value="<?php if(!empty($anio_cur_nuevo)) echo $anio_cur_nuevo; ?>" class="form-control" placeholder="Ingrese Año Nuevo" maxlength="4" onkeypress="return soloNumeros(event);"/>
                                    </div>
                                </div>                    
                            </div>       
               
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>Fecha Ingreso CUR</label>
                                    <div class="form-group">                            
                                        <input type="date" name="fecha_cur" id="fecha_cur" value="<?php if(!empty($fecha_cur)) echo $fecha_cur; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3"> 
                                </div> 
                    
                                <div class="col-sm-3">
                                </div>                  
                    
                                <div class="col-sm-3">
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
                                    <input type="text" name="no_orden" id="no_orden" value="<?php if(!empty($no_orden)) echo $no_orden; ?>" class="form-control" placeholder="Ingrese No. de Orden" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Orden</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_orden" id="fecha_orden" value="<?php if(!empty($fecha_orden)) echo $fecha_orden; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                </div>
                            </div>                              
                    
                            <div class="col-sm-3">
                                <label>Monto de Orden</label>
                                <div class="form-group">
                                    <input type="text" name="monto_orden" id="monto_orden" value="<?php if(!empty($monto_orden)) echo $monto_orden; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required="" />
                                </div>                        
                            </div> 
                    
                            <div class="col-sm-3">
                                <label>No. de Solped</label>
                                <div class="form-group">
                                    <input type="text" name="no_solped" id="no_solped" value="<?php if(!empty($no_solped)) echo $no_solped; ?>" class="form-control" placeholder="Ingrese el No. de Solped" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div>
                  
                       </div>                        
                              
               
                        <div class="row">     
                            <div class="col-sm-6">
                                <label>Descripción de la Orden</label>
                                <div class="form-group">
                                    <input type="text" name="des_orden" id="des_orden" value="<?php if(!empty($des_orden)) echo $des_orden; ?>" class="form-control" placeholder="Ingrese Descripción de la Orden" maxlength="150" required=""/>
                                </div>                        
                            </div>    

                            <div class="col-sm-6">
                                <label>Tipo</label>
                                <div class="form-group">                            
                                    <select class="form-control selectpicker show-tick" name="cod_tipo" id="cod_tipo" data-show-subtext="false" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Tipo..." required="">                                     
                                        <?php                                                                  
                                            while(($resTipo = oci_fetch_object($conTipo)) != false){
                                                if(isset($_POST["cod_tipo"]) AND $_POST["cod_tipo"]==$resTipo->COD_TIPO)
                                                    echo '<option value="'.$resTipo->COD_TIPO.'" selected="">'.$resTipo->DES_TIPO.' </option>';
                                                else
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
                                            while(($resProv = oci_fetch_object($conProv)) != false){                                             
                                                if(isset($_POST["cod_proveedor"]) AND $_POST["cod_proveedor"]==$resProv->COD_PROVEEDOR)
                                                    echo '<option value="'.$resProv->COD_PROVEEDOR.'" selected="">'.$resProv->DES_PROVEEDOR.' </option>';
                                                else
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
                                    <input type="text" name="fuente" id="fuente" value="<?php if(!empty($fuente)) echo $fuente; ?>" class="form-control" placeholder="Ingrese Fuente" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>
                            </div>
                    
                            <div class="col-sm-6">
                                <label>Renglón Presupuestario</label>
                                <div class="form-group">
                                    <input type="text" name="renglon" id="renglon" value="<?php if(!empty($renglon)) echo $renglon; ?>" class="form-control" placeholder="Ingrese Renglón" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div>                    
                        </div>    
                        
                         <div class="row">   
                            <div class="col-sm-12">
                                <label>Dependencia</label>
                                <div class="form-group">
                                                
                                    <select class="form-control selectpicker show-tick" name="cod_dependencia" id="cod_dependencia" data-show-subtext="true" data-live-search="true" data-style="btn-outline-secondary" title="Seleccione Dependencia..." required="">                                         
                                        <?php                                                                  
                                            while(($resDepen = oci_fetch_object($conDepen)) != false){                                             
                                                if(isset($_POST["cod_dependencia"]) AND $_POST["cod_dependencia"]==$resDepen->COD_DEPENDENCIA)
                                                    echo '<option value="'.$resDepen->COD_DEPENDENCIA.'" selected="">'.$resDepen->DES_DEPENDENCIA.' </option>';
                                                else
                                                    echo '<option value="'.$resDepen->COD_DEPENDENCIA.'">'.$resDepen->DES_DEPENDENCIA.' </option>';                                                                                       
                                        }?>
                                    </select>
                                </div>
                            </div>                              
                         
                         </div>
                                                                 
                        
                    </div>
                </div>
                <!--FIN DATOS DE LA ORDEN DE COMPRA-->        
                
                <!--INICIO DATOS DEL CHEQUE-->                
                <div class="card">            
        
                    <!--<div class="card-header"><i class="fa fa-money fa-lg"></i> <strong>Datos del Cheque</strong></div>-->                    
                    <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-money fa-stack-1x fa-inverse"></i></span><strong>Datos del Cheque</strong></div>
            
                    <div class="card-body">
                            
                        <div class="row">                        
                            <div class="col-sm-3">
                                <label>No. de Cheque</label>
                                <div class="form-group">
                                    <input type="text" name="no_cheque" id="no_cheque" value="<?php if(!empty($no_cheque)) echo $no_cheque; ?>" class="form-control" placeholder="Ingrese No. de Cheque" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Cheque</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_cheque" id="fecha_cheque" value="<?php if(!empty($fecha_cheque)) echo $fecha_cheque; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                </div>
                            </div>                              
                    
                            <div class="col-sm-3">
                                <label>Monto de Cheque</label>
                                <div class="form-group">
                                    <input type="text" name="monto_cheque" id="monto_cheque" value="<?php if(!empty($monto_cheque)) echo $monto_cheque; ?>" class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div> 
                    
                            <div class="col-sm-3">
                                <label>No. Cuenta Bancaria</label>
                                <div class="form-group">
                                    <input type="text" name="cuenta_bancaria" id="cuenta_bancaria" value="<?php if(!empty($cuenta_bancaria)) echo $cuenta_bancaria; ?>" class="form-control" placeholder="Ingrese el No. de Cuenta" maxlength="25" onkeypress="return soloNumeros(event);" required=""/>
                                </div>                        
                            </div>                  
                    
                  
                        </div>
                                          
                    </div>
                </div>
                <!--FIN DATOS DEL CHEQUE-->
        
                <!--INICIO DATOS DE LA FACTURA-->                
                <div class="card">
            
                    <!--<div class="card-header"><i class="fa fa-id-card fa-lg"></i> <strong>Datos de la Factura</strong></div>-->
                    <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-id-card fa-stack-1x fa-inverse"></i></span><strong>Datos de la Factura</strong></div>
    
                    <div class="card-body">

                        <div class="row">                        
                            <div class="col-sm-3">
                                <label>No. de Factura</label>
                                <div class="form-group">
                                    <input type="text" name="no_factura" id="no_factura" value="<?php if(!empty($no_factura)) echo $no_factura; ?>" class="form-control" placeholder="Ingrese No. de Factura" maxlength="25" onkeyup="javascript:this.value=this.value.toUpperCase();" required=""/>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">                        
                                <label>Fecha de Factura</label>
                                <div class="form-group">                            
                                    <input type="date" name="fecha_factura" id="fecha_factura" value="<?php if(!empty($fecha_factura)) echo $fecha_factura; ?>" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                </div>
                            </div>                                   
                    
                            <div class="col-sm-3">
                                <label>Monto de Factura</label>
                                <div class="form-group">
                                    <input type="text" name="monto_factura" id="monto_factura" value="<?php if(!empty($monto_factura)) echo $monto_factura; ?>"class="form-control" maxlength="15" style="text-align: right;" pattern="^\d{1,3}(,\d{3})*(\.\d+)?" value="" data-type="currency" placeholder="0.00" onkeypress="return soloNumeros(event);" required=""/>
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
                    <button type="submit" name="ingreso" value="ingreso" id="ingreso" class="btn btn-success btn-block"><i class="fa fa-save fa-lg"></i> Grabar Registro</button>
                </div>
                <!--FIN BOTON GUARDAR DATOS-->                         
            </form>
        </div>

        <footer class="container__footer">            
            <p>21 Calle 6-77 Zona 1, Centro Cívico, Palacio Municipal. Ciudad de Guatemala, Guatemala, Centroamérica.</p>
            <p>Powered by I2+D Informática</p>            
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

</html>
