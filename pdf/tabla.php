<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Content-Type: text/html; charset=utf-8');
header("Pragma: no-cache");

mb_internal_encoding('UTF-8'); 
mb_http_output('UTF-8');

$fhoy    = date("d/m/Y h:i A");
$fhoy1    = date("dmYhiA");
$nomArchivo = 'ListadoCheques_'.$fhoy1.'.pdf';

function obtener_datos(){
    
    include "../config/config.php";

    $tabla='';
                   
    $sqlArchivo = "SELECT a.cod_registro REGISTRO, a.cur_actual||'-'||anio_cur_actual CUR, b.nit_proveedor NIT, b.des_proveedor PROVEEDOR, a.no_factura FACTURA, 
                   TO_CHAR(a.fecha_factura,'DD/MM/YYYY') FECHA, TRIM(TO_CHAR(a.monto_cur,'999,999,990.00')) MONTO, a.monto_cur SUMAR
                   FROM TB_PAGA_CH_PROVEEDOR a
                   INNER JOIN TB_PAGA_CAT_PROVEEDOR b
                   ON A.COD_PROVEEDOR = B.COD_PROVEEDOR
                   WHERE a.estado = 'AU'
                   AND a.correo = 'X'
                   AND TRUNC(a.fecha_autoriza) = TRUNC(SYSDATE)
                   ORDER BY a.fecha_factura";                   
    
    $conArchivo = oci_parse($con,$sqlArchivo);
    oci_execute($conArchivo);
    
    $total = 0;
    
    while(($resArchivo = oci_fetch_object($conArchivo)) != false){
        $reg       = $resArchivo->REGISTRO;
        $cur       = $resArchivo->CUR;
        $nit       = $resArchivo->NIT;
        $proveedor = $resArchivo->PROVEEDOR;               
        $factura   = $resArchivo->FACTURA;        
        $fecha     = $resArchivo->FECHA;
        $monto     = $resArchivo->MONTO;
        $suma      = $resArchivo->SUMAR;


        $tabla .='<tr>
                    <td style="text-align: center;">' . $cur . '</td>
                    <td style="text-align: center;">' . $nit . '</td>                                       
                    <td style="text-align: left;">  ' . $proveedor . '</td>                    
                    <td style="text-align: center;">' . $factura . '</td>
                    <td style="text-align: center;">' . $fecha . '</td>
                    <td style="text-align: right;"> ' . $monto . '</td>                                                            
                  </tr>';
             
                $sqlGenera = "UPDATE TB_PAGA_CH_PROVEEDOR
                              SET CORREO = 'G'                      
                              WHERE COD_REGISTRO=".$reg;                               
                                                           
                $conGenera = oci_parse($con,$sqlGenera);
                oci_execute($conGenera);
        

        $total = $total + $suma;
        //$english_format_number = number_format($n�mero, 2, '.',',');                
                  
    }    
        $tabla .='<tfoot>                    
                    <tr>    
                    <th></th>
                    <th></th> 
                    <th></th>
                    <th></th> 
                    <th style="text-align: right; font-weight: bold;">Total</th>                    
                    <th style="text-align: right; font-weight: bold;">' . number_format($total,2,'.',',') . '</th>                    
                    </tr>
                  </tfoot>';
    
	   return $tabla;                   
       
}

require_once('tcpdf_include.php');

//Crear un nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//Configurar la informaci�n del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Fredy Padilla');
$pdf->SetTitle('Listado de Cheques');
$pdf->SetSubject('Generaci�n de Listado');
$pdf->SetKeywords('TCPDF, PDF, Listado, Cheques');

//Eliminar encabezado y pie de p�gina predeterminado
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//Establecer datos de encabezado predeterminados
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

//Establecer fuentes de encabezado y pie de p�gina
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'', PDF_FONT_SIZE_DATA));

//Establecer fuente monoespaciada predeterminada
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Establecer m�rgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetTopMargin(5);
$pdf->SetFooterMargin(5);
$pdf->SetLeftMargin(10);
$pdf->SetRightMargin(10);

//Establecer saltos de p�gina autom�ticos
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//Establecer el factor de escala de la imagen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//Establecer algunas cadenas dependientes del idioma (opcional)
if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
	require_once(dirname(__FILE__).'/lang/spa.php');
	$pdf->setLanguageArray($l);
}

//Establecer el modo de subconjunto de fuentes predeterminado
$pdf->setFontSubsetting(true);

//Establecer fuente --> freeserif, dejavusans, courier, times, helvetica, 
$pdf->SetFont('helvetica','',8);

//Convertir tama�o de p�gina de pulgadas a milimetros
$ancho_en_plg = 8.5;
$alto_en_plg  = 11;
$ancho_en_mm  = $ancho_en_plg * 25.4; 
$alto_en_mm   = $alto_en_plg  * 25.4;

// Agregar una p�gina --> Orientaci�n, Tama�o P�gina
$pdf->AddPage('P',array($ancho_en_mm, $alto_en_mm));

//Establecer la calidad JPEG
$pdf->setJPEGQuality(100);

// Imagen membrete
$pdf->Image('images/membrete_byn.jpg', 10, 10, 65, 30);


//Configurar contenido para imprimir
$html  ='';
$html .='
    <!DOCTYPE HTML>
    <html>
    <head>
    <style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;}
    </style>	
    </head>
    
    <body>
        <div style="text-align: center; color: black;">
            <h3><b>SISTEMA DE PAGADURIA</b>
            <br/>
            <b>LISTADO DE CHEQUES AUTORIZADOS</b></h3>
        </div>
    
        <div style="text-align: right; color: black;">Fecha: ' . $fhoy . '</div>' . '
        <br/><br/>
    
    	<table>
    			<tr>
    				<th width="15%" style="text-align: center; background-color: #eeeeee;"><b> Cur </b></th>				
    				<th width="10%" style="text-align: center; background-color: #eeeeee;"><b> Nit </b></th>				                                
                    <th width="40%" style="text-align: left;   background-color: #eeeeee;"><b> Proveedor </b></th>
    				<th width="10%" style="text-align: center; background-color: #eeeeee;"><b> Factura </b></th>				
    				<th width="10%" style="text-align: center; background-color: #eeeeee;"><b> Fecha </b></th>				                                
                    <th width="15%" style="text-align: right;  background-color: #eeeeee;"><b> Monto </b></th>                
    			</tr>';
                $html .= obtener_datos();
                    
$html .= '</table>
          </body>
          </html> ';			
          //$pdf->writeHTMLCell(0,0,'','',$html,0,1,0,true,'',true);		  
          $pdf->writeHTML($html);
          $pdf->Output($nomArchivo,'D');
          //$pdf->Output($nomArchivo, 'I');

 oci_close($con);       
            
?>