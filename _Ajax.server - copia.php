<?php
require ("_Ajax.comun.php"); // No modificar esta linea
/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// S E R V I D O R   A J A X //
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/


/**********************************************/
/* FCA01 :: GENERA INGRESO TABLA PRESUPUESTO  */
/**********************************************/
function genera_cabecera_formulario($sAccion='nuevo', $aForm='') {
	//Definiciones
	global $DSN_Ifx, $DSN;	
	session_start ();
	
	$fu = new Formulario ( );
	$fu->DSN = $DSN;
	
	$ifu = new Formulario ( );
	$ifu->DSN = $DSN_Ifx;

        $oCon = new Dbo ( );
	$oCon->DSN = $DSN;
	$oCon->Conectar ();

	$oIfx = new Dbo ( );
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar ();
	
	$sHtml = '';
	$oReturn = new xajaxResponse ( );
	
	// VARIABLES
	$perfil =  $_SESSION['U_PERFIL'];
	$id_empresa = $aForm['empresa'];
	$fecha_ini = $aForm['fecha_ini'];
    $fecha_fin = $aForm['fecha_fin'];
    $id_cliente = $aForm['cliente'];
    $grupo = $aForm['grupo'];

        //  LECTURA SUCIA
        //////////////
        
	switch ($sAccion){
		case 'nuevo':
		
			$ifu->AgregarCampoListaSQL ( 'empresa', 'Empresa|left', "select empr_cod_empr, empr_nom_empr from saeempr order by 2" , true, 'auto' );
			$ifu->AgregarComandoAlCambiarValor('empresa','cargar_grupo()');
			$ifu->AgregarCampoListaSQL ( 'proveedor', 'Proveedor|left', '', true, 'auto' );
			
			$ifu->AgregarCampoFecha('fecha_ini','Fecha Inicio|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
			$ifu->AgregarCampoFecha('fecha_fin','Fecha Final|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
			$ifu->AgregarCampoListaSQL ( 'grupo', 'Grupo|left', '', true, 'auto' );
			break;
		case 'grupo':
		
			$ifu->AgregarCampoListaSQL ( 'empresa', 'Empresa|left', "select empr_cod_empr, empr_nom_empr from saeempr order by 2" , true, 'auto' );
			$ifu->AgregarComandoAlCambiarValor('empresa','cargar_grupo()');

			$ifu->AgregarCampoFecha('fecha_ini','Fecha Inicio|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
			$ifu->AgregarCampoFecha('fecha_fin','Fecha Final|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
																	
			$ifu->AgregarCampoListaSQL ( 'grupo', 'Grupo|left',"select  grpv_cod_grpv , grpv_nom_grpv  from saegrpv where
                                                                                        grpv_cod_modu = 4 and
                                                                                        grpv_cod_empr = $id_empresa ", true, 'auto' );
			$ifu->AgregarComandoAlCambiarValor('grupo','cargar_prove()');																			
			$ifu->AgregarCampoListaSQL('proveedor','Proveedor|left',"",true,'auto');
				
			$ifu->cCampos["empresa"]->xValor = $id_empresa;	
			$ifu->cCampos["grupo"]->xValor = $grupo;			
			$ifu->cCampos["fecha_ini"]->xValor = $fecha_ini;
			$ifu->cCampos["fecha_fin"]->xValor = $fecha_fin;
			$ifu->cCampos["cliente"]->xValor = $id_cliente;
			
			break;
		
		case 'proveedor':
		
			$ifu->AgregarCampoListaSQL ( 'empresa', 'Empresa|left', "select empr_cod_empr, empr_nom_empr from saeempr order by 2" , true, 'auto' );
						$ifu->AgregarComandoAlCambiarValor('empresa','cargar_grupo()');

			$ifu->AgregarCampoFecha('fecha_ini','Fecha Inicio|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
			$ifu->AgregarCampoFecha('fecha_fin','Fecha Final|left',true,date('Y').'/'.date('m').'/'.date('d'));
			
			$ifu->AgregarCampoListaSQL ( 'grupo', 'Grupo|left',"select  grpv_cod_grpv , grpv_nom_grpv  from saegrpv where
                                                                                        grpv_cod_modu = 4 and
                                                                                        grpv_cod_empr = $id_empresa ", true, 'auto' );
			
			$ifu->AgregarComandoAlCambiarValor('grupo','cargar_prove()');					


			$ifu->AgregarCampoListaSQL('proveedor','Proveedor|left',"SELECT CLPV_COD_CLPV, CLPV_NOM_CLPV FROM SAECLPV WHERE
																	CLPV_CLOPV_CLPV='PV' AND
																	grpv_cod_grpv = '$grupo' and 
																	CLPV_COD_EMPR = $id_empresa ORDER BY CLPV_NOM_CLPV",true,'auto');
																	
			$ifu->cCampos["empresa"]->xValor = $id_empresa;	
			$ifu->cCampos["grupo"]->xValor = $grupo;				
			$ifu->cCampos["fecha_ini"]->xValor = $fecha_ini;
			$ifu->cCampos["fecha_fin"]->xValor = $fecha_fin;
			$ifu->cCampos["cliente"]->xValor = $id_cliente;
			break;
	}
		
	
	$boton = '<input type="button" value="Buscar"
				onClick="javascript:reporte( )"
				id="BuscaBtn" class="BotonFormulario"
				onMouseOver="javascript:this.className=\''.BotonFormularioActivo.'\';"
				onMouseOut="javascript:this.className=\''.BotonFormulario.'\';"
				style="width:100px"/>';
	
	$sHtml .='<table class="table table-striped table-condensed" style="width: 80%; margin-bottom: 0px;" align="center">
					<tr>
							<td>
									<div class="btn-group">
										<div class="btn btn-primary btn-sm" onclick="genera_formulario();">
											<span class="glyphicon glyphicon-file"></span>
											Nuevo
										</div>
										<div class="btn btn-primary btn-sm" onclick="document.location=\'excel.php?\'" id = "excel"
											<span class="glyphicon glyphicon-print"></span>
											Imprimir
										</div>
									</div>

							</td>
					</tr>
			   </table>';
	$sHtml .= '<table class="table table-striped table-condensed" style="width: 80%; margin-bottom: 0px;" align="center">
				<tr ><td colspan="4" align="center" class="bg-primary">ESTADO DE CUENTA PROVEEDOR</td></tr>
				<tr class="msgFrm"><td colspan="4" align="center">Los campos con * son de ingreso obligatorio</td></tr>';
	$sHtml .= '<tr>
					<td align="left">' . $ifu->ObjetoHtmlLBL ( 'empresa' ) . '</td>
					<td>' . $ifu->ObjetoHtml ( 'empresa' ) . '</td>
					<td align="left">' . $ifu->ObjetoHtmlLBL ( 'grupo' ) . '</td>
					<td>' . $ifu->ObjetoHtml ( 'grupo' ) . '</td>
			   </tr>';
	$sHtml .= '<tr>
					<td align="left">' . $ifu->ObjetoHtmlLBL ( 'fecha_ini' ) . '</td>
					<td>' . $ifu->ObjetoHtml ( 'fecha_ini' ) . '</td>
					<td align="left">' . $ifu->ObjetoHtmlLBL ( 'fecha_fin' ) . '</td>
					<td>' . $ifu->ObjetoHtml ( 'fecha_fin' ) . '</td>
				</tr>';
	$sHtml .= '<tr>
					<td align="left">' . $ifu->ObjetoHtmlLBL ( 'proveedor' ) . '</td>
					<td colspan="3">' . $ifu->ObjetoHtml ( 'proveedor' ) . '</td>
			   </tr>';        
	$sHtml .= '<tr>
					<td colspan="4" align="center">
					'.$boton.'															
					</td>
			   </tr>';
			
	$oReturn->assign ("DivPresupuesto", "innerHTML", $sHtml);
	
	return $oReturn;

}

// REPORTE CXTAS POR PAGAR
function reporte( $aForm = '' ) {
	global $DSN_Ifx, $DSN;

	session_start ();

    $oCon = new Dbo ( );
	$oCon->DSN = $DSN;
	$oCon->Conectar ();

	$oIfx = new Dbo ( );
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar ();

	$oReturn = new xajaxResponse ();

	// VARIABLES
	$user_web 	= $_SESSION['U_ID'];
	$id_empresa = $aForm['empresa'];
	$fecha_ini  = fecha_informix($aForm['fecha_ini']);
	$fecha_fin  = fecha_informix($aForm['fecha_fin']);
	$id_prove   = $aForm['proveedor'];
	$id_grupo   = $aForm['grupo'];

	//  LECTURA SUCIA
	//////////////

	if(empty($id_prove)){
		if(!empty($id_grupo)){
			// UN GRUPO
			$sql_sp = "SELECT * FROM  'informix'.sp_estado_cuenta_prove_web( $id_empresa,  '',  '$fecha_ini',  '$fecha_fin' , 3 , $user_web, '$id_grupo' ); ";
		}else{
			// todos los proveedor
			$sql_sp = "SELECT * FROM  'informix'.sp_estado_cuenta_prove_web( $id_empresa,  '',  '$fecha_ini',  '$fecha_fin' , 2 , $user_web, '$id_grupo' ); ";
		}
		
		
		
		// saldo anterior
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_ini' ) group by 2 order by 2";
		unset($array_saldo);
		if($oIfx->Query($sql)){
			if($oIfx->NumFilas()>0){
				do{
					$cod_clpv = $oIfx->f('clpv_cod_clpv');
					$saldo = $oIfx->f('saldo');
					if(empty($saldo)){
						$saldo = 0;
					}
					$array_saldo [$cod_clpv] = $saldo;
				}while ($oIfx->SiguienteRegistro());
			}else{
				$array_saldo [0] = 0;
			}
		}
		$oIfx->Free();
	}else{
		// un solo proveedor
		$sql_sp = "SELECT * FROM  'informix'.sp_estado_cuenta_prove_web( $id_empresa,  $id_prove,  '$fecha_ini',  '$fecha_fin' , 1 , $user_web, '$id_grupo' ); ";

		// saldo anterior
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
					   AND ( saedmcp.clpv_cod_clpv = $id_prove  )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_ini' ) group by 2 order by 2";
//            $oReturn->alert($sql);
		unset($array_saldo);
		if($oIfx->Query($sql)){
			if($oIfx->NumFilas()>0){
				do{
					$cod_clpv 	= $oIfx->f('clpv_cod_clpv');
					$saldo 		= $oIfx->f('saldo');
					$array_saldo [$cod_clpv] = $saldo;
				}while ($oIfx->SiguienteRegistro());
			}else{
				$array_saldo [0] = 0;
			}
		}
		$oIfx->Free();
	} // fin if
	
	$tabla_reporte .='<br>';
	$tabla_reporte .='<table class="table table-striped table-condensed table-bordered table-hover" style="width: 80%; margin-top: 20px;" align="center">';
	$tabla_reporte .='<tr>
                                <td class="bg-primary" align="center" colspan="12">REPORTE ESTADO DE PROVEEDORES</td>
                     </tr>';
	$tabla_reporte .='<tr>
							<td align="center">Sucursal</td>
							<td align="center">Proveedor</td>
							<td align="center">Tran</td>
							<td align="center">Nombre Tran</td>
							<td align="center">Documento</td>
							<td align="center" style="width: 10%;">No- Factura</td>
							<td align="center">Emision</td>
							<td align="center">Vence</td>
							<td align="center">Detalle</td>
							<td align="center" width="6%">Debito</th>
							<td align="center" width="6%">Credito</th>
							<td align="center" width="6%">Saldo</th>
					  </tr>';
	$oReturn->alert('Buscando....');
	$x			 = 1;
	$total_nb 	 = 0;
	$total_cre 	 = 0;
	$total_sald  = 0;
	$sub_tot_deb = 0;
	$sub_tot_cre = 0;
	$sub_sald 	 = 0;
	unset($array_clie);
	if($oIfx->Query($sql_sp)){
		if ($oIfx->NumFilas() > 0){
			do{
				$fec_emi 		= fecha_d_m_y($oIfx->f('fecha_emision'));
				$cod_tran 		= $oIfx->f('tran_cod_tran');
				$comprobante 	= $oIfx->f('comprobante');
				$fact 			= $oIfx->f('factura');
				$fec_vence 		= fecha_d_m_y($oIfx->f('fecha_vencimiento'));
				$detalle 		= acento_func($oIfx->f('detalle'));
				$debito 		= $oIfx->f('debito');
				$credito 		= $oIfx->f('credito');
				$proveedor		= acento_func($oIfx->f('prove'));
				$clpv_cod_clpv  = $oIfx->f('clpv_cod_clpv');
				$user_web 		= $oIfx->f('user_web');
				$array_clie [$x]= $clpv_cod_clpv;
				$cod_sucu		= $oIfx->f('sucu_cod');
				$dmcp_cod_modu  = $oIfx->f('modu_cod');
				$ejer_cod       = $oIfx->f('ejer_cod');
				$prdo_cod	    = $oIfx->f('prdo_cod');
				
				if($x==1){
					$saldo = $array_saldo[$clpv_cod_clpv];
					$sub_sald = $saldo;
					if ($sClass=='off') $sClass='on'; else $sClass='off';
					$tabla_reporte .='<tr height="20" class="'.$sClass.'"
										onMouseOver="javascript:this.className=\'link\';"
										onMouseOut="javascript:this.className=\''.$sClass.'\';">';
					$tabla_reporte .= '<td colspan="8"></td>';
					$tabla_reporte .='<td align="right" colspan="3" class="letra_rojo">SALDO ANTERIOR:</td>';
					$tabla_reporte .='<td align="right" class="fecha_grande">'.number_format($saldo,2,'.',',').'</td>';
					$tabla_reporte .='</tr>';
				}elseif($x>1){
					if( $array_clie[$x] != $array_clie[$x-1] ){
						if ($sClass=='off') $sClass='on'; else $sClass='off';
						// subtotales
						$tabla_reporte .= '<tr bgcolor="#EBF0FA" height="18px">';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .= '<td class="fecha_grande" align="right">TOTALES:</td>';
						$tabla_reporte .= '<td class="fecha_grande" align="right">'.number_format($sub_tot_deb,2,'.',',').'</td>
										   <td class="fecha_grande" align="right">'.number_format($sub_tot_cre,2,'.',',').'</td>
										   <td class="fecha_grande" align="right">'.$saldo.'</td>
										   <td></td>
										   </tr>';

						// inicio otro PROVE
						$saldo = $array_saldo[$clpv_cod_clpv];
						$sub_sald = $saldo;

						$tabla_reporte .='<tr height="20" class="'.$sClass.'"
											onMouseOver="javascript:this.className=\'link\';"
											onMouseOut="javascript:this.className=\''.$sClass.'\';">';
						$tabla_reporte .='<td align="left"></td>';
						$tabla_reporte .='<td align="left"></td>';
						$tabla_reporte .='<td align="right"></td>';
						$tabla_reporte .='<td align="right"></td>';
						$tabla_reporte .='<td align="left"></td>';
						$tabla_reporte .='<td align="left"></td>';
						 $tabla_reporte .='<td align="left"></td>';
						$tabla_reporte .='<td align="right" colspan="3" class="letra_rojo">SALDO ANTERIOR:</td>';
						$tabla_reporte .='<td align="right" class="fecha_grande">'.number_format($saldo,2,'.',',').'</td>';
						$tabla_reporte .='</tr>';

						$sub_tot_deb = 0;
						$sub_tot_cre = 0;
					}// fin if

				}
				
				$saldo = $saldo + $debito - $credito;
				$nombre_tran = nombre_transaccion($cod_tran, $cod_sucu, $dmcp_cod_modu);
				$nombre_sucu = nombre_sucursal($cod_sucu);
				
				if ($sClass=='off') $sClass='on'; else $sClass='off';
				$tabla_reporte .='<tr height="20" class="'.$sClass.'"
										onMouseOver="javascript:this.className=\'link\';"
										onMouseOut="javascript:this.className=\''.$sClass.'\';">';
				$tabla_reporte .='<td align="left">'.$nombre_sucu.'</td>';
				$tabla_reporte .='<td align="left">'.$proveedor.'</td>';
				$tabla_reporte .='<td align="left">'.$cod_tran.'</td>';
				$tabla_reporte .='<td align="left">'.$nombre_tran.'</td>';
				$tabla_reporte .='<td style="cursor:hand" align="right" title="Click para ver el Asiento.."
										onclick="seleccionaItem('.$id_empresa.', '.$cod_sucu.', '.$ejer_cod.', '.$prdo_cod.', \''.$comprobante.'\', \''.$clpv_cod_clpv.'\');">
										'.$comprobante.'
								  </td>';
				$tabla_reporte .='<td align="right">'.$fact.'</td>';
				$tabla_reporte .='<td align="left">'.$fec_emi.'</td>';
				$tabla_reporte .='<td align="left">'.$fec_vence.'</td>';
				$tabla_reporte .='<td align="left">'.$detalle.'</td>';
				$tabla_reporte .='<td align="right">'.number_format($debito,3,'.',',').'</td>';
				$tabla_reporte .='<td align="right">'.number_format($credito,3,'.',',').'</td>';
				$tabla_reporte .='<td align="right">'.number_format($saldo,3,'.',',').'</td>';
				$tabla_reporte .='</tr>';
				$x++;
				$total_nb += $debito;
				$total_cre += $credito;
				$sub_tot_deb += $debito;
				$sub_tot_cre += $credito;
			} while ( $oIfx->SiguienteRegistro () );

				// subtotales
				$tabla_reporte .= '<tr bgcolor="#EBF0FA" height="18px">';
				$tabla_reporte .= '<td colspan="8"></td>';
				$tabla_reporte .= '<td class="fecha_grande" align="right">TOTALES:</td>';
				$tabla_reporte .= '<td class="fecha_grande" align="right">'.number_format($sub_tot_deb,3,'.',',').'</td>
								   <td class="fecha_grande" align="right">'.number_format($sub_tot_cre,3,'.',',').'</td>
								   <td class="fecha_grande" align="right">'.number_format($saldo,3,'.',',').'</td>
								   </tr>';

				// totales
				$tabla_reporte .= '<tr bgcolor="#EBF0FA" height="18px">';
			   $tabla_reporte .= '<td colspan="8"></td>';
				$tabla_reporte .= '<td class="letra_rojo" align="right">TOTALES:</td>';
				$tabla_reporte .= '<td class="fecha_grande" align="right">'.number_format($total_nb,3,'.',',').'</td>
								   <td class="fecha_grande" align="right">'.number_format($total_cre,3,'.',',').'</td>
								   <td class="fecha_grande" align="right">'.number_format($total_nb - $total_cre + $sub_sald,3,'.',',').'</td>
								   </tr>';
		}
	}
	$oIfx->Free();
	$tabla_reporte .='</table>';

	//Armado Cabecera Excel
	unset($_SESSION['sHtml_cab']);
	unset($_SESSION['sHtml_det']);
	$sHtml_exe_p .='<table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
						<tr>
								<th colspan = "10">ESTADO DE CUENTA PROVEEDOR</th>
						</tr>
						<tr></tr><tr></tr>
								<th colspan="2">Fecha Reporte:</th>
								<td align="left">'.date("d-m-Y").'</td>
								<td></td>
						</tr>
						<tr></tr><tr></tr>
					</table>';

	$_SESSION['sHtml_cab']=$sHtml_exe_p;
	$_SESSION['sHtml_det']=$tabla_reporte;

    $oReturn->assign ( "DivReporte", "innerHTML", $tabla_reporte );
	return $oReturn;
}

function Mes($mes){

	switch($mes){
		case 1:
			$nombre_mes = "Enero";
		break;
		case 2:
			$nombre_mes = "Febrero";
		break;
		case 3:
			$nombre_mes = "Marzo";
		break;
		case 4:
			$nombre_mes = "Abril";
		break;
		case 5:
			$nombre_mes = "Mayo";
		break;
		case 6:
			$nombre_mes = "Junio";
		break;
		case 7:
			$nombre_mes = "Julio";
		break;
		case 8:
			$nombre_mes = "Agosto";
		break;
		case 9:
			$nombre_mes = "Septiembre";
		break;
		case 10:
			$nombre_mes = "Octubre";
		break;
		case 11:
			$nombre_mes = "Noviembre";
		break;
		case 12:
			$nombre_mes = "Diciembre";
		break;
	}

	return $nombre_mes;
}

function fecha_informix($fecha){
	$m = substr($fecha,5,2);
	$y = substr($fecha,0,4);
	$d = substr($fecha,8,2);

	return ( $m.'/'.$d.'/'.$y );
}

function fecha_mysql($fecha){
        $fecha_array = explode('/',$fecha);
        $m = $fecha_array[0];
	$y = $fecha_array[2];
	$d = $fecha_array[1];

	return ( $d.'/'.$m.'/'.$y );
}

function fecha_d_m_y($fecha){
        $fecha_array = explode('/',$fecha);
        $y = $fecha_array[0];
	$m = $fecha_array[1];
	$d = $fecha_array[2];

	return ( $d.'/'.$m.'/'.$y );
}

function restaFechas($dFecIni, $dFecFin){
        $dFecIni = str_replace("-","",$dFecIni);
        $dFecIni = str_replace("/","",$dFecIni);
        $dFecFin = str_replace("-","",$dFecFin);
        $dFecFin = str_replace("/","",$dFecFin);

        ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);

        ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

        $date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
        $date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);

        return round(($date2 - $date1) / (60 * 60 * 24));
}

function nombre_sucursal($cod){
global $DSN_Ifx, $DSN;

	session_start ();
	$oIfx = new Dbo ( );
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar ();	
	
	$sql="SELECT sucu_nom_sucu FROM saesucu WHERE sucu_cod_sucu='$cod'";
	if($oIfx->Query($sql)){
		$nombre= $oIfx->f('sucu_nom_sucu');
	}
	
	return $nombre;
}

function nombre_transaccion($cod, $sucu, $modu){
global $DSN_Ifx, $DSN;

	session_start ();
$oIfx = new Dbo ( );
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar ();
	
	$sql="select tran_des_tran  from saetran where tran_cod_tran='$cod' and tran_cod_sucu='$sucu' and tran_cod_modu='$modu'";
	//echo $sql;exit;
	if($oIfx->Query($sql)){
		$nombre= $oIfx->f('tran_des_tran');
	}
	//echo $nombre;exit;
	return $nombre;
	
}




function verDiarioContable($aForm = '', $empr = 0, $sucu = 0, $ejer = 0, $mes = 0, $asto = '', $clpv_cod = ''){

	if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
	global $DSN_Ifx,$DSN;

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();
	
	$oCon = new Dbo;
	$oCon->DSN = $DSN;
	$oCon->Conectar();
	
	$oReturn = new xajaxResponse();

	//variables del formulario
	$empresa = $aForm['empresa'];
	$anio = $aForm['anio'];
	$mes_1 = $aForm['mes_1'];
	$mes_2 = $aForm['mes_2'];
	$nivel = $aForm['nivel']; 
	$campo = 0;
	
	$class = new GeneraDetalleAsientoContable();
	
	$arrayAsto 			= $class->informacionAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);
	
	$arrayDiario 		= $class->diarioAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);
	
	$arrayDirectorio 	= $class->directorioAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);
	
	$arrayRetencion 	= $class->retencionAsientoContable($oIfx, $empr, $sucu, $ejer, $mes, $asto);
	
	$arrayAdjuntos 		= $class->adjuntosAsientoContable($oCon, $empr, $sucu, $ejer, $mes, $asto, $clpv_cod);

	try{
		
		//LECTURA SUCIA1
		//////////////
		
		
		//sucursal
		$sql = "select sucu_nom_sucu from saesucu where sucu_cod_sucu = $sucu";
		$sucu_nom_sucu = consulta_string_func($sql, 'sucu_nom_sucu', $oIfx, '');
		
		
		$oReturn->assign("divTituloAsto", "innerHTML", $asto.' - '.$sucu_nom_sucu);
		
		if(count($arrayAsto) > 0){
			
			$table.= '<table class="table table-striped table-condensed" align="center" width="98%">';
			$table.= '<tr>';
			$table.= '<td colspan="4" class="bg-primary">DIARIO CONTABLE</td>';
			$table.= '</tr>';
		
			foreach($arrayAsto as $val){
				$asto_cod_asto = $val[0]; 
				$asto_vat_asto = $val[1]; 
				$asto_ben_asto = $val[2];
				$asto_fec_asto = $val[3];
				$asto_det_asto = $val[4];
				$asto_cod_modu = $val[5];
				$asto_usu_asto = $val[6];
				$asto_user_web = $val[7];
				$asto_fec_serv = $val[8];
				$asto_cod_tidu = $val[9];
				
				//modulo
				$sql = "select modu_des_modu from saemodu where modu_cod_modu = $asto_cod_modu";
				$modu_des_modu = consulta_string_func($sql, 'modu_des_modu', $oIfx, '');
				
				//tipo documento
				$sql = "select tidu_des_tidu from saetidu where tidu_cod_tidu = $asto_cod_tidu";
				$tidu_des_tidu = consulta_string_func($sql, 'tidu_des_tidu', $oIfx, '');
				
				$table.= '<tr>';
				$table.= '<td>Diario:</td>';
				$table.= '<td>'.$asto_cod_asto.'</td>';
				$table.= '<td>Fecha:</td>';
				$table.= '<td>'.$asto_fec_asto.'</td>';
				$table.= '</tr>';
				
				$table.= '<tr>';
				$table.= '<td>Beneficiario:</td>';
				$table.= '<td colspan="3">'.$asto_ben_asto.'</td>';
				$table.= '</tr>';
				
				$table.= '<tr>';
				$table.= '<td>Modulo:</td>';
				$table.= '<td>'.$modu_des_modu.'</td>';
				$table.= '<td>Documento:</td>';
				$table.= '<td>'.$asto_cod_tidu.' - '.$tidu_des_tidu.'</td>';
				$table.= '</tr>';
				
				$table.= '<tr>';
				$table.= '<td>Detalle:</td>';
				$table.= '<td colspan="3">'.$asto_det_asto.'</td>';
				$table.= '</tr>';
				//sucursal, cod_prove, asto_cod, ejer_cod, prdo_cod
				$table.= '<tr>';
				$table.= '<td>Formato:</td>';
				$table.= '<td align="left">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario('.$sucu.', 0, \'' . $asto. '\', '.$ejer.', '.$mes.');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
				$table.= '<td>Valor:</td>';
				$table.= '<td class="bg-danger fecha_letra" align="left">'.number_format($asto_vat_asto,2,'.',',').'</td>';
				$table.= '</tr>';
				
			}//fin foreach
			
			$table.= '</table>';
			
			$oReturn->assign("divInfo", "innerHTML", $table);
		}
		
		//directorio
		if(count($arrayDiario) > 0){
			
			$tableDia.= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableDia.= '<tr>';
			$tableDia.= '<td colspan="5" class="bg-primary">DIARIO</td>
						<td align="center">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario('.$sucu.', 0, \'' . $asto. '\', '.$ejer.', '.$mes.');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
			$tableDia.= '</tr>';
			$tableDia.= '<tr>';
			$tableDia.= '<td>Cuenta Contable</td>';
			$tableDia.= '<td>Centro Costos</td>';
			$tableDia.= '<td>Centro Actividad</td>';
			$tableDia.= '<td>Documento</td>';
			$tableDia.= '<td>Debito</td>';
			$tableDia.= '<td>Credito</td>';
			$tableDia.= '</tr>';
			$totalDeb = 0;
			$totalCre = 0;
			foreach($arrayDiario as $val){
				$dasi_cod_cuen = $val[0]; 
				$dasi_cod_cact = $val[1]; 
				$ccos_cod_ccos = $val[2];
				$dasi_dml_dasi = $val[3];
				$dasi_cml_dasi = $val[4];
				$dasi_det_asi = $val[5];
				$dasi_num_depo = $val[6];
									
				//clpv
				$cuen_nom_cuen = '';
				if(!empty($dasi_cod_cuen)){
					$sql = "select cuen_nom_cuen from saecuen where cuen_cod_cuen = '$dasi_cod_cuen' and cuen_cod_empr = $empr";
					$cuen_nom_cuen = consulta_string_func($sql, 'cuen_nom_cuen', $oIfx, '');
				}
				
				$ccosn_nom_ccosn = '';
				if(!empty($ccos_cod_ccos)){
					$sql = "select ccosn_nom_ccosn from saeccosn where ccosn_cod_ccosn = '$ccos_cod_ccos' and ccosn_cod_empr = $empr";
					$ccosn_nom_ccosn = consulta_string_func($sql, 'ccosn_nom_ccosn', $oIfx, '');
				}
				
				$cact_nom_cact = '';
				if(!empty($dasi_cod_cact)){
					$sql = "select cact_nom_cact from saecact where cact_cod_cact = '$dasi_cod_cact' and cact_cod_empr = $empr";
					$cact_nom_cact = consulta_string_func($sql, 'cact_nom_cact', $oIfx, '');
				}
				
				$tableDia.= '<tr>';
				$tableDia.= '<td>'.$dasi_cod_cuen.' - '.$cuen_nom_cuen.'</td>';
				$tableDia.= '<td>'.$ccos_cod_ccos.' - '.$ccosn_nom_ccosn.'</td>';
				$tableDia.= '<td>'.$dasi_cod_cact.' - '.$cact_nom_cact.'</td>';
				$tableDia.= '<td>'.$dasi_num_depo.'</td>';
				$tableDia.= '<td align="right">'.number_format($dasi_dml_dasi,2,'.',',').'</td>';
				$tableDia.= '<td align="right">'.number_format($dasi_cml_dasi,2,'.',',').'</td>';
				$tableDia.= '</tr>';
				
				$totalDeb += $dasi_dml_dasi;
				$totalCre += $dasi_cml_dasi;
			}//fin foreach
			$tableDia.= '<tr>';
			$tableDia.= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
			$tableDia.= '<td align="right" class="bg-danger fecha_letra">'.number_format($totalDeb,2,'.',',').'</td>';
			$tableDia.= '<td align="right" class="bg-danger fecha_letra">'.number_format($totalCre,2,'.',',').'</td>';
			$tableDia.= '</tr>';
			$tableDia.= '</table>';
			
			$oReturn->assign("divDiario", "innerHTML", $tableDia);
			
		}
		
		//directorio
		if(count($arrayDirectorio) > 0){
			
			$tableDir.= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableDir.= '<tr>';
			$tableDir.= '<td colspan="6" class="bg-primary">DIRECTORIO</td>';
			$tableDir.= '</tr>';
			$tableDir.= '<tr>';
			$tableDir.= '<td>No.</td>';
			$tableDir.= '<td>Cliente/Proveedor</td>';
			$tableDir.= '<td>Transaccion</td>';
			$tableDir.= '<td>Factura</td>';
			$tableDir.= '<td>Credito</td>';
			$tableDir.= '<td>Debito</td>';
			$tableDir.= '</tr>';
			$totalDeb = 0;
			$totalCre = 0;
			foreach($arrayDirectorio as $val){
				$dir_cod_dir = $val[0]; 
				$dir_cod_cli = $val[1]; 
				$tran_cod_modu = $val[2];
				$dir_cod_tran = $val[3];
				$dir_num_fact = $val[4];
				$dir_detalle = $val[5];
				$dir_fec_venc = $val[6];
				$dir_deb_ml = $val[7];
				$dir_cre_ml = $val[8];
				
				//clpv
				$clpv_nom_clpv = '';
				if(!empty($dir_cod_cli)){
					$sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $dir_cod_cli";
					$clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
				}
				
				$tableDir.= '<tr>';
				$tableDir.= '<td>'.$dir_cod_dir.'</td>';
				$tableDir.= '<td>'.$clpv_nom_clpv.'</td>';
				$tableDir.= '<td>'.$dir_cod_tran.'</td>';
				$tableDir.= '<td>'.$dir_num_fact.'</td>';
				$tableDir.= '<td align="right">'.number_format($dir_cre_ml,2,'.',',').'</td>';
				$tableDir.= '<td align="right">'.number_format($dir_deb_ml,2,'.',',').'</td>';
				$tableDir.= '</tr>';
				
				$totalCre += $dir_cre_ml;
				$totalDeb += $dir_deb_ml;
			}//fin foreach
			$tableDir.= '<tr>';
			$tableDir.= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
			$tableDir.= '<td align="right" class="bg-danger fecha_letra">'.number_format($totalCre,2,'.',',').'</td>';
			$tableDir.= '<td align="right" class="bg-danger fecha_letra">'.number_format($totalDeb,2,'.',',').'</td>';
			$tableDir.= '</tr>';
			$tableDir.= '</table>';
			
			$oReturn->assign("divDirectorio", "innerHTML", $tableDir);
			
		}
		
		//retencion
		if(count($arrayRetencion) > 0){
			
			$tableRet.= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableRet.= '<tr>';
			$tableRet.= '<td colspan="8" class="bg-primary">RETENCION</td>';
			$tableRet.= '</tr>';
			$tableRet.= '<tr>';
			$tableRet.= '<td>Cliente/Proveedor</td>';
			$tableRet.= '<td>Factura</td>';
			$tableRet.= '<td>Retencion</td>';
			$tableRet.= '<td>Codigo</td>';
			$tableRet.= '<td>Porcentaje</td>';
			$tableRet.= '<td>Base Imp.</td>';
			$tableRet.= '<td>Valor</td>';
			$tableRet.= '<td>Print</td>';
			$tableRet.= '</tr>';
			foreach($arrayRetencion as $val){
				$ret_cta_ret = $val[0]; 
				$ret_porc_ret = $val[1]; 
				$ret_bas_imp = $val[2];
				$ret_valor = $val[3];
				$ret_num_ret = $val[4];
				$ret_detalle = $val[5];
				$ret_num_fact = $val[6];
				$ret_ser_ret = $val[7];
				$ret_cod_clpv = $val[8];
				$ret_fec_ret = $val[9];
				
				//clpv
				$clpv_nom_clpv = '';
				if(!empty($ret_cod_clpv)){
					$sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $ret_cod_clpv";
					$clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
				}
				
				//fprv
				$printRet = '';
				if($asto_cod_modu == 4 || $asto_cod_modu == 6){
					
					//fecha fprv o minv
					if($asto_cod_modu == 4){
						$sql = "select fprv_fec_emis 
								from saefprv
								where fprv_cod_clpv = $ret_cod_clpv and
								fprv_num_fact = '$ret_num_fact' and
								fprv_cod_asto = '$asto' and
								fprv_cod_ejer = $ejer and
								fprv_cod_empr = $empr and
								fprv_cod_sucu = $sucu";
						$fechaEmis = consulta_string_func($sql, 'fprv_fec_emis', $oIfx, '');
					}elseif($asto_cod_modu == 6){
						$sql = "select minv_fmov 
								from saeminv
								where minv_cod_clpv = $ret_cod_clpv and
								minv_fac_prov = '$ret_num_fact' and
								minv_comp_cont = '$asto' and
								minv_cod_ejer = $ejer and
								minv_cod_empr = $empr and
								minv_cod_sucu = $sucu";
						$fechaEmis = consulta_string_func($sql, 'minv_fmov', $oIfx, '');
					}
					
					$printRet = '<div class="btn btn-primary btn-sm" onclick="genera_documento(5, \''.$campo.'\',\''.$fprv_clav_sri.'\' ,
																				 \''.$ret_cod_clpv.'\'  , \''.$ret_num_fact.'\', \''.$ejer.'\',
																				 \''.$asto.'\',  \''.$fechaEmis.'\', '.$sucu.');">
									<span class="glyphicon glyphicon-print"></span>
								</div>';
				}
				
				$tableRet.= '<tr>';
				$tableRet.= '<td>'.$clpv_nom_clpv.'</td>';
				$tableRet.= '<td>'.$ret_num_fact.'</td>';
				$tableRet.= '<td>'.$ret_ser_ret.' - '.$ret_num_ret.'</td>';
				$tableRet.= '<td>'.$ret_cta_ret.'</td>';
				$tableRet.= '<td align="right">'.$ret_porc_ret.'</td>';
				$tableRet.= '<td align="right">'.number_format($ret_bas_imp,2,'.',',').'</td>';
				$tableRet.= '<td align="right">'.number_format($ret_valor,2,'.',',').'</td>';
				$tableRet.= '<td align="center">'.$printRet.'</td>';
				$tableRet.= '</tr>';
				
			}//fin foreach
			
			$tableRet.= '</table>';
			
			$oReturn->assign("divRetencion", "innerHTML", $tableRet);
			
		}
		
		//adjuntos
		if(count($arrayAdjuntos) > 0){
			
			$tableAdj.= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableAdj.= '<tr>';
			$tableAdj.= '<td colspan="2" class="bg-primary">ARCHIVOS ADJUNTOS</td>';
			$tableAdj.= '</tr>';
			$tableAdj.= '<tr>';
			$tableAdj.= '<td>Titulo</td>';
			$tableAdj.= '<td>Ruta</td>';
			$tableAdj.= '</tr>';
			foreach($arrayAdjuntos as $val){
				$titulo = $val[0]; 
				$ruta = $val[1]; 
				
				$tableAdj.= '<tr>';
				$tableAdj.= '<td>'.$titulo.'</td>';
				$tableAdj.= '<td><a href="#" onclick="dowloand(\'' . $ruta . '\')">' . $ruta . '</a></td>';
				$tableAdj.= '</tr>';
				
			}//fin foreach
			
			$tableAdj.= '</table>';
			
			$oReturn->assign("divAdjuntos", "innerHTML", $tableAdj);
			
		}
		
	} catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }

	return $oReturn;
}





/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest ();
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
?>

