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
         swal({type:'warning', title:'Error al Anular el Registro...',  text:'ADVERTENCIA', showConfirmButton: true, confirmButtonColor: "orange", confirmButtonText:'Aceptar', timer: 1000},
         function(){
            location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }   
      
        function alerta_acepta()
        {
         swal({type:'success', title:'El Registro Fue Anulado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
         function(){
            location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }
    
    </script>
    
      
	
</head>
</html>

<?php


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
    
    $sqlTipo = "SELECT COD_TIPO, DES_TIPO 
                FROM TB_PAGA_CAT_TIPO
                ORDER BY 1";
    $conTipo = oci_parse($con,$sqlTipo);
    oci_execute($conTipo);       
    
    $sqlRevision = "SELECT a.cod_registro, DECODE(a.tipo_cur,'CC','CONTABLE','CP','PRESUPUESTARIO') tipo_cur, a.cur_actual||'-'||anio_cur_actual cur_actual,
                    a.no_daj, a.no_orden, TO_CHAR(a.fecha_orden,'DD/MM/YYYY') fecha_orden, 
                    TRIM(TO_CHAR(a.monto_cur,'999,999,990.00')) monto_cur, a.no_solped, b.nit_proveedor, b.des_proveedor, a.no_factura, a.des_anulacion, 
                    DECODE(a.fecha_anulacion,NULL,'NO','SI') anulado, DECODE(a.fecha_autoriza,NULL,'NO','SI') autorizado, c.des_tipo tipo_cuenta
                    FROM TB_PAGA_CH_PROVEEDOR a
                    INNER JOIN TB_PAGA_CAT_PROVEEDOR b
                    ON A.COD_PROVEEDOR = B.COD_PROVEEDOR
                    INNER JOIN TB_PAGA_CAT_TIPO c
                    ON A.COD_TIPO = c.COD_TIPO
                    WHERE a.estado = 'OC'
                    ORDER BY 1";
    $conRevision = oci_parse($con,$sqlRevision);
    oci_execute($conRevision);
    
    
    if(isset($_POST['anular'])){        
        
        $usuario = $_POST['usuario'];
        
        $fhoy    = date("d/m/Y h:i A");
        
        $fecha_anula  = "TO_DATE('" . $fhoy . "','DD/MM/YYYY HH:MI AM')";
        $usuario_anula= $usuario;        
        
        $cod_registro= $_POST['cod_registro'];
        $des_anula   = $_POST['des_anula'];   
        
        $sqlAnula  = "UPDATE TB_PAGA_CH_PROVEEDOR
                      SET DES_ANULACION  = '". $des_anula ."',
                      USUARIO_ANULACION  = '". $usuario_anula ."',
                      FECHA_ANULACION    =  ". $fecha_anula ."
                      WHERE COD_REGISTRO =  ". $cod_registro;                         
       
        /*echo $sqlAnula;
        $file = fopen("archivo.txt", "w");
        fwrite($file,$sqlAnula . PHP_EOL);
        fclose($file);*/                                    
        
        $conAnula = oci_parse($con,$sqlAnula);
        $r = oci_execute($conAnula);
        
        
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
        <title>Consulta de Datos</title>

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
        
    <!-- Style to set the size of checkbox -->
    <style>
      input.check {
        width: 25px;
        height: 25px;
        cursor: pointer;
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
                <h1 class="titulo--interno">Consulta de Ordenes</h1>
                
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
        <br />
        <br />
        <br />        
        <!--FIN MENU-->      
        

            
        <!--INICIO DE CONTENEDOR MAESTRO-->        
        <!--<div class="container">-->
        <div class="col-sm-12">     
                  

             <!--INICIO BUSQUEDA DE DATOS-->
             <div class="card">
               
                <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-search fa-stack-1x fa-inverse"></i></span><strong>Consulta de Datos</strong><a href="ingOrden.php?usuario=<?php echo base64_encode($usuario); ?>" class="float-right btn btn-secondary btn-sm"><i class="fa fa-fw fa-plus-circle"></i> Ingreso Orden de Compra</a></div>                

                <div class="card-body">
                
                    <!--INICIO DE TABLA-->                
                        <?php                        
                            echo '<table id="dTable" class="table table-striped table-bordered table-hover dt-boostrap" style="width:100%">
                                  <thead>
                                    <tr>                   
                                        <th>No.</th>                     
                                        <th>Tipo CUR</th>
                                        <th>CUR Actual</th>
                                        <th>No. DAJ</th>                                                                                
                                        <th>No. Orden</th>
                                        <th>Fecha Orden</th>
                                        <th>Monto Cur</th>
                                        <th>No. Solped</th>
                                        <th>NIT</th>
                                        <th>Proveedor</th>
                                        <th>No. Factura</th>                                        
                                        <th>Tipo Cuenta</th>
                                        <th>Editar</th>
                                        <th>Anular</th>
                                        <th>Autorización Cheque</th>
                                    </tr>
                                    </thead>
                                    <tbody>';             
                                    
                                    while(($resRevision = oci_fetch_object($conRevision)) != false){                                                                                
                                        
                                        echo '<tr>';
                                        
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->COD_REGISTRO;
                                            $cod_registro = $resRevision->COD_REGISTRO;
                                        echo '</td>';                        
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->TIPO_CUR;
                                            $tipo_cur = $resRevision->TIPO_CUR;
                                        echo '</td>';     
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->CUR_ACTUAL;
                                            $cur_act = $resRevision->CUR_ACTUAL;
                                        echo '</td>';                                        
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->NO_DAJ;
                                            $no_daj = $resRevision->NO_DAJ;    
                                        echo '</td>';
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->NO_ORDEN;
                                            $no_orden = $resRevision->NO_ORDEN;
                                        echo '</td>';                                                                 
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->FECHA_ORDEN;
                                            $fecha_orden = $resRevision->FECHA_ORDEN;
                                        echo '</td>';
                                        echo '<td style="text-align: right;">'; 
                                            echo $resRevision->MONTO_CUR;                                            
                                        echo '</td>';                                                                                                                    
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->NO_SOLPED;  
                                            $no_solped = $resRevision->NO_SOLPED;           
                                        echo '</td>';                                        
                                        echo '<td style="text-align: right;">'; 
                                            echo $resRevision->NIT_PROVEEDOR;                                            
                                        echo '</td>';
                                        echo '<td style="text-align: left;">'; 
                                            echo $resRevision->DES_PROVEEDOR;                                            
                                        echo '</td>';
                                        echo '<td style="text-align: left;">'; 
                                            echo $resRevision->NO_FACTURA;                                                                                        
                                        echo '</td>';
                                        echo '<td style="text-align: left;">'; 
                                            echo $resRevision->TIPO_CUENTA;                                                                                        
                                        echo '</td>';                                        
                                        echo '<td style="text-align: center;">';
                                            if ($resRevision->ANULADO=="SI"){
                                                    echo '<a href="ediOrden.php?noRegistro='.base64_encode($cod_registro). '&usuario='.base64_encode($usuario).' "  data-toggle="tooltip" data-placement="top" title="Editar Orden de Compra" class="btn btn-outline-secondary btn-sm disabled"><i class="fa fa-edit"></i></a>';
                                            }else{
                                                echo '<a href="ediOrden.php?noRegistro='.base64_encode($cod_registro). '&usuario='.base64_encode($usuario).' "  data-toggle="tooltip" data-placement="top" title="Editar Orden de Compra" class="btn btn-outline-success btn-sm"><i class="fa fa-edit"></i></a>';                                                
                                            }
                                        echo '</td>';       
                                        
                                        echo '<td style="text-align: center;">';    
                                            if ($resRevision->ANULADO=="SI"){
                                                echo "<a data-toggle='modal' data-target='#anulacion' title='Anular Orden de Compra' class='btn btn-outline-secondary btn-sm disabled'><i class='fa fa-ban'></i></a>";
                                            }else{                                                
                                                echo "<a data-toggle='modal' data-target='#anulacion' 
                                                        data-cod_registro='$cod_registro' 
                                                        data-tipo_cur='$tipo_cur' 
                                                        data-no_daj='$no_daj'
                                                        data-no_orden='$no_orden'
                                                        data-no_solped='$no_solped'
                                                        data-fecha_orden='$fecha_orden'                                                        
                                                        title='Anular Orden de Compra' class='btn btn-outline-danger btn-sm'><i class='fa fa-ban'></i></a>";
                                            }        
                                        echo '</td>';   
                                        echo '<td style="text-align: center;">';
                                        if ($resRevision->ANULADO=="SI"){
                                            echo '<input type="checkbox" name="cheque" id="cheque" title="Autorizar Cheque" class="check" disabled="">';
                                        }
                                        else{
                                            echo '<input type="checkbox" name="cheque" id="cheque" title="Autorizar Cheque" class="check" value="'. $cod_registro .'">';
                                        }
                                        echo '</td>';                                          
                                                                                                                                                                     
                                        echo '</tr>'; 
                                        
                                        }             
                                echo '
                                </tbody>    
                            </table>';                
                        ?>                
                        
                        <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#agregaModal"><span class="glyphicon glyphicon-plus"></span> Agregar Sala</button>-->
                    <!--FIN DE TABLA-->
                    
                    
                
                         
                </div>            
            </div>
                <!--FIN BUSQUEDA DE DATOS-->      
            
        </div>
         

        <footer class="container__footer">            
            <p>21 Calle 6-77 Zona 1, Centro Cívico, Palacio Municipal. Ciudad de Guatemala, Guatemala, Centroamérica.</p>
            <p>Powered by I<sup>2</sup>+D Informática</p>              
        </footer>        
        
        <!-- Modal -->
        <div class="modal fade" id="anulacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
              
                            <input type="hidden" name="cod_registro" id="cod_registro" value="cod_registro" class="form-control" readonly=""/>
                            <input type="hidden" name="usuario" id="usuario" value="<?php echo $usuario; ?>" readonly=""/>
              
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>Tipo CUR</label>
                                    <div class="form-group">
                                        <input type="text" name="tipo_cur" id="tipo_cur" value="tipo_cur" class="form-control" maxlength="25" readonly=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3">
                                    <label>No. DAJ</label>
                                    <div class="form-group">
                                        <input type="text" name="no_daj" id="no_daj" value="no_daj" class="form-control" maxlength="25" readonly=""/>
                                    </div>                        
                                </div> 
                    
                                <div class="col-sm-3">
                                    <label>No. Orden</label>
                                    <div class="form-group">
                                        <input type="text" name="no_orden" id="no_orden" value="no_orden" class="form-control" maxlength="25" readonly=""/>
                                    </div>                        
                                </div>                  
                    
                                <div class="col-sm-3">                        
                                    <label>No. Solped</label>
                                    <div class="form-group">
                                        <input type="text" name="no_solped" id="no_solped" value="no_solped" class="form-control" maxlength="25" readonly=""/>
                                    </div>
                                </div>                    
                            </div>        
                            
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>Fecha de Orden</label>
                                    <div class="form-group">                            
                                        <input type="text" name="fecha_orden" id="fecha_orden" value="fecha_orden" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required="" readonly=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3"> 
                                </div> 
                    
                                <div class="col-sm-3">
                                </div>                  
                    
                                <div class="col-sm-3">
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
                            
                            
                            <div class="float-right">                              
                            <button type="submit" name="anular" value="anular" id="anular" class="btn btn-success"><i class="fa fa-save"></i> Grabar</button>
                            <button type="submit" class="btn btn-info" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            </div>
                    </div>
                                  
              </form>
              
            </div>
          </div>
        </div>       
         

        <?php oci_close($con); ?>

    </body>

<script>
$(document).ready(function(){
    $("#anulacion").on('shown.bs.modal', function(){
        $(this).find('#des_anula').focus();
    });
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
                {
                    "targets": [1],
                    "visible": false,
                    "searchable": true                    
                    
                },                
                {
                    "targets": [11],
                    "visible": false,
                    "searchable": true                    
                    
                },                
                                                                                                                                   
            ]                             
            });
        });     
    
    </script>
    
    <script>
        $('#anulacion').on('show.bs.modal', function(e)
        {
            var cod_registro   = $(e.relatedTarget).data('cod_registro');
            var tipo_cur       = $(e.relatedTarget).data('tipo_cur');
            var no_daj         = $(e.relatedTarget).data('no_daj');
            var no_orden       = $(e.relatedTarget).data('no_orden');            
            var no_solped      = $(e.relatedTarget).data('no_solped');            
            var fecha_orden    = $(e.relatedTarget).data('fecha_orden');            
            var des_anula      = $(e.relatedTarget).data('des_anula');

            $(e.currentTarget).find('input[name="cod_registro"]').val(cod_registro);
            $(e.currentTarget).find('input[name="tipo_cur"]').val(tipo_cur);
            $(e.currentTarget).find('input[name="no_daj"]').val(no_daj);
            $(e.currentTarget).find('input[name="no_orden"]').val(no_orden);            
            $(e.currentTarget).find('input[name="no_solped"]').val(no_solped);                        
            $(e.currentTarget).find('input[name="fecha_orden"]').val(fecha_orden);            
            $(e.currentTarget).find('textarea[name="des_anula"]').val(des_anula);

        });
    </script>    
    
    <script>
    $(function(){           
        $(document).on( 'click', '#cheque', function(){
        let vRegistro = $(this).val();    
          
          if( $(this).is(':checked' ) ){        
            var vMarca = 1;
          }
          else{
            var vMarca = 0;        
          }     
           
           var pRegistro = vRegistro;
           var pMarca = vMarca;
           var pUser = '<?php echo $usuario; ?>';         
        
            $.ajax(
               {
                  type: 'post',
                  url: 'cheque.php',
                  data: { 
                    "registro": pRegistro,
                    "marca": pMarca,
                    "usuario": pUser
                  },
                  success: function (response) {
                    if(pMarca==1){                    
                            swal({type:'success', title:'OC Para Cheque...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
                                 function(){
                                    location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';                                    
                                 });                        
                    }else{
                    
                             swal({type:'warning', title:'Registro No Autorizado...',  text:'ADVERTENCIA', showConfirmButton: true, confirmButtonColor: "orange", confirmButtonText:'Aceptar', timer: 1000},
                             function(){
                                location.href = 'tblOrden.php?usuario='+'<?php echo base64_encode($usuario); ?>';
                             });                                             
                    }
                  },
                  error: function () {
                    alert("Error...");
                  }
               }
            );
    
        });
        
    });
    </script>       
   

</html>
