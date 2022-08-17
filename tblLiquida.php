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
            location.href = 'tblLiquida.php?usuario='+'<?php echo base64_encode($usuario); ?>';
         });     
        }   
      
        function alerta_acepta()
        {
         swal({type:'success', title:'El Registro Fue Grabado...', text:'INFORMACION', showConfirmButton: true, confirmButtonColor: "green", confirmButtonText:'Aceptar', timer: 1000},
         function(){
            location.href = 'tblLiquida.php?usuario='+'<?php echo base64_encode($usuario); ?>';
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
    
    $sqlRevision = "SELECT a.cod_registro, a.cur_actual||'-'||anio_cur_actual cur_actual, TO_CHAR(a.fecha_cur,'DD/MM/YYYY') fecha_cur,  
                    a.no_orden, TO_CHAR(a.fecha_orden,'DD/MM/YYYY') fecha_orden, a.no_solped, 
                    d.des_proveedor, TRIM(TO_CHAR(a.monto_cur,'999,999,990.00')) monto_cur,
                    COUNT(*) cantidad, COUNT(DECODE(b.estado,'EN',b.estado)) entregado, NVL(COUNT(*),0)-NVL(COUNT(DECODE(b.estado,'EN',b.estado)),0) pendiente, 
                    a.cant_folio, TO_CHAR(a.fecha_liquidacion,'DD/MM/YYYY') fecha_liquidacion, DECODE(a.fecha_liquidacion,NULL,'NO','SI') liquidado
                    FROM TB_PAGA_CH_PROVEEDOR a
                    INNER JOIN TB_PAGA_DET_CHEQUE b
                    ON a.cod_registro = b.cod_registro
                    INNER JOIN TB_PAGA_CAT_TIPO c
                    ON a.cod_tipo = c.cod_tipo
                    INNER JOIN TB_PAGA_CAT_PROVEEDOR d
                    ON a.cod_proveedor = d.cod_proveedor
                    WHERE b.estado !='AN'
                    GROUP BY a.cod_registro, a.cur_actual||'-'||anio_cur_actual, TO_CHAR(a.fecha_cur,'DD/MM/YYYY'),  
                    a.no_orden, TO_CHAR(a.fecha_orden,'DD/MM/YYYY'), a.no_solped, d.des_proveedor, 
                    TRIM(TO_CHAR(a.monto_cur,'999,999,990.00')), a.cant_folio, a.fecha_liquidacion";
    $conRevision = oci_parse($con,$sqlRevision);
    oci_execute($conRevision);
    
    
    if(isset($_POST['liquidar'])){        
        
        $cod_registro= $_POST['cod_registro'];        
        $can_folio   = $_POST['can_folio'];
        $fec_liquida = $_POST['fec_liquida'];     
        $fec_liquida1= date("d/m/Y", strtotime($fec_liquida));     
        $fec_liquida1= "TO_DATE('" . $fec_liquida1 . "','DD/MM/YYYY')";
        
        $sqlLiquida  = "UPDATE TB_PAGA_CH_PROVEEDOR
                        SET CANT_FOLIO    = ". $can_folio .",                        
                        FECHA_LIQUIDACION = ". $fec_liquida1 ."
                        WHERE COD_REGISTRO= ". $cod_registro;
        
        /*
        echo $sqlLiquida;
        $file = fopen("archivo.txt", "w");
        fwrite($file,$sqlLiquida . PHP_EOL);
        fclose($file);*/                                   
        
        
        $conLiquida = oci_parse($con,$sqlLiquida);
        $r = oci_execute($conLiquida);        
        
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
        <title>Liquidación de Ordenes</title>

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
                <h1 class="titulo--interno">Liquidación de Ordenes</h1>
                
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
        <!--<div class="container">-->
        <div class="col-sm-12">     
                  

             <!--INICIO BUSQUEDA DE DATOS-->
             <div class="card">
               
                <div class="card-header"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-search fa-stack-1x fa-inverse"></i></span><strong>Liquidación de Datos</strong></div>                

                <div class="card-body">
                
                    <!--INICIO DE TABLA-->                                                            
                        <?php                        
                            echo '<table id="dTable" class="table table-striped table-bordered table-hover dt-boostrap" style="width:100%">
                                  <thead>
                                    <tr>                   
                                        <th>No.</th>
                                        <th>CUR Actual</th>
                                        <th>Fecha CUR</th>                                                                                
                                        <th>No. Orden</th>
                                        <th>Fecha Orden</th>                                        
                                        <th>No. Solped</th>                                        
                                        <th>Proveedor</th>
                                        <th>Monto Cur</th>                                                                                                                        
                                        <th>Cantidad Cheques</th>
                                        <th>Cheques Entregados</th>
                                        <th>Cheques Pendientes</th>                                                                                
                                        <th>No. Folios</th>
                                        <th>Fecha Liquidación</th>
                                        <th>Liquidar</th>                                        
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
                                            echo $resRevision->CUR_ACTUAL;
                                            $cur_act = $resRevision->CUR_ACTUAL;
                                        echo '</td>';     
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->FECHA_CUR;
                                            $fec_cur = $resRevision->FECHA_CUR;
                                        echo '</td>';                                        
                                        echo '<td style="text-align: center;">'; 
                                        echo $resRevision->NO_ORDEN;
                                            $no_orden = $resRevision->NO_ORDEN;    
                                        echo '</td>';
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->FECHA_ORDEN;
                                            $fec_orden = $resRevision->FECHA_ORDEN;
                                        echo '</td>';                                                                 
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->NO_SOLPED;  
                                            $no_solped = $resRevision->NO_SOLPED;
                                        echo '</td>';
                                        echo '<td style="text-align: left;">'; 
                                            echo $resRevision->DES_PROVEEDOR;
                                            $proveedor = $resRevision->DES_PROVEEDOR;                                         
                                        echo '</td>';                                        
                                        echo '<td style="text-align: right;">'; 
                                            echo $resRevision->MONTO_CUR;  
                                            $monto_cur = $resRevision->MONTO_CUR;                                         
                                        echo '</td>';
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->CANTIDAD;                                                                                        
                                        echo '</td>';
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->ENTREGADO;                                                                                        
                                        echo '</td>';                                        
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->PENDIENTE;                                                                                        
                                        echo '</td>';
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->CANT_FOLIO;                                                                                        
                                        echo '</td>';                    
                                        echo '<td style="text-align: center;">'; 
                                            echo $resRevision->FECHA_LIQUIDACION;                                                                                        
                                        echo '</td>';                                                            
                                        echo '<td style="text-align: center;">';    
                                            if ($resRevision->PENDIENTE>0){
                                                echo "<a data-toggle='modal' data-target='#liquidacion' title='Liquidar' class='btn btn-outline-secondary btn-sm disabled'><i class='fa fa-ban'></i></a>";                                                                                                
                                            }elseif($resRevision->LIQUIDADO=="SI"){
                                                echo "<a data-toggle='modal' data-target='#liquidacion' title='Liquidar' class='btn btn-outline-secondary btn-sm disabled'><i class='fa fa-ban'></i></a>";   
                                            }else{                                                
                                                echo "<a data-toggle='modal' data-target='#liquidacion' 
                                                         data-cod_registro='$cod_registro' 
                                                         data-cur_act='$cur_act' 
                                                         data-fec_cur='$fec_cur'
                                                         data-no_orden='$no_orden'
                                                         data-fec_orden='$fec_orden'
                                                         data-no_solped='$no_solped'
                                                         data-proveedor='$proveedor'
                                                         data-monto_cur='$monto_cur'
                                                         title='Liquidar' class='btn btn-outline-success btn-sm'><i class='fa fa-edit'></i></a>";
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
        <div class="modal fade" id="liquidacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Liquidación de Ordenes</h5>
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
                                    <label>CUR Actual</label>
                                    <div class="form-group">
                                        <input type="text" name="cur_act" id="cur_act" value="cur_act" class="form-control" maxlength="25" readonly=""/>
                                    </div>
                                </div>
                    
                                <div class="col-sm-3">
                                    <label>Fecha CUR</label>
                                    <div class="form-group">
                                        <input type="text" name="fec_cur" id="fec_cur" value="fec_cur" class="form-control" maxlength="25" readonly=""/>
                                    </div>                        
                                </div> 
                    
                                <div class="col-sm-3">
                                    <label>No. Orden</label>
                                    <div class="form-group">
                                        <input type="text" name="no_orden" id="no_orden" value="no_orden" class="form-control" maxlength="25" readonly=""/>
                                    </div>                        
                                </div>                  
                    
                                <div class="col-sm-3">                        
                                    <label>Fecha Orden</label>
                                    <div class="form-group">
                                        <input type="text" name="fec_orden" id="fec_orden" value="fec_orden" class="form-control" maxlength="25" readonly=""/>
                                    </div>
                                </div>                    
                            </div>        
                            
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>No. Solped</label>
                                    <div class="form-group">                            
                                        <input type="text" name="no_solped" id="no_solped" value="no_solped" class="form-control"  maxlength="25" readonly=""/>
                                    </div>
                                </div>                  
                                
                                <div class="col-sm-6">
                                    <label>Proveedor</label>
                                    <div class="form-group">                            
                                        <input type="text" name="proveedor" id="proveedor" value="proveedor" class="form-control"  maxlength="25" readonly=""/>
                                    </div>
                                </div>                                    

                                <div class="col-sm-3">
                                    <label>Monto Cur</label>
                                    <div class="form-group">                            
                                        <input type="text" name="monto_cur" id="monto_cur" value="monto_cur" class="form-control"  maxlength="25" readonly=""/>
                                    </div>
                                </div>     
                                  
                 
                            </div>            
                                                        
                            <div class="row">                        
                                <div class="col-sm-3">
                                    <label>No. Folios</label>
                                    <div class="form-group">                            
                                        <input type="number" name="can_folio" id="can_folio" class="form-control"  maxlength="3" min="1" max="100" onkeypress="return soloNumeros(event);" autofocus="" required="" />
                                    </div>
                                </div>                  
                                
                                <div class="col-sm-3">
                                    <label>Fecha Liquidación</label>
                                    <div class="form-group">                            
                                        <!--<input type="date" name="fec_liquida" id="fec_liquida" class="form-control"  maxlength="25" required=""/>-->
                                        <input type="date" name="fec_liquida" id="fec_liquida" class="form-control"  maxlength="10" min="1920-01-01" max="<?php echo $fmax; ?>" title="Ingresa una Fecha Valida" required=""/>
                                    </div>
                                </div>                 
                            </div>                                 
                            

                            <hr />
                            <div class="float-right">                              
                            <button type="submit" name="liquidar" value="liquidar" id="liquidar" class="btn btn-success"><i class="fa fa-save"></i> Grabar</button>
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
    $("#liquidacion").on('shown.bs.modal', function(){
        $(this).find('#can_folio').focus();
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
            ]                             
            });
        });     
    
    </script>
    
    <script>
        $('#liquidacion').on('show.bs.modal', function(e)
        {
            var cod_registro = $(e.relatedTarget).data('cod_registro');
            var cur_act      = $(e.relatedTarget).data('cur_act');
            var fec_cur      = $(e.relatedTarget).data('fec_cur');
            var no_orden     = $(e.relatedTarget).data('no_orden');            
            var fec_orden    = $(e.relatedTarget).data('fec_orden');
            var no_solped    = $(e.relatedTarget).data('no_solped');
            var proveedor    = $(e.relatedTarget).data('proveedor');
            var monto_cur  = $(e.relatedTarget).data('monto_cur');            

            $(e.currentTarget).find('input[name="cod_registro"]').val(cod_registro);
            $(e.currentTarget).find('input[name="cur_act"]').val(cur_act);
            $(e.currentTarget).find('input[name="fec_cur"]').val(fec_cur);
            $(e.currentTarget).find('input[name="no_orden"]').val(no_orden);
            $(e.currentTarget).find('input[name="fec_orden"]').val(fec_orden);            
            $(e.currentTarget).find('input[name="no_solped"]').val(no_solped);                        
            $(e.currentTarget).find('input[name="proveedor"]').val(proveedor);
            $(e.currentTarget).find('input[name="monto_cur"]').val(monto_cur);

        });
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
