<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/html; charset=utf-8');
header("Pragma: no-cache");
mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');

$fact = date("d/m/Y");

session_start();
session_destroy();

?>

<!DOCTYPE HTML>
<html lang="es">

    <head>
    
        <meta http-equiv="content-type" content="text/html" charset="UTF-8"/>	            
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Acceso al Sistema</title>

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
            width: 400px;
            margin: 30px auto;
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
        </style>        
        
    </head>

    <body class="container__body">
    
        <header class="container__header">
            <div class="wrapper__logo--interno registro--logo">
                <img class="escudo" src="img/escudo-hdpi.png" alt="Escudo Municipalidad de Guatemala" />
            </div>
                <h1 class="titulo--interno">Acceso al Sistema<br/>Pagaduría</h1>
                
            <div class="wrapper__logo--interno">
                <img class="logo" src="img/logo_informatica.png" alt="Logo Dirección de informática" />
            </div>
            
        </header>
                
        <!--INICIO DE CONTENEDOR MAESTRO-->        
        <div class="container">                    
                        
            <div style="text-align: left; color: black;"><?php echo "Fecha: " . $fact; ?></div>
            
            <div class="card">
               
                <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-vcard fa-stack-1x fa-inverse"></i></span><strong>Datos del Usuario</strong></div>

                <div class="card-body">
                
                <div class="login-form">
                    <form action="checklogin.php" method="post">
                        <h2 class="text-center"></h2>   
                        <div class="form-group">
        	            <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                <span class="fa fa-user"></span>
                                </span>                    
                            </div>
                            <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Ingrese el Usuario" maxlength="25" autofocus="" required="required" />				
                        </div>
                        </div>
                        
		                <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                                </span>                    
                        </div>
                        <input type="password" class="form-control" name="clave" id="clave" placeholder="Ingrese la Contraseña" maxlength="25" required="required" />				
                        </div>
                        </div>        
                   
                 <!--INICIO BOTON GUARDAR DATOS-->   
                <div>
                    <button type="submit" class="btn btn-info btn-block"><h4><i class="fa fa-key fa-lg"></i> Ingresar</h4></button>
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

</html>
