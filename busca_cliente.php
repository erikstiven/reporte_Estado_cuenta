<?php
	
	include_once('../../Include/config.inc.php');
	include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
	include_once(path(DIR_INCLUDE).'comun.lib.php');

	if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    global $DSN_Ifx, $DSN;

	$oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //varibales de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    
    if (isset($_REQUEST['nomClpv']))
        $nomClpv = $_REQUEST['nomClpv'];
    else
        $nomClpv = null;

    //lectura sucia
    //////////////

    $tabla = '';

    $sql = "select first 100 clpv_cod_clpv, clpv_ruc_clpv, clpv_nom_clpv,
    		clpv_est_clpv
    		from saeclpv
    		where clpv_cod_empr = $idempresa and
    		clpv_clopv_clpv = 'PV' and
    		clpv_nom_clpv like upper ('%$nomClpv%')
    		order by 3";
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		$sHtmlEstado = '';
    		do{

    			$clpv_cod_clpv = $oIfx->f('clpv_cod_clpv');
    			$clpv_ruc_clpv = $oIfx->f('clpv_ruc_clpv');
                $clpv_nom_clpv = $oIfx->f('clpv_nom_clpv');
                $clpv_est_clpv = $oIfx->f('clpv_est_clpv');

                $estado = '';
                $color = '';
                if ($clpv_est_clpv == 'A') {
                    $estado = 'ACTIVO';
                    $color = 'primary';
                } elseif ($clpv_est_clpv == 'P') {
                    $estado = 'PENDIENTE';
                    $color = 'success';
                } elseif ($clpv_est_clpv == 'S') {
                    $estado = 'SUSPENDIDO';
                    $color = 'danger';
                } 
				
				$sHtmlEstado = '<div class=\"'.$color.'\">'.$estado.'</div>';

				$img = '<div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $clpv_cod_clpv . '\', \'' . $clpv_nom_clpv . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div>';

    			$tabla.='{
				  "clpv_cod_clpv":"'.$clpv_cod_clpv.'",
				  "clpv_ruc_clpv":"'.$clpv_ruc_clpv.'",
				  "clpv_nom_clpv":"'.$clpv_nom_clpv.'",
				  "clpv_est_clpv":"'.$sHtmlEstado.'",
				  "selecciona":"'.$img.'"
				},';

			}while($oIfx->SiguienteRegistro());
    	}
	}
	$oIfx->Free();

	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);

	echo '{"data":['.$tabla.']}';
	
?>