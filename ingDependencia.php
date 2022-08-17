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
         swal({type:'warning', title:'Error al Grabar el Registro...',  text:'ADVERTENCIA', showConfirmButton: true, confirmButtonColor: "orange", confirmButtonText:'Aceptar', timer: 1000},
         function(){
            location.href = 'ingDependencia.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }   
      
        function alerta_acepta()
        {
         swal({type:'success', title:'El Registro Fue Grabado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
         function(){
            location.href = 'ingOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
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
    
    if(isset($_POST['dependencia'])){
                
        $sqlMax ="SELECT NVL(MAX(COD_DEPENDENCIA),0)+1 correlativo
                  FROM TB_PAGA_CAT_DEPENDENCIA";
        
        $conMax = oci_parse($con,$sqlMax);
        oci_execute($conMax); 
        $resMax = oci_fetch_object($conMax);    
        $correlativo = $resMax->CORRELATIVO;
        
        $nom_dependencia = $_POST['nom_dependencia'];                         
        
        $cadena = "" . $correlativo . ",'" .$nom_dependencia. "'";               
               
        $sqlInserta = "INSERT INTO TB_PAGA_CAT_DEPENDENCIA
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
    
    oci_close($con);
    
?>

<!DOCTYPE HTML>
<html lang="es">

    <head>
    
        <meta http-equiv="content-type" content="text/html" charset="UTF-8"/>	            
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Agregar Dependencia</title>

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
        .login-form {
            width: 600px;
            margin: 20px auto;
        }
        .login-form form {        
            margin-bottom: 15px;
            background: #f7f7f7;
            box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.5);
            border: 1px solid #cdcdcd;
            padding: 30px;
        }
        .login-form h2 {
            margin: 0 0 15px;
        }
        .form-control, .login-btn {
            border-radius: 2px;
        }
        .input-group-prepend .fa {
            font-size: 18px;
        }
        .login-btn {
            font-size: 15px;
            font-weight: bold;
          	min-height: 40px;
        }
        .social-btn .btn {
            border: none;
            margin: 10px 3px 0;
            opacity: 1;
        }
        .social-btn .btn:hover {
            opacity: 0.9;
        }
        .social-btn .btn-secondary, .social-btn .btn-secondary:active {
            background: #507cc0 !important;
        }
        .social-btn .btn-info, .social-btn .btn-info:active {
            background: #64ccf1 !important;
        }
        .social-btn .btn-danger, .social-btn .btn-danger:active {
            background: #df4930 !important;
        }
        .or-seperator {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #ccc;
        }
        .or-seperator i {
            padding: 0 10px;
            background: #f7f7f7;
            position: relative;
            top: -11px;
            z-index: 1;
        }

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
                <h1 class="titulo--interno">Agregar Dependencia</h1>
                
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
                        
                        
            <div class="card">
               
                <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-list fa-stack-1x fa-inverse"></i></span><strong>Dependencias</strong></div>

                <div class="card-body">
                
               <div class="login-form">
                    
                    <form name="frmingreso" id="frmingreso" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">                           
                        
                     <input type="hidden" class="form-control" name="usuario" id="usuario" value="<?php echo $usuario; ?>" readonly=""/>
                        
                        <span>Nombre de Dependencia: </span>
		                <div class="form-group">
                            <input type="text" class="form-control" name="nom_dependencia" id="nom_dependencia" placeholder="Ingrese Nombre de Dependencia" maxlength="150" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus="" required="" />
                        </div>
                   
                 <!--INICIO BOTON GUARDAR DATOS-->   
                <div>
                    <button type="submit" name="dependencia" value="dependencia" id="dependencia" class="btn btn-success btn-block"><i class="fa fa-save"></i> Guardar Dependencia</button>                    
                </div>    
                <!--FIN BOTON GUARDAR DATOS-->        
                                 
                </div> 
                </div> 
            </div>     
                                         
            </form>
        </div>

        <footer class="container__footer">            
            <p>21 Calle 6-77 Zona 1, Centro Cívico, Palacio Municipal. Ciudad de Guatemala, Guatemala, Centroamérica.</p>
            <p>Powered by I<sup>2</sup>+D Informática</p>           
        </footer>
    </body>
    
    
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
