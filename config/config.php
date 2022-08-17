<?php

    /*Datos de conexion a la base de datos*/
    //$db_user = "fpadilla";
    //$db_pass = "fpadilla0";        
    
    $db_user = "SIS_INT_PAG";
    $db_pass = "s3s3ntp1g";    
               
    //CONEXION BDESAL
    //$db_host = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=172.23.50.49)(PORT=1521))(CONNECT_DATA=(SID = BDSAL)))";    
    //$charset = "utf8";
    
    //CONEXION MUNI
    $db_host = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=MUNISCAN.MUNI)(PORT=1521))(CONNECT_DATA=(SERVER=dedicated)(SERVICE_NAME=MUNI)))";
    $charset = "utf8";    
  
    $con =@oci_connect($db_user, $db_pass, $db_host, $charset);

    if (!$con){
        @die("<h2 style='text-align:center'>Imposible conectarse a la base de datos!</h2>" . oci_error($con));
    }else{
        null;
    }

?>