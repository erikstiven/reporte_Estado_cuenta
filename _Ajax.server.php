<?php
require("_Ajax.comun.php"); // No modificar esta linea
/*:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// S E R V I D O R   A J A X //
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/


/**********************************************/
/* FCA01 :: GENERA INGRESO TABLA PRESUPUESTO  */
/**********************************************/
function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '')
{
	//Definiciones
	global $DSN_Ifx, $DSN;
	session_start();

	$fu = new Formulario();
	$fu->DSN = $DSN;

	$ifu = new Formulario();
	$ifu->DSN = $DSN_Ifx;

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$sHtml = '';
	$oReturn = new xajaxResponse();

	//variables de sesion
	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];
	$perfil =  $_SESSION['U_PERFIL'];
	//variables del formulario
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	//  LECTURA SUCIA
	//////////////

	switch ($sAccion) {
		case 'nuevo':
			$ifu->AgregarCampoListaSQL('empresa', 'Empresa|left', "SELECT empr_cod_empr, empr_nom_empr from saeempr where empr_cod_empr = $idempresa", true, 170, 150, true);

			$ifu->AgregarComandoAlCambiarValor('empresa', 'f_filtro_sucursal();', true, 170, 150, true);
			$ifu->AgregarCampoListaSQL('sucursal', 'Sucursal|left', "SELECT sucu_cod_sucu, sucu_nom_sucu from saesucu where sucu_cod_empr = $empresa", false, 170, 150, true);
			$ifu->AgregarCampoListaSQL('ejercicio', 'Ejercicio|left', '', true, 150, 150, true);

			$ifu->AgregarComandoAlCambiarValor('ejercicio', 'f_filtro_periodo()', true, 150, 150, true);
			$ifu->AgregarCampoListaSQL('periodo', 'Per&iacuteodo|left', '', true, 150, 150, true);
			$ifu->AgregarCampoFecha('fecha_inicio', 'Inicio|left', true, date('Y') . '/' . date('m') . '/01', 70, 20, true);
			$ifu->AgregarCampoFecha('fecha_fin', 'Fin|left', true, date('Y') . '/' . date('m') . '/' . date('d'), 70, 20, true);
			$ifu->AgregarCampoLista('tipo', 'Tipo|left', false, '', 150, 150, true);
			$ifu->AgregarOpcionCampoLista('tipo', 'Diario', 'DI');
			$ifu->AgregarOpcionCampoLista('tipo', 'Ingreso', 'IN');
			$ifu->AgregarOpcionCampoLista('tipo', 'Egreso', 'EG');
			$ifu->AgregarCampoListaSQL('modulo', 'Modulo|left', "select modu_cod_modu, upper(modu_des_modu) as modu_des_modu
															from saemodu
															order by modu_cod_modu", true, 150, 150, true);
			$ifu->AgregarComandoAlCambiarValor('modulo', 'f_filtro_documento();', true, 150, 150, true);
			$ifu->AgregarCampoListaSQL('moneda', 'Moneda|left', "select mone_cod_mone, upper(mone_des_mone) as mone_des_mone
															from saemone
															where mone_cod_empr = $empresa
															order by mone_des_mone", true, 170, 150, true);
			$ifu->AgregarCampoListaSQL('documento', 'Documento|left', '', false, 150, 150, true);

			$ifu->AgregarCampoTexto('cliente', 'Proveedor|left', false, '', 220, 150, true);
			$ifu->AgregarComandoAlEscribir('cliente', 'buca_proveedor(event, id); form1.cliente.value=form1.cliente.value.toUpperCase();');
			$ifu->AgregarCampoOculto('clpv_cod_clpv', '');
			$ifu->AgregarCampoTexto('factura', 'No. Factura|left', false, '', 170, 150, true);
			$ifu->AgregarCampoListaSQL('grupo', 'Grupo|left', "select grpv_cod_grpv, grpv_nom_grpv
															  from saegrpv
															  where grpv_cod_empr = $empresa
															  and grpv_cod_modu = 4", false, 170, 150, true);
			$ifu->AgregarCampoListaSQL('zona', 'Zona|left', "select zona_cod_zona, zona_nom_zona
															  from saezona
															  where zona_cod_empr = $empresa
															  and zona_cod_sucu = $sucursal", false, 170, 150, true);

			$sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $empresa";
			$moneda_base = consulta_string($sql_moneda, 'pcon_mon_base', $oIfx, '0');

			$ifu->cCampos["empresa"]->xValor = $empresa;
			$ifu->cCampos["sucursal"]->xValor = $sucursal;
			$ifu->cCampos["moneda"]->xValor = $moneda_base;
	}
	/*
	<div class="btn-group">
								<div class="btn btn-primary btn-sm" onclick="document.location=\'excel.php?\'" >
									<span class="glyphicon glyphicon-print"></span>
									Excel
								</div>							
								<div class="btn btn-primary btn-sm" onclick="f_pdf();" id = "exportar">
										<span class="glyphicon glyphicon-print"></span>
										Imprimir
								</div>								
							</div>
	*/
	$table_op .= '<table class="table table-bordered table-condensed" style="margin-bottom: 0px; width: 90%;">
					<tr> 
						<td colspan="8" align="center" class="bg-primary">ESTADO DE CUENTA - PROVEEDORES</td>
					</tr>
					<tr>
						<td colspan = "8">    
							<div class="btn-group">
													
								<div class="btn btn-primary btn-sm" onclick="nuevoFormulario();" >
										<span class="glyphicon glyphicon-file"></span>
										Nuevo
								</div>								
							</div>
						</td>                   
					</tr>
					<tr class="info">
						<td colspan="8" align="center">Los campos con * son de ingreso obligatorio</td>
					</tr>
					<tr class="info">
						<td style="width: 50%;" colspan="4" align="center">Filtros</td>
						<td style="width: 30%;" align="center">Periodo</td>
						<td style="width: 20%;" align="center">Ordena Por</td>
					</tr>
					<tr>
						<td>' . $ifu->ObjetoHtmlLBL('empresa') . '</td>
						<td>' . $ifu->ObjetoHtml('empresa') . '</td>
						<td>' . $ifu->ObjetoHtmlLBL('sucursal') . '</td>
						<td>' . $ifu->ObjetoHtml('sucursal') . '</td>
						<td>
							 <table style="width: 100%;">
								<tr>
									<td>
										<label for="fecha3">A&ntildeo</label>
										<input type="radio" name="fecha" id="fecha3" value="a" onclick="cambioFiltroFecha(3)"/>
									</td>
									<td>
										<label for="fecha2">Mes</label>
										<input type="radio" name="fecha" id="fecha2" value="m" onclick="cambioFiltroFecha(2)"/>
									</td>
									 <td>
										<label for="fecha1">Fecha</label>
										<input type="radio" name="fecha" id="fecha1" value="f" checked onclick="cambioFiltroFecha(1)"/>
									</td>
								</tr>	
							 </table>
						</td>
						<td>
							<table style="width: 100%;">
								<tr>
									<td>
										<label for="orden">Fecha</label>
										<input type="radio" name="orden" value="fecha" checked="true"/>
									</td>
									<td>
										<label for="orden">Factura</label>
										<input type="radio" name="orden" value="factura"/>						
									</td>
								</tr>	
							</table>
						</td>												
					</tr>	
					<tr>
						<td>' . $ifu->ObjetoHtmlLBL('grupo') . ' </td>						
						<td>' . $ifu->ObjetoHtml('grupo') . ' </td>
						<td>' . $ifu->ObjetoHtmlLBL('zona') . ' </td>						
						<td>' . $ifu->ObjetoHtml('zona') . ' </td>	
						<td>
							<table style="width: 100%;">
								<tr id="campoFiltroFecha">
									<td>' . $ifu->ObjetoHtmlLBL('fecha_inicio') . '</td>
									<td>' . $ifu->ObjetoHtml('fecha_inicio') . '</td>
									<td>' . $ifu->ObjetoHtmlLBL('fecha_fin') . '</td>
									<td>' . $ifu->ObjetoHtml('fecha_fin') . '</td>
								</tr>
							</table>
						</td>
						<td align="center">  
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="remesa" id="remesa" value="S" onclick="cambioRemesa(id)">
            <label class="form-check-label" for="remesa">Sin Anticipos</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="anticipos" id="anticipos" value="S" onclick="cambioRemesa(id)">
            <label class="form-check-label" for="anticipos">Solo Anticipos</label>
        </div>
						</td>						
					 </tr>
					 <tr>
						<td>' . $ifu->ObjetoHtmlLBL('moneda') . ' </td>
						<td>' . $ifu->ObjetoHtml('moneda') . ' </td>						
						<td>' . $ifu->ObjetoHtmlLBL('factura') . ' </td>
						<td>' . $ifu->ObjetoHtml('factura') . ' </td>
					</tr>
						<td>' . $ifu->ObjetoHtml('clpv_cod_clpv') . '' . $ifu->ObjetoHtmlLBL('cliente') . ' </td>
						<td>' . $ifu->ObjetoHtml('cliente') . '
							<div id ="cliente" class="btn btn-primary btn-sm" onclick="buca_proveedor_id()">
						         <span class="glyphicon glyphicon-list-alt"><span>
						    </div></td>
						</td>
					<tr>
						
					</tr>
					<tr>
						<td colspan = "8" align="center">    
							<div class="btn-group">
								<div class="btn btn-primary btn-sm" onclick="reporte(1);" id = "reporte">
										<span class="glyphicon glyphicon-search"></span>
										Consultar
								</div>
								<div class="btn btn-success btn-sm" onclick="reporte(2);" id = "reporte">
										<span class="glyphicon glyphicon-export"></span>
										Exportar Excel
								</div>
							</div>
						</td>                   
					</tr>

                </table>				
				<br>
				<div id = "reporte"> </div>';
	$table_op .= '</fieldset>';
	$oReturn->assign("DivPresupuesto", "innerHTML", $table_op);
	return $oReturn;
}

function f_filtro_sucursal($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario
	$empresa = $aForm['empresa'];

	// DATOS EMPRESA
	$sql = "select sucu_cod_sucu, sucu_nom_sucu
			from saesucu
			where sucu_cod_empr = '$empresa'			
			order by sucu_nom_sucu";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_sucursal();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_sucursal(' . $i++ . ',\'' . $oIfx->f('sucu_cod_sucu') . '\', \'' . $oIfx->f('sucu_nom_sucu') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('sucursal', 'value', $data);
	return $oReturn;
}

function cambioFiltroFecha($aForm = '', $op = '')
{
	//Definiciones
	global $DSN_Ifx, $DSN;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo;
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$ifu = new Formulario;
	$ifu->DSN = $DSN_Ifx;

	$oReturn = new xajaxResponse();

	//variables de sesion
	$idempresa = $_SESSION['U_EMPRESA'];

	//variables del formulario
	$empresa = $aForm['empresa'];

	//echo $op; exit;

	if ($op == 1) { //fecha
		$ifu->AgregarCampoFecha('fecha_inicio', 'Fecha Inicio|left', true, date('Y') . '/' . date('m') . '/1', 70, 20, true);

		$ifu->AgregarCampoFecha('fecha_fin', 'Fecha Fin|left', true, date('Y') . '/' . date('m') . '/' . date('d'), 70, 20, true);

		$table .= '<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('fecha_inicio') . '</td>
                        <td>' . $ifu->ObjetoHtml('fecha_inicio') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('fecha_fin') . '</td>
                        <td>' . $ifu->ObjetoHtml('fecha_fin') . '</td>
                    </tr>';
	} elseif ($op == 2) { //mes
		$id_anio = date("Y");

		$sql = "select ejer_cod_ejer from saeejer where DATE_PART('year', ejer_fec_inil) = $id_anio";
		$ejer_cod_ejer = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 0);
		//echo $ejer_cod_ejer; exit;
		$ifu->AgregarCampoListaSQL('anio', 'A&ntildeo|left', "select ejer_cod_ejer,  DATE_PART('year', ejer_fec_inil) as anio from saeejer where
                                                        ejer_cod_empr = $empresa order by 2 desc ", true, 90, 150, true);
		$ifu->AgregarComandoAlCambiarValor('anio', 'cargarMes()');

		$ifu->AgregarCampoListaSQL('mes', 'Mes|left', "", false, 90, 150, true);

		$ifu->cCampos["anio"]->xValor = $ejer_cod_ejer;
		$oReturn->script('cargarMes(' . $ejer_cod_ejer . ')');

		$table .= '<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('anio') . '</td>
                        <td>' . $ifu->ObjetoHtml('anio') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mes') . '</td>
                        <td>' . $ifu->ObjetoHtml('mes') . '</td>
                    </tr>';
	} elseif ($op == 3) { //anio
		$id_anio = date("Y");

		$sql = "select ejer_cod_ejer from saeejer where DATE_PART('year', ejer_fec_inil) = $id_anio";
		$ejer_cod_ejer = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 0);

		$ifu->AgregarCampoListaSQL('anio', 'A&ntildeo|left', "select ejer_cod_ejer,  DATE_PART('year', ejer_fec_inil) as anio from saeejer where
                                                    ejer_cod_empr = $empresa order by 2 desc ", true, 90, 150, true);
		$ifu->cCampos["anio"]->xValor = $ejer_cod_ejer;

		$table .= '<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('anio') . '</td>
                        <td>' . $ifu->ObjetoHtml('anio') . '</td>
                    </tr>';
	}


	$oReturn->assign("campoFiltroFecha", "innerHTML", $table);
	return $oReturn;
}

function cargarMes($aForm = '')
{
	//Definiciones
	global $DSN_Ifx, $DSN;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oCon = new Dbo;
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oReturn = new xajaxResponse();

	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];

	//variables de sesion
	$empresa = $aForm['empresa'];
	$anio = $aForm['anio'];
	$id_mes = floor(date("m"));

	//$oReturn->alert($anio);

	if (empty($anio)) {
		$id_anio = date("Y");

		$sql = "select ejer_cod_ejer from saeejer where DATE_PART('year', ejer_fec_inil) = $id_anio";
		$ejer_cod_ejer = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 0);

		$anio = $ejer_cod_ejer;
	}

	$sql = "select prdo_num_prdo, prdo_nom_prdo from saeprdo where prdo_cod_empr = $empresa and prdo_cod_ejer = $anio";
	//$oReturn->alert($sql);
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminarCampo();');
		//$oReturn->script('eliminar_lista_anio();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadirElementoCampo(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\')'));
				//$oReturn->script(('anadir_elemento_anio(' . $i++ . ',\'' . $oIfx->f('ejer_cod_ejer') . '\',\'' . $oIfx->f('anio') . '\')'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oIfx->Free();

	$oReturn->assign('mes', 'value', $id_mes);

	return $oReturn;
}


// REPORTE CXTAS POR PAGAR
function reporte($aForm = '', $op)
{
	global $DSN_Ifx, $DSN;

	session_start();

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfxA = new Dbo();
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();

	$oReturn = new xajaxResponse();

	// VARIABLES
	$user_web 	 = $_SESSION['U_ID'];
	$id_empresa  = $aForm['empresa'];
	$id_sucursal = trim($aForm['sucursal']);
	$grupo       = trim($aForm['grupo']);
	$zona        = trim($aForm['zona']);
	$moneda      = $aForm['moneda'];
	$id_prove    = $aForm['clpv_cod_clpv'];
	$factura     = trim($aForm['factura']);
	$anio		 = $aForm['anio'];
	$mes		 = $aForm['mes'];
	$orden		 = $aForm['orden'];
	$fecha_rb    = $aForm['fecha'];
	$anticipos 	 = $aForm['remesa'];
	$cliente 	 = $aForm['cliente'];
	$solo_anti 	 = $aForm['anticipos'];

	if ($fecha_rb == 'f') {
		$fecha_inicio = $aForm['fecha_inicio'];
		$fecha_inicio = date("Y-m-d", strtotime($fecha_inicio));
		$fecha_fin = $aForm['fecha_fin'];
		$sql = "select ejer_cod_ejer 
				from saeejer 
				where DATE_PART('year', ejer_fec_inil) = DATE_PART('year', TIMESTAMP '$fecha_inicio')
				and ejer_cod_empr = $id_empresa ";
		//echo $sql; exit;		
		$anio = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 0);
	} elseif ($fecha_rb == 'm') {
		$anio = $aForm['anio'];
		$mes = $aForm['mes'];
		$sql = "select prdo_fec_ini, prdo_fec_fin 
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = $mes
				and prdo_cod_ejer = $anio";
		$fecha_inicio = consulta_string($sql, 'prdo_fec_ini', $oIfx, '');
		$fecha_fin = consulta_string($sql, 'prdo_fec_fin', $oIfx, '');
	} elseif ($fecha_rb == 'a') {
		$anio = $aForm['anio'];
		$sql = "select prdo_fec_ini
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = 1
				and prdo_cod_ejer = $anio";

		$fecha_inicio = consulta_string($sql, 'prdo_fec_ini', $oIfx, '');
		$sql = "select prdo_fec_fin
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = 12
				and prdo_cod_ejer = $anio";
		$fecha_fin = consulta_string($sql, 'prdo_fec_fin', $oIfx, '');
	}


	if ($solo_anti == 'S') {
		$anti_solo = 1;
	} else {
		$anti_solo = 0;
	}
	//echo $anti_solo; exit;
	if ($anticipos == 'S') {
		$remesa_sn = 1;
	} else {
		$remesa_sn = 0;
	}
	//echo $anticipos.'-'.$remesa_sn; exit;
	if (empty($id_sucursal)) {
		$id_sucursal = '%';
	}
	if (empty($grupo)) {
		$grupo = '%';
	}
	if (empty($zona)) {
		$zona = '%';
	}
	if (empty($moneda)) {
		$moneda = '%';
	}
	if (empty($factura)) {
		$factura = '%';
	}

	//  LECTURA SUCIA
	//////////////

	$sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $id_empresa";
	$moneda_base = consulta_string($sql_moneda, 'pcon_mon_base', $oIfx, '0');

	if ($orden == 'factura') {
		$ord = 1;
	} else {
		$ord = 2;
	}


	//echo $id_prove.'-'.$cliente; exit;
	if (empty($cliente)) { // Todos
		// DATOS DEL PROVEEDOR
		$sql = "select clpv_cod_clpv, clpv_ruc_clpv,
						(select max(tlcp_tlf_tlcp) from saetlcp 
						 where tlcp_cod_clpv = saeclpv.clpv_cod_clpv
						 and tlcp_cod_empr = saeclpv.clpv_cod_empr
						 and tlcp_cod_sucu = saeclpv.clpv_cod_sucu
						 ) as telefono,
						 (select max(dire_dir_dire) from saedire
						 where dire_cod_clpv = saeclpv.clpv_cod_clpv
						 and dire_cod_empr = saeclpv.clpv_cod_empr
						 and dire_cod_sucu = saeclpv.clpv_cod_sucu
						 ) as direccion
				from saeclpv
				where clpv_clopv_clpv = 'PV'
				and clpv_cod_empr = $id_empresa
				and clpv_cod_sucu ||'' like '$id_sucursal'";
		//echo $sql; exit;		
		unset($arrayProve);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$arrayProve[$oIfx->f('clpv_cod_clpv')] = array($oIfx->f('clpv_ruc_clpv'), $oIfx->f('telefono'), $oIfx->f('direccion'));
				} while ($oIfx->SiguienteRegistro());
			}
		}
		$oIfx->Free();
		//var_dump($arrayProve); exit;
		// TODOS LOS PROVEEDORES
		$fecha_fin   = date("Y-m-d", strtotime($fecha_fin));
		$sql_sp = "SELECT * FROM  sp_estado_cuenta_prove_web( $id_empresa,  0,  '$fecha_inicio',  '$fecha_fin' , 2 , '$id_sucursal', '$grupo', '$zona', '$factura', $ord, $remesa_sn, $anti_solo ); ";
		//		echo($sql_sp);
		//		exit;
		// saldo anterior		
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_inicio' ) group by 2 order by 2";
		unset($array_saldo);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$cod_clpv = $oIfx->f('clpv_cod_clpv');
					$saldo = $oIfx->f('saldo');
					if (empty($saldo)) {
						$saldo = 0;
					}
					$array_saldo[$cod_clpv] = $saldo;
				} while ($oIfx->SiguienteRegistro());
			} else {
				$array_saldo[0] = 0;
			}
		}
		$oIfx->Free();
	} else {
		// UN SOLO PROVEEDOR
		$sql = "select clpv_cod_clpv, clpv_ruc_clpv,
					(select max(tlcp_tlf_tlcp) from saetlcp 
					 where tlcp_cod_clpv = saeclpv.clpv_cod_clpv
					 and tlcp_cod_empr = saeclpv.clpv_cod_empr
					 and tlcp_cod_sucu = saeclpv.clpv_cod_sucu
					 ) as telefono,
					 (select max(dire_dir_dire) from saedire
					 where dire_cod_clpv = saeclpv.clpv_cod_clpv
					 and dire_cod_empr = saeclpv.clpv_cod_empr
					 and dire_cod_sucu = saeclpv.clpv_cod_sucu
					 ) as direccion
			from saeclpv
			where clpv_clopv_clpv = 'PV'
			and clpv_cod_empr = $id_empresa
			and clpv_cod_sucu ||'' like '$id_sucursal'
			and clpv_cod_clpv = $id_prove";
		unset($arrayProve);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$arrayProve[$oIfx->f('clpv_cod_clpv')] = array($oIfx->f('clpv_ruc_clpv'), $oIfx->f('telefono'), $oIfx->f('direccion'));
				} while ($oIfx->SiguienteRegistro());
			}
		}
		$oIfx->Free();
		//echo $sql; exit;
		$fecha_fin   = date("Y-m-d", strtotime($fecha_fin));
		$sql_sp = "SELECT * FROM sp_estado_cuenta_prove_web( $id_empresa,  $id_prove,  '$fecha_inicio',  '$fecha_fin' , 1 , '$id_sucursal', '$grupo', '$zona', '$factura', $ord, $remesa_sn, $anti_solo ); ";
		// echo $sql_sp; exit;
		// saldo anterior
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
					   AND ( saedmcp.clpv_cod_clpv = $id_prove  )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_inicio' ) group by 2 order by 2";
		//            $oReturn->alert($sql);
		unset($array_saldo);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$cod_clpv 	= $oIfx->f('clpv_cod_clpv');
					$saldo 		= $oIfx->f('saldo');
					$array_saldo[$cod_clpv] = $saldo;
				} while ($oIfx->SiguienteRegistro());
			} else {
				$array_saldo[0] = 0;
			}
		}
		$oIfx->Free();
	} // fin if

	$array_tip_tran = [];
	$sql = "SELECT tran_cod_tran, trans_tip_tran from saetran";
	if ($oIfx->Query($sql)) {
		if ($oIfx->NumFilas() > 0) {
			do {
				$array_tip_tran[$oIfx->f('tran_cod_tran')] = $oIfx->f('trans_tip_tran');
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oIfx->Free();

	$array_sucu = [];
	$sql = "SELECT sucu_cod_sucu, sucu_nom_sucu from saesucu";
	if ($oIfx->Query($sql)) {
		if ($oIfx->NumFilas() > 0) {
			do {
				$array_sucu[$oIfx->f('sucu_cod_sucu')] = $oIfx->f('sucu_nom_sucu');
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oIfx->Free();

	$contenido  = array();
	$cabecera   = array();
	$pie 		= array();
	$datos_tabla	= [];

	if ($op == 2) {
		$cabecera = array(
			"N°",
			"Emision",
			"Tran",
			"N°Factura",
			"Documento",
			"Vence",
			"Detalle",
			"Debito",
			"Credito",
			"Saldo"
		);
	}
	//$oReturn->alert('Buscando....');
	$x			 = 1;
	$total_nb 	 = 0;
	$total_cre 	 = 0;
	$total_sald  = 0;
	$sub_tot_deb = 0;
	$sub_tot_cre = 0;
	$sub_sald 	 = 0;
	$num = 1;
	unset($array_clie);
	if ($oIfx->Query($sql_sp)) {
		if ($oIfx->NumFilas() > 0) {
			do {
				$fec_emi 		= ($oIfx->f('fec_emis'));
				$cod_tran 		= $oIfx->f('cod_tran');
				$comprobante 	= $oIfx->f('comprob');
				$fact 			= $oIfx->f('num_fac');
				$fec_vence 		= ($oIfx->f('fec_vence'));
				$detalle 		= ($oIfx->f('detalle'));
				$debito 		= $oIfx->f('deb');
				$credito 		= $oIfx->f('cre');
				$proveedor		= ($oIfx->f('proveedor'));
				$clpv_cod_clpv  = $oIfx->f('cod_clpv');
				$user_web 		= $oIfx->f('user_web');
				$array_clie[$x] = $clpv_cod_clpv;
				$cod_sucu		= $oIfx->f('sucu_cod');
				$dmcp_cod_modu  = $oIfx->f('modu_cod');
				$ejer_cod       = $oIfx->f('ejer_cod');
				$prdo_cod	    = $oIfx->f('prdo_cod');
				//$nombre_sucu    = nombre_sucursal($cod_sucu);
				$ruc            = $arrayProve[$clpv_cod_clpv][0];
				$telefono 		= $arrayProve[$clpv_cod_clpv][1];
				$direccion 		= $arrayProve[$clpv_cod_clpv][2];

				$nombre_sucu	= "";
				if (isset($array_sucu[$cod_sucu])) {
					$nombre_sucu = $array_sucu[$cod_sucu];
				}
				// Adrian	

				//$sql_tran_tip = "select trans_tip_tran from saetran where tran_cod_tran = '$cod_tran'";
				//$tipo_transaccion = consulta_string($sql_tran_tip, 'trans_tip_tran', $oIfxA, '');

				$tipo_transaccion = "";
				if (isset($array_tip_tran[$cod_tran])) {
					$tipo_transaccion = $array_tip_tran[$cod_tran];
				}
				// DB -> Debito	
				// CR -> Crebito	


				if ($tipo_transaccion == 'CR' && substr($cod_tran, 0, 3) == 'NDB') {
					// echo(substr($cod_tran, 0, 3));exit;	
					if ($debito > 0) {
						$valor_debito = $debito;
					} else {
						$valor_debito = $credito;
					}
					$credito = $valor_debito;
					$debito = 0;
				} else if ($tipo_transaccion == 'DB' && substr($cod_tran, 0, 3) == 'NCR') {
					// echo(substr($cod_tran, 0, 3));exit;					
					if ($debito > 0) {
						$valor_credito = $debito;
					} else {
						$valor_credito = $credito;
					}
					$credito = 0;
					$debito = $valor_credito;
				} else {
					$credito = $credito;
					$debito = $debito;
				}



				if ($x == 1) {
					$saldo = $array_saldo[$clpv_cod_clpv];
					$sub_sald = $saldo;
					if ($sClass == 'off') $sClass = 'on';
					else $sClass = 'off';

					if ($op == 2) {

						$nsucursal = ' Sucursal: ' . $nombre_sucu;
						$nruc = ' Ruc: ' . $ruc;
						$nprove = ' Nombre: ' . $proveedor;
						$ntelf = ' Telf: ' . $telefono;
						$ndir = ' Dir: ' . $direccion;

						$array_datos_indi = array(
							$num,
							$nsucursal,
							$nruc,
							$nprove,
							$ntelf,
							"",
							$ndir,
							"",
							"SALDO ANTERIOR",
							number_format($saldo, 2, '.', '')
						);

						array_push($contenido, $array_datos_indi);
					} else {

						/* $tabla_reporte .= '<tr>';
						$tabla_reporte .='<td align="center">'.$num.'</td>';
						$tabla_reporte .='<td><b>Sucursal: ' . $nombre_sucu . '</b></td>';
						$tabla_reporte .='<td><b>Ruc: ' . $ruc . '</b></td>';
						$tabla_reporte .='<td><b>Nombre: ' . $proveedor . '</b></td>';
						$tabla_reporte .='<td><b>Telf: ' . $telefono . '</b></td>';
						$tabla_reporte .= '<td></td>';
						$tabla_reporte .='<td><b>Dir: ' . $direccion . '</b></td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:left;"></td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right;"><b>SALDO ANTERIOR:</b></td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right;"><b>' . number_format($saldo, 2, '.', '') . '</b></td>';
						$tabla_reporte .= '</tr>'; */

						//$nsucursal=' Sucursal: '.$nombre_sucu;
						//$nruc=' Ruc: '.$ruc;
						//$nprove=' Nombre: '.$proveedor;
						//$ntelf=' Telf: '.$telefono;
						//$ndir=' Dir: '.$direccion;

						$data = [
							"num" => $num,
							"sucursal" => "Sucursal: $nombre_sucu",
							"ruc" => "Ruc: $ruc",
							"nombre" => "Nombre: $proveedor",
							"telefono" => "Telf: $telefono",
							"num_depo" => "",
							"vence" => "",
							"direccion" => "Dir: $direccion",
							"debito" => "",
							"credito" => "SALDO ANTERIOR",
							"saldo" => number_format($saldo, 2, '.', '')
						];

						array_push($datos_tabla, $data);

						/* $array_datos_indi = array(
							$num,
							'<b>Sucursal: ' . $nombre_sucu . '</b>',
							'<b>Ruc: ' . $ruc . '</b>',
							'<b>Nombre: ' . $proveedor . '</b>',
							'<b>Telf: ' . $telefono . '</b>',
							"",
							'<b>Dir: ' . $direccion . '</b>',
							"",
							"<b>SALDO ANTERIOR:</b>",
							'<b>' . number_format($saldo, 2, '.', '') . '</b>'
						);

						array_push($contenido, $array_datos_indi); */
					}
					$num++;
				} elseif ($x > 1) {
					if ($array_clie[$x] != $array_clie[$x - 1]) {
						if ($sClass == 'off') $sClass = 'on';
						else $sClass = 'off';
						// subtotales

						if ($op == 2) {
							$array_datos_indi = array(
								$num,
								"",
								"",
								"",
								"",
								"",
								"TOTAL:",
								number_format($sub_tot_deb, 2, '.', ''),
								number_format($sub_tot_cre, 2, '.', ''),
								number_format($saldo, 2, '.', '')
							);
							array_push($contenido, $array_datos_indi);
						} else {
							/* $tabla_reporte .= '<tr  height="20">';
							$tabla_reporte .='<td align="center">'.$num.'</td>';
							$tabla_reporte .= '<td></td>';
							$tabla_reporte .= '<td></td>';
							$tabla_reporte .= '<td></td>';
							$tabla_reporte .= '<td></td>';
							$tabla_reporte .= '<td></td>';
	
							$tabla_reporte .= '<td style="font-size: 10px; text-align:right;"><b>TOTAL:</b></td>';
							$tabla_reporte .= '<td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_deb, 2, '.', '') . '</td>
											   <td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_cre, 2, '.', '') . '</td>
											   <td style="font-size: 10px; text-align:right;">' . $saldo . '</td>										   
											   </tr>'; */
							/* $array_datos_indi = array(
												$num,
												"",
												"",
												"",
												"",
												"",
												"<b>TOTAL:</b>",
												number_format($sub_tot_deb, 2, '.', ''),
												number_format($sub_tot_cre, 2, '.', ''),
												number_format($saldo, 2, '.', '')
											);
							array_push($contenido, $array_datos_indi); */


							$data = [
								"num" => $num,
								"sucursal" => "",
								"ruc" => "",
								"nombre" => "",
								"telefono" => "",
								"num_depo" => "",
								"vence" => "",
								"direccion" => "TOTAL",
								"debito" => number_format($sub_tot_deb, 2, '.', ''),
								"credito" => number_format($sub_tot_cre, 2, '.', ''),
								"saldo" => number_format($saldo, 2, '.', '')
							];

							array_push($datos_tabla, $data);
						}

						$num++;
						// inicio otro PROVE
						$saldo = $array_saldo[$clpv_cod_clpv];
						$sub_sald = $saldo;

						if ($op == 2) {
							$array_datos_indi = array(
								$num,
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								""
							);
							array_push($contenido, $array_datos_indi);
						} else {
							/* $tabla_reporte .= '<tr  height="20" >';
							$tabla_reporte .='<td align="center">'.$num.'</td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
							$tabla_reporte .= '</tr>'; */
							/* $array_datos_indi = array(
								$num,
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								"",
								""
							);
							array_push($contenido, $array_datos_indi); */

							$data = [
								"num" => $num,
								"sucursal" => "",
								"ruc" => "",
								"nombre" => "",
								"telefono" => "",
								"num_depo" => "",
								"vence" => "",
								"direccion" => "",
								"debito" => "",
								"credito" => "",
								"saldo" => ""
							];

							array_push($datos_tabla, $data);
						}
						$num++;
						if ($op == 2) {
							$nsucursal = ' Sucursal: ' . $nombre_sucu;
							$nruc = ' Ruc: ' . $ruc;
							$nprove = ' Nombre: ' . $proveedor;
							$ntelf = ' Telf: ' . $telefono;
							$ndir = ' Dir: ' . $direccion;

							$array_datos_indi = array(
								$num,
								$nsucursal,
								$nruc,
								$nprove,
								$ntelf,
								"",
								$ndir,
								"",
								"SALDO ANTERIOR",
								number_format($saldo, 2, '.', '')
							);
							array_push($contenido, $array_datos_indi);
						} else {
							/* $tabla_reporte .= '<tr  height="20">';
								$tabla_reporte .='<td align="center">'.$num.'</td>';
								$tabla_reporte .='<td><b>Sucursal: ' . $nombre_sucu . '</b></td>';
								$tabla_reporte .='<td><b>Ruc: ' . $ruc . '</b></td>';
								$tabla_reporte .='<td><b>Nombre: ' . $proveedor . '</b></td>';
								$tabla_reporte .='<td><b>Telf: ' . $telefono . '</b></td>';
								$tabla_reporte .= '<td  style="font-size: 10px; text-align:left;"></td>';
								$tabla_reporte .='<td><b>Dir: ' . $direccion . '</b></td>';
								$tabla_reporte .= '<td  style="font-size: 10px; text-align:left;"></td>';
								$tabla_reporte .= '<td style="text-align:right; font-size: 10px;"><b>SALDO ANTERIOR: </b></td>';
								$tabla_reporte .= '<td style="text-align:right; font-size: 10px;"><b>' . number_format($saldo, 2, '.', '') . '</b></td>';
								$tabla_reporte .= '</tr>'; */
							/* $array_datos_indi = array(
								$num,
								'<b>Sucursal: ' . $nombre_sucu . '</b>',
								'<b>Ruc: ' . $ruc . '</b>',
								'<b>Nombre: ' . $proveedor . '</b>',
								'<b>Telf: ' . $telefono . '</b>',
								"",
								'<b>Dir: ' . $direccion . '</b>',
								"",
								"<b>SALDO ANTERIOR: </b>",
								'<b>' . number_format($saldo, 2, '.', '') . '</b>'
							);
							array_push($contenido, $array_datos_indi); */

							$data = [
								"num" => $num,
								"sucursal" => "Sucursal: $nombre_sucu",
								"ruc" => "Ruc: $ruc",
								"nombre" => "Nombre: $proveedor",
								"telefono" => "Telf: $telefono",
								"num_depo" => "",
								"vence" => "",
								"direccion" => "Dir: $direccion",
								"debito" => "",
								"credito" => "SALDO ANTERIOR: ",
								"saldo" => number_format($saldo, 2, '.', '')
							];

							array_push($datos_tabla, $data);
						}
						$num++;

						$sub_tot_deb = 0;
						$sub_tot_cre = 0;
					} // fin if

				}

				$saldo = $saldo + $debito - $credito;
				//$nombre_tran = nombre_transaccion($cod_tran, $cod_sucu, $dmcp_cod_modu);
				//$nombre_sucu = nombre_sucursal($cod_sucu);

				if ($sClass == 'off') $sClass = 'on';
				else $sClass = 'off';

				if ($op == 2) {
					$array_datos_indi = array(
						$num,
						formato_fecha_jire($fec_emi),
						$cod_tran,
						$comprobante,
						$fact,
						formato_fecha_jire($fec_vence),
						$detalle,
						number_format($debito, 2, '.', ''),
						number_format($credito, 2, '.', ''),
						number_format($saldo, 2, '.', '')
					);
					array_push($contenido, $array_datos_indi);
				} else {
					/* if (($x % 2) == 0) {
							$tabla_reporte .= '<tr height="20">';
						} else {
							$tabla_reporte .= '<tr height="20" bgcolor="#B2ABAA">';
						}
						$tabla_reporte .='<td align="center">'.$num.'</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:center; ">' . formato_fecha_jire($fec_emi) . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:center; ">' . $cod_tran . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:center; "> <a href="#" onclick="seleccionaItem(' . $id_empresa . ', ' . $cod_sucu . ', ' . $ejer_cod . ', ' . $prdo_cod . ', \'' . $comprobante . '\');" style="color:blue;">' . $comprobante . '&nbsp;</a> </td>';


						$tabla_reporte .= '<td style="font-size: 10px; text-align:left; ">' . $fact . '</td>';

						$tabla_reporte .= '<td style="font-size: 10px; text-align:center; ">' . formato_fecha_jire($fec_vence) . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:left; ">' . $detalle . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right; ">' . number_format($debito, 2, '.', '') . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right; ">' . number_format($credito, 2, '.', '') . '</td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right; ">' . number_format($saldo, 2, '.', '') . '</td>';
						$tabla_reporte .= '</tr>'; */

					/* if (($x % 2) == 0) {
							$clase_rep .= '';
						} else {
							$clase_rep .= 'bg-primary';
						}

						$array_datos_indi = array(
							//"clase_tr" => $clase_rep,
							$num,
							formato_fecha_jire($fec_emi),
							$cod_tran,
							' <a href="#" onclick="seleccionaItem(' . $id_empresa . ', ' . $cod_sucu . ', ' . $ejer_cod . ', ' . $prdo_cod . ', \'' . $comprobante . '\');" style="color:blue;">' . $comprobante . '&nbsp;</a>',
							$fact,
							formato_fecha_jire($fec_vence),
							$detalle,
							number_format($debito, 2, '.', ''),
							number_format($credito, 2, '.', ''),
							number_format($saldo, 2, '.', '')
						);
						array_push($contenido, $array_datos_indi); */

					$datos_item = [
						"id_empresa" => $id_empresa,
						"cod_sucu" => $cod_sucu,
						"ejer_cod" => $ejer_cod,
						"prdo_cod" => $prdo_cod,
						"comprobante" => $comprobante
					];


					$class = new GeneraDetalleAsientoContable();
					$arrayDiario = $class->diarioAsientoContable($oIfxA, $id_empresa, $cod_sucu, $ejer_cod, $prdo_cod, $comprobante);
					$num_depo_ad = '';
					foreach ($arrayDiario as $val) {
						$dasi_num_depo = $val[6];
						if (!empty($dasi_num_depo)) {
							$num_depo_ad = $dasi_num_depo;
						}
					}

					$data = [
						"num" => $num,
						"sucursal" => $fec_emi,
						"ruc" => $cod_tran,
						"nombre" => $datos_item,
						"telefono" => $fact,
						"num_depo" => $num_depo_ad,
						"vence" => $fec_vence,
						"direccion" => $detalle,
						"debito" => floatval(number_format($debito, 2, '.', '')),
						"credito" => floatval(number_format($credito, 2, '.', '')),
						"saldo" => floatval(number_format($saldo, 2, '.', ''))
					];

					array_push($datos_tabla, $data);
				}
				$x++;
				$total_nb += $debito;
				$total_cre += $credito;
				$sub_tot_deb += $debito;
				$sub_tot_cre += $credito;
				$num++;
			} while ($oIfx->SiguienteRegistro());

			if ($op == 2) {
				$array_datos_indi = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"TOTAL:",
					number_format($sub_tot_deb, 2, '.', ''),
					number_format($sub_tot_cre, 2, '.', ''),
					number_format($saldo, 2, '.', '')
				);
				array_push($contenido, $array_datos_indi);
			} else {

				/* // subtotales
				$tabla_reporte .= '<tr  height="20">';
				$tabla_reporte .='<td align="center">'.$num.'</td>';
				$tabla_reporte .= '<td></td>';
				$tabla_reporte .= '<td></td>';
				$tabla_reporte .= '<td></td>';
				$tabla_reporte .= '<td></td>';
				$tabla_reporte .= '<td></td>';
				$tabla_reporte .= '<td style="font-size: 12px; text-align:right;" ><b>TOTAL:</b></td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_deb, 2, '.', '') . '</td>
									<td style="font-size: 10px; text-align:right;" >' . number_format($sub_tot_cre, 2, '.', '') . '</td>
									<td style="font-size: 10px; text-align:right;" >' . number_format($saldo, 2, '.', '') . '</td>
									</tr>'; */
				/* $array_datos_indi = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"<b>TOTAL:</b>",
					number_format($sub_tot_deb, 2, '.', ''),
					number_format($sub_tot_cre, 2, '.', ''),
					number_format($saldo, 2, '.', '')
				);
				array_push($contenido, $array_datos_indi); */

				$data = [
					"num" => $num,
					"sucursal" => "",
					"ruc" => "",
					"nombre" => "",
					"telefono" => "",
					"num_depo" => "",
					"vence" => "",
					"direccion" => "TOTAL:",
					"debito" => number_format($sub_tot_deb, 2, '.', ''),
					"credito" => number_format($sub_tot_cre, 2, '.', ''),
					"saldo" => number_format($saldo, 2, '.', '')
				];

				array_push($datos_tabla, $data);
			}
			$num++;

			if ($op == 2) {
				$array_datos_indi = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					""
				);
				array_push($contenido, $array_datos_indi);
			} else {
				/* $tabla_reporte .= '<tr  height="20" style="font-size: 11px; border-bottom: 1px solid;  text-align:center;">';
						$tabla_reporte .='<td align="center">'.$num.'</td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '<td style="font-size: 11px; border-bottom: 1px solid;"></td>';
						$tabla_reporte .= '</tr>'; */
				/* $array_datos_indi = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					""
				);
				array_push($contenido, $array_datos_indi); */

				$data = [
					"num" => $num,
					"sucursal" => "",
					"ruc" => "",
					"nombre" => "",
					"telefono" => "",
					"num_depo" => "",
					"vence" => "",
					"direccion" => "",
					"debito" => "",
					"credito" => "",
					"saldo" => ""
				];

				array_push($datos_tabla, $data);
			}
			$num++;

			if ($op == 2) {
				$array_datos_indi = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"TOTALES:",
					number_format($total_nb, 2, '.', ''),
					number_format($total_cre, 2, '.', ''),
					number_format($total_nb - $total_cre + $sub_sald, 2, '.', '')
				);

				array_push($contenido, $array_datos_indi);
			} else {

				// totales
				/* $tabla_reporte .= '<tr  height="20">';
					$tabla_reporte .='<td align="center">'.$num.'</td>';
					$tabla_reporte .= '<td></td>';
					$tabla_reporte .= '<td></td>';
					$tabla_reporte .= '<td></td>';
					$tabla_reporte .= '<td></td>';
					$tabla_reporte .= '<td></td>';
					$tabla_reporte .= '<td style="font-size: 12px; text-align:right;color:red"><b>TOTALES:</b></td>';
					$tabla_reporte .= '<td style="font-size: 12px; text-align:right;"><b>' . number_format($total_nb, 2, '.', '') . '</b></td>
										<td style="font-size: 12px; text-align:right;"><b>' . number_format($total_cre, 2, '.', '') . '</b></td>
										<td style="font-size: 12px; text-align:right;"><b>' . number_format($total_nb - $total_cre + $sub_sald, 2, '.', '') . '</b></td>
										</tr>'; */
				/* $array_datos_indi = array(
											$num,
											"",
											"",
											"",
											"",
											"",
											"<b>TOTALES:</b>",
											number_format($total_nb, 2, '.', ''),
											number_format($total_cre, 2, '.', ''),
											number_format($total_nb - $total_cre + $sub_sald, 2, '.', '')
										);
						
										array_push($contenido, $array_datos_indi); */


				$pie = array(
					$num,
					"",
					"",
					"",
					"",
					"",
					"<b>TOTALES:</b>",
					number_format($total_nb, 2, '.', ''),
					number_format($total_cre, 2, '.', ''),
					number_format($total_nb - $total_cre + $sub_sald, 2, '.', '')
				);

				$data = [
					"num" => $num,
					"sucursal" => "",
					"ruc" => "",
					"nombre" => "",
					"telefono" => "",
					"num_depo" => "",
					"vence" => "",
					"direccion" => "TOTALES:",
					"debito" => number_format($total_nb, 2, '.', ''),
					"credito" => number_format($total_cre, 2, '.', ''),
					"saldo" => number_format($total_nb - $total_cre + $sub_sald, 2, '.', '')
				];

				array_push($datos_tabla, $data);
			}
			$num++;
		}
	}
	$oIfx->Free();
	/* if($op!=2){
			$tabla_reporte .= '</tbody>';
			$tabla_reporte .= '</table>';
		} */

	if ($op == 2) {

		$nombre_archivo = 'Estado_Cuenta_Proveedores';

		$datos_envio = array(
			"cabecera" => array($cabecera),
			"contenido" => $contenido,
			"nombre_archivo" => $nombre_archivo
		);

		$headers = array(
			"Content-Type:application/json"
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, URL_JIREH_DOCUMENTOS . "/reporte/convertir/excel");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_envio));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$archivo_excel = curl_exec($ch);

		$nombre_archivo = $nombre_archivo . ".xlsx";

		$ruta_carpeta = "exportado/" . $nombre_archivo; // Reemplaza con la ruta deseada

		if (!file_exists("exportado")) {
			mkdir("exportado", 0777, true);
		}

		chmod("exportado", 0777);

		file_put_contents($ruta_carpeta, $archivo_excel);

		$oReturn->script('$("#enlace_descarga").attr("href", \'' . $ruta_carpeta . '\');');
		$oReturn->script('document.getElementById("enlace_descarga").click();');
		$oReturn->script('jsRemoveWindowLoad()');
	} else {

		/* $sql = "select trim(empr_nom_empr) as empr_nom_empr, 
						trim(empr_dir_empr) as empr_dir_empr,
						trim(empr_ruc_empr) as empr_ruc_empr
				from saeempr
				where empr_cod_empr = $id_empresa";
				$nombreEmpresa = consulta_string($sql, 'empr_nom_empr', $oIfx, '');
				$direccionEmpresa = consulta_string($sql, 'empr_dir_empr', $oIfx, '');
				$rucEmpresa = consulta_string($sql, 'empr_ruc_empr', $oIfx, '');



				//Armado Cabecera Excel
				unset($_SESSION['sHtml_cab']);
				unset($_SESSION['sHtml_det']);
				$sHtml_exe_p .= '<table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
						<tr>
								<th style="font-size: 12px;" align="center" colspan = "11">' . $nombreEmpresa . '</th>
						</tr>
						<tr>
								<th style="font-size: 10px;" align="center" colspan = "11">RUC: ' . $rucEmpresa . '</th>
						</tr>	
						<tr>
								<th style="font-size: 10px;" align="center" colspan = "11">DIR: ' . $direccionEmpresa . '</th>
						</tr>							
						<tr>
								<th style="font-size: 12px;" colspan = "11" align="center"><br>ESTADO DE CUENTA - PROVEEDORES</th>
						</tr>					   
						<tr>
								<th style="font-size: 10px;" align="center" colspan="11">Fecha Reporte: ' . date("d/m/Y") . '</th>
						</tr>                            
					</table>';
					$table = '';
					//arma pdf
					$table .= '<page footer="date;heure;page" style="font-size: 9px width: 95%">';
					$table .= $sHtml_exe_p . $tabla_reporte;
					$table .= '</page>';


					//$_SESSION['sHtml_cab'] = $sHtml_exe_p;
					//$_SESSION['sHtml_det'] = $tabla_reporte;
					//$_SESSION['pdf'] = $table;
					$oReturn->assign("DivReporte", "innerHTML", $tabla_reporte);

					$titulo= $nombreEmpresa.'\nRUC: '.$rucEmpresa.'\nDIRECCION: '.$direccionEmpresa.'\nESTADO DE CUENTA PROVEEDOR\nFecha Reporte: '.date("d-m-Y");
					$oReturn->script('jsRemoveWindowLoad()');
					$oReturn->script("initTablaEdit('tb_prove','$titulo');"); */

		//$nombre_archivo = 'Estado_cuenta_proveedores';

		//foreach ($contenido as $elemento) {
		//	$elemento_sin_tabulaciones = str_replace("\t", "", $elemento);
		//	$elemento_sin_tabulaciones = preg_replace('/[\x00-\x1F]/u', '', $elemento_sin_tabulaciones);
		//	$contenido_2[] = $elemento_sin_tabulaciones;
		//}

		/* $datos_envio = array(
						"cabecera" => array($cabecera),
						"contenido" => $contenido,
						"pie" => $pie,
						"nombre_archivo" => $nombre_archivo
					); */

		//var_dump($datos_envio);
		//exit;

		if (count($datos_tabla) > 0) {
			$tabla = ["data" => $datos_tabla];
			$respuesta = base64_encode(json_encode($tabla));

			$oReturn->script('generar_reporte(\'' . $respuesta . '\');');
		} else {
			$oReturn->alert("Sin datos para mostrar");
			$oReturn->script('jsRemoveWindowLoad();');
			return $oReturn;
		}
	}

	return $oReturn;
}
function reporte_pdf_excel($aForm = '')
{
	global $DSN_Ifx, $DSN;

	session_start();

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();


	$oIfxA = new Dbo();
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();

	$oReturn = new xajaxResponse();

	// VARIABLES
	$user_web 	 = $_SESSION['U_ID'];
	$id_empresa  = $aForm['empresa'];
	$id_sucursal = trim($aForm['sucursal']);
	$grupo       = trim($aForm['grupo']);
	$zona        = trim($aForm['zona']);
	$moneda      = $aForm['moneda'];
	$id_prove    = $aForm['clpv_cod_clpv'];
	$factura     = trim($aForm['factura']);
	$anio		 = $aForm['anio'];
	$mes		 = $aForm['mes'];
	$orden		 = $aForm['orden'];
	$fecha_rb    = $aForm['fecha'];
	$anticipos 	 = $aForm['remesa'];
	$cliente 	 = $aForm['cliente'];
	$solo_anti 	 = $aForm['anticipos'];

	if ($fecha_rb == 'f') {
		$fecha_inicio = $aForm['fecha_inicio'];
		$fecha_inicio = date("Y-m-d", strtotime($fecha_inicio));
		$fecha_fin = $aForm['fecha_fin'];
		$sql = "select ejer_cod_ejer 
				from saeejer 
				where DATE_PART('year', ejer_fec_inil) = DATE_PART('year', TIMESTAMP '$fecha_inicio')
				and ejer_cod_empr = $id_empresa ";
		//echo $sql; exit;		
		$anio = consulta_string($sql, 'ejer_cod_ejer', $oIfx, 0);
	} elseif ($fecha_rb == 'm') {
		$anio = $aForm['anio'];
		$mes = $aForm['mes'];
		$sql = "select prdo_fec_ini, prdo_fec_fin 
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = $mes
				and prdo_cod_ejer = $anio";
		$fecha_inicio = consulta_string($sql, 'prdo_fec_ini', $oIfx, '');
		$fecha_fin = consulta_string($sql, 'prdo_fec_fin', $oIfx, '');
	} elseif ($fecha_rb == 'a') {
		$anio = $aForm['anio'];
		$sql = "select prdo_fec_ini
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = 1
				and prdo_cod_ejer = $anio";

		$fecha_inicio = consulta_string($sql, 'prdo_fec_ini', $oIfx, '');
		$sql = "select prdo_fec_fin
				from saeprdo 
				where prdo_cod_empr = $id_empresa
				and prdo_num_prdo = 12
				and prdo_cod_ejer = $anio";
		$fecha_fin = consulta_string($sql, 'prdo_fec_fin', $oIfx, '');
	}


	if ($solo_anti == 'S') {
		$anti_solo = 1;
	} else {
		$anti_solo = 0;
	}
	//echo $anti_solo; exit;
	if ($anticipos == 'S') {
		$remesa_sn = 1;
	} else {
		$remesa_sn = 0;
	}
	//echo $anticipos.'-'.$remesa_sn; exit;
	if (empty($id_sucursal)) {
		$id_sucursal = '%';
	}
	if (empty($grupo)) {
		$grupo = '%';
	}
	if (empty($zona)) {
		$zona = '%';
	}
	if (empty($moneda)) {
		$moneda = '%';
	}
	if (empty($factura)) {
		$factura = '%';
	}

	//  LECTURA SUCIA
	//////////////

	$sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $id_empresa";
	$moneda_base = consulta_string($sql_moneda, 'pcon_mon_base', $oIfx, '0');

	if ($orden == 'factura') {
		$ord = 1;
	} else {
		$ord = 2;
	}


	//echo $id_prove.'-'.$cliente; exit;
	if (empty($cliente)) { // Todos
		// DATOS DEL PROVEEDOR
		$sql = "select clpv_cod_clpv, clpv_ruc_clpv,
						(select max(tlcp_tlf_tlcp) from saetlcp 
						 where tlcp_cod_clpv = saeclpv.clpv_cod_clpv
						 and tlcp_cod_empr = saeclpv.clpv_cod_empr
						 and tlcp_cod_sucu = saeclpv.clpv_cod_sucu
						 ) as telefono,
						 (select max(dire_dir_dire) from saedire
						 where dire_cod_clpv = saeclpv.clpv_cod_clpv
						 and dire_cod_empr = saeclpv.clpv_cod_empr
						 and dire_cod_sucu = saeclpv.clpv_cod_sucu
						 ) as direccion
				from saeclpv
				where clpv_clopv_clpv = 'PV'
				and clpv_cod_empr = $id_empresa
				and clpv_cod_sucu ||'' like '$id_sucursal'";
		//echo $sql; exit;		
		unset($arrayProve);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$arrayProve[$oIfx->f('clpv_cod_clpv')] = array($oIfx->f('clpv_ruc_clpv'), $oIfx->f('telefono'), $oIfx->f('direccion'));
				} while ($oIfx->SiguienteRegistro());
			}
		}
		$oIfx->Free();
		//var_dump($arrayProve); exit;
		// TODOS LOS PROVEEDORES
		$fecha_fin   = date("Y-m-d", strtotime($fecha_fin));
		$sql_sp = "SELECT * FROM  sp_estado_cuenta_prove_web( $id_empresa,  0,  '$fecha_inicio',  '$fecha_fin' , 2 , '$id_sucursal', '$grupo', '$zona', '$factura', $ord, $remesa_sn, $anti_solo ); ";
		//		echo($sql_sp);
		//		exit;
		// saldo anterior		
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_inicio' ) group by 2 order by 2";
		unset($array_saldo);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$cod_clpv = $oIfx->f('clpv_cod_clpv');
					$saldo = $oIfx->f('saldo');
					if (empty($saldo)) {
						$saldo = 0;
					}
					$array_saldo[$cod_clpv] = $saldo;
				} while ($oIfx->SiguienteRegistro());
			} else {
				$array_saldo[0] = 0;
			}
		}
		$oIfx->Free();
	} else {
		// UN SOLO PROVEEDOR
		$sql = "select clpv_cod_clpv, clpv_ruc_clpv,
					(select max(tlcp_tlf_tlcp) from saetlcp 
					 where tlcp_cod_clpv = saeclpv.clpv_cod_clpv
					 and tlcp_cod_empr = saeclpv.clpv_cod_empr
					 and tlcp_cod_sucu = saeclpv.clpv_cod_sucu
					 ) as telefono,
					 (select max(dire_dir_dire) from saedire
					 where dire_cod_clpv = saeclpv.clpv_cod_clpv
					 and dire_cod_empr = saeclpv.clpv_cod_empr
					 and dire_cod_sucu = saeclpv.clpv_cod_sucu
					 ) as direccion
			from saeclpv
			where clpv_clopv_clpv = 'PV'
			and clpv_cod_empr = $id_empresa
			and clpv_cod_sucu ||'' like '$id_sucursal'
			and clpv_cod_clpv = $id_prove";
		unset($arrayProve);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$arrayProve[$oIfx->f('clpv_cod_clpv')] = array($oIfx->f('clpv_ruc_clpv'), $oIfx->f('telefono'), $oIfx->f('direccion'));
				} while ($oIfx->SiguienteRegistro());
			}
		}
		$oIfx->Free();
		//echo $sql; exit;
		$fecha_fin   = date("Y-m-d", strtotime($fecha_fin));
		$sql_sp = "SELECT * FROM sp_estado_cuenta_prove_web( $id_empresa,  $id_prove,  '$fecha_inicio',  '$fecha_fin' , 1 , '$id_sucursal', '$grupo', '$zona', '$factura', $ord, $remesa_sn, $anti_solo ); ";
		// echo $sql_sp; exit;
		// saldo anterior
		$sql = "SELECT  ( sum(saedmcp.dcmp_deb_ml ) - sum(saedmcp.dcmp_cre_ml ) )  as saldo ,  clpv_cod_clpv  FROM saedmcp
						WHERE ( saedmcp.dmcp_cod_empr = $id_empresa )
					   AND ( saedmcp.clpv_cod_clpv = $id_prove  )
						AND ( saedmcp.dcmp_fec_emis < '$fecha_inicio' ) group by 2 order by 2";
		//            $oReturn->alert($sql);
		unset($array_saldo);
		if ($oIfx->Query($sql)) {
			if ($oIfx->NumFilas() > 0) {
				do {
					$cod_clpv 	= $oIfx->f('clpv_cod_clpv');
					$saldo 		= $oIfx->f('saldo');
					$array_saldo[$cod_clpv] = $saldo;
				} while ($oIfx->SiguienteRegistro());
			} else {
				$array_saldo[0] = 0;
			}
		}
		$oIfx->Free();
	} // fin if

	$tabla_reporte .= '<table class="table table-striped table-condensed table-bordered table-hover" style="width: 90%; margin-top: 18px; font-size: 10px;" align="center">';
	$tabla_reporte .= '<thead><tr>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 8%;">Emision</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 5%;">Tran</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 10%;">Documento</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 15%;">No- Factura</th>							
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 7%;">Vence</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 25%;">Detalle</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 10%;">Debito</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 10%;">Credito</th>
							<th scope="col" class="bg-primary" style="font-size: 12px; border-bottom: 1px solid; border-top: 1px solid; text-align:center; width: 10%;">Saldo</th>
					  </tr></thead>';
	$tabla_reporte .= '<tbody>';
	$oReturn->alert('Buscando....');
	$x			 = 1;
	$total_nb 	 = 0;
	$total_cre 	 = 0;
	$total_sald  = 0;
	$sub_tot_deb = 0;
	$sub_tot_cre = 0;
	$sub_sald 	 = 0;
	unset($array_clie);
	if ($oIfx->Query($sql_sp)) {
		if ($oIfx->NumFilas() > 0) {
			do {
				$fec_emi 		= ($oIfx->f('fec_emis'));
				$cod_tran 		= $oIfx->f('cod_tran');
				$comprobante 	= $oIfx->f('comprob');
				$fact 			= $oIfx->f('num_fac');
				$fec_vence 		= ($oIfx->f('fec_vence'));
				$detalle 		= ($oIfx->f('detalle'));
				$debito 		= $oIfx->f('deb');
				$credito 		= $oIfx->f('cre');
				$proveedor		= ($oIfx->f('proveedor'));
				$clpv_cod_clpv  = $oIfx->f('cod_clpv');
				$user_web 		= $oIfx->f('user_web');
				$array_clie[$x] = $clpv_cod_clpv;
				$cod_sucu		= $oIfx->f('sucu_cod');
				$dmcp_cod_modu  = $oIfx->f('modu_cod');
				$ejer_cod       = $oIfx->f('ejer_cod');
				$prdo_cod	    = $oIfx->f('prdo_cod');
				$nombre_sucu    = nombre_sucursal($cod_sucu);
				$ruc            = $arrayProve[$clpv_cod_clpv][0];
				$telefono 		= $arrayProve[$clpv_cod_clpv][1];
				$direccion 		= $arrayProve[$clpv_cod_clpv][2];

				// Adrian	

				$sql_tran_tip = "select trans_tip_tran from saetran where tran_cod_tran = '$cod_tran'";
				$tipo_transaccion = consulta_string($sql_tran_tip, 'trans_tip_tran', $oIfxA, '');
				// DB -> Debito	
				// CR -> Crebito	


				if ($tipo_transaccion == 'CR' && substr($cod_tran, 0, 3) == 'NDB') {
					// echo(substr($cod_tran, 0, 3));exit;	
					if ($debito > 0) {
						$valor_debito = $debito;
					} else {
						$valor_debito = $credito;
					}
					$credito = $valor_debito;
					$debito = 0;
				} else if ($tipo_transaccion == 'DB' && substr($cod_tran, 0, 3) == 'NCR') {
					// echo(substr($cod_tran, 0, 3));exit;					
					if ($debito > 0) {
						$valor_credito = $debito;
					} else {
						$valor_credito = $credito;
					}
					$credito = 0;
					$debito = $valor_credito;
				} else {
					$credito = $credito;
					$debito = $debito;
				}



				if ($x == 1) {
					$saldo = $array_saldo[$clpv_cod_clpv];
					$sub_sald = $saldo;
					if ($sClass == 'off') $sClass = 'on';
					else $sClass = 'off';
					$tabla_reporte .= '<tr>';
					$tabla_reporte .= '<td colspan="6" style="font-size: 10px; text-align:left;"><b>Nombre: ' . $proveedor . '&nbsp;&nbsp;&nbsp;&nbsp;Sucursal: ' . $nombre_sucu . '&nbsp;&nbsp;&nbsp;&nbsp;Ruc: ' . $ruc . '&nbsp;&nbsp;&nbsp;&nbsp;Telf: ' . $telefono . '&nbsp;&nbsp;&nbsp;&nbsp;Dir: ' . $direccion . '</b></td>';
					$tabla_reporte .= '<td colspan="2" style="font-size: 10px; text-align:right;"><b>SALDO ANTERIOR:</b></td>';
					$tabla_reporte .= '<td style="font-size: 10px; text-align:right;"><b>' . number_format($saldo, 2, '.', ',') . '</b></td>';
					$tabla_reporte .= '</tr>';
				} elseif ($x > 1) {
					if ($array_clie[$x] != $array_clie[$x - 1]) {
						if ($sClass == 'off') $sClass = 'on';
						else $sClass = 'off';
						// subtotales
						$tabla_reporte .= '<tr  height="20">';
						$tabla_reporte .= '<td colspan="6" style="font-size: 10px; text-align:right;"><b>TOTAL:</b></td>';
						$tabla_reporte .= '<td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_deb, 2, '.', ',') . '</td>
										   <td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_cre, 2, '.', ',') . '</td>
										   <td style="font-size: 10px; text-align:right;">' . $saldo . '</td>										   
										   </tr>';

						// inicio otro PROVE
						$saldo = $array_saldo[$clpv_cod_clpv];
						$sub_sald = $saldo;
						$tabla_reporte .= '<tr  height="20"><td colspan="9" style="font-size: 11px; border-bottom: 1px solid;  text-align:center;"></td></tr>';
						$tabla_reporte .= '<tr  height="20">';
						$tabla_reporte .= '<td colspan="6" style="font-size: 10px; text-align:left;"><b>Nombre: ' . $proveedor . '&nbsp;&nbsp;&nbsp;&nbsp;Sucursal: ' . $nombre_sucu . '&nbsp;&nbsp;&nbsp;&nbsp;Ruc: ' . $ruc . '&nbsp;&nbsp;&nbsp;&nbsp;Telf: ' . $telefono . '&nbsp;&nbsp;&nbsp;&nbsp;Dir: ' . $direccion . '</b></td>';
						$tabla_reporte .= '<td colspan="2" style="text-align:right; font-size: 10px;"><b>SALDO ANTERIOR: </b></td>';
						$tabla_reporte .= '<td style="text-align:right; font-size: 10px;"><b>' . number_format($saldo, 2, '.', ',') . '</b></td>';
						$tabla_reporte .= '</tr>';

						$sub_tot_deb = 0;
						$sub_tot_cre = 0;
					} // fin if

				}

				$saldo = $saldo + $debito - $credito;
				//$nombre_tran = nombre_transaccion($cod_tran, $cod_sucu, $dmcp_cod_modu);
				//$nombre_sucu = nombre_sucursal($cod_sucu);

				if ($sClass == 'off') $sClass = 'on';
				else $sClass = 'off';
				if (($x % 2) == 0) {
					$tabla_reporte .= '<tr height="20">';
				} else {
					$tabla_reporte .= '<tr height="20" bgcolor="#B2ABAA">';
				}

				$tabla_reporte .= '<td style="font-size: 10px; text-align:center; width: 8%;">' . formato_fecha_jire($fec_emi) . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:center; width: 5%;">' . $cod_tran . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:center; width: 10%;"> <a href="#" onclick="seleccionaItem(' . $id_empresa . ', ' . $cod_sucu . ', ' . $ejer_cod . ', ' . $prdo_cod . ', \'' . $comprobante . '\');" style="color:blue;">' . $comprobante . '&nbsp;</a> </td>';


				$tabla_reporte .= '<td style="font-size: 10px; text-align:left; width: 15%;">' . $fact . '</td>';

				$tabla_reporte .= '<td style="font-size: 10px; text-align:center; width: 7%;">' . formato_fecha_jire($fec_vence) . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:left; width: 25%;">' . $detalle . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:right; width: 10%;">' . number_format($debito, 2, '.', ',') . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:right; width: 10%;">' . number_format($credito, 2, '.', ',') . '</td>';
				$tabla_reporte .= '<td style="font-size: 10px; text-align:right; width: 10%;">' . number_format($saldo, 2, '.', ',') . '</td>';
				$tabla_reporte .= '</tr>';
				$x++;
				$total_nb += $debito;
				$total_cre += $credito;
				$sub_tot_deb += $debito;
				$sub_tot_cre += $credito;
			} while ($oIfx->SiguienteRegistro());

			// subtotales
			$tabla_reporte .= '<tr  height="20">';
			$tabla_reporte .= '<td colspan="5"></td>';
			$tabla_reporte .= '<td style="font-size: 12px; text-align:right;" ><b>TOTAL:</b></td>';
			$tabla_reporte .= '<td style="font-size: 10px; text-align:right;">' . number_format($sub_tot_deb, 2, '.', ',') . '</td>
								   <td style="font-size: 10px; text-align:right;" >' . number_format($sub_tot_cre, 2, '.', ',') . '</td>
								   <td style="font-size: 10px; text-align:right;" >' . number_format($saldo, 2, '.', ',') . '</td>
								   </tr>';
			$tabla_reporte .= '<tr><td colspan="9" style="font-size: 11px; border-bottom: 1px solid;  text-align:center;"></td></tr>';
			// totales
			$tabla_reporte .= '<tr  height="20">';
			$tabla_reporte .= '<td colspan="5"></td>';
			$tabla_reporte .= '<td style="font-size: 12px; text-align:right;color:red"><b>TOTALES:</b></td>';
			$tabla_reporte .= '<td style="font-size: 12px; text-align:right;"><b>' . number_format($total_nb, 2, '.', ',') . '</b></td>
								   <td style="font-size: 12px; text-align:right;"><b>' . number_format($total_cre, 2, '.', ',') . '</b></td>
								   <td style="font-size: 12px; text-align:right;"><b>' . number_format($total_nb - $total_cre + $sub_sald, 2, '.', ',') . '</b></td>
								   </tr>';
		}
	}
	$oIfx->Free();
	$tabla_reporte .= '</tbody>';
	$tabla_reporte .= '</table>';

	$sql = "select trim(empr_nom_empr) as empr_nom_empr, 
			trim(empr_dir_empr) as empr_dir_empr,
			trim(empr_ruc_empr) as empr_ruc_empr
	from saeempr
	where empr_cod_empr = $id_empresa";
	$nombreEmpresa = consulta_string($sql, 'empr_nom_empr', $oIfx, '');
	$direccionEmpresa = consulta_string($sql, 'empr_dir_empr', $oIfx, '');
	$rucEmpresa = consulta_string($sql, 'empr_ruc_empr', $oIfx, '');



	//Armado Cabecera Excel
	unset($_SESSION['sHtml_cab']);
	unset($_SESSION['sHtml_det']);
	$sHtml_exe_p .= '<table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
						<tr>
								<th style="font-size: 12px;" align="center" colspan = "11">' . $nombreEmpresa . '</th>
						</tr>
						<tr>
								<th style="font-size: 10px;" align="center" colspan = "11">RUC: ' . $rucEmpresa . '</th>
						</tr>	
						<tr>
								<th style="font-size: 10px;" align="center" colspan = "11">DIR: ' . $direccionEmpresa . '</th>
						</tr>							
						<tr>
								<th style="font-size: 12px;" colspan = "11" align="center"><br>ESTADO DE CUENTA PROVEEDOR</th>
						</tr>					   
						<tr>
								<th style="font-size: 10px;" align="center" colspan="11">Fecha Reporte: ' . date("d/m/Y") . '</th>
						</tr>                            
					</table>';
	$table = '';
	//arma pdf
	$table .= '<page footer="date;heure;page" style="font-size: 9px width: 95%">';
	$table .= $sHtml_exe_p . $tabla_reporte;
	$table .= '</page>';


	$_SESSION['sHtml_cab'] = $sHtml_exe_p;
	$_SESSION['sHtml_det'] = $tabla_reporte;
	$_SESSION['pdf'] = $table;
	$oReturn->assign("DivReporte", "innerHTML", $tabla_reporte);
	return $oReturn;
}

function Mes($mes)
{

	switch ($mes) {
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

function fecha_informix($fecha)
{
	$m = substr($fecha, 5, 2);
	$y = substr($fecha, 0, 4);
	$d = substr($fecha, 8, 2);

	return ($m . '/' . $d . '/' . $y);
}

function fecha_mysql($fecha)
{
	$fecha_array = explode('/', $fecha);
	$m = $fecha_array[0];
	$y = $fecha_array[2];
	$d = $fecha_array[1];

	return ($d . '/' . $m . '/' . $y);
}

function fecha_d_m_y($fecha)
{
	$fecha_array = explode('/', $fecha);
	$y = $fecha_array[0];
	$m = $fecha_array[1];
	$d = $fecha_array[2];

	return ($d . '/' . $m . '/' . $y);
}

function restaFechas($dFecIni, $dFecFin)
{
	$dFecIni = str_replace("-", "", $dFecIni);
	$dFecIni = str_replace("/", "", $dFecIni);
	$dFecFin = str_replace("-", "", $dFecFin);
	$dFecFin = str_replace("/", "", $dFecFin);

	ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);

	ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

	$date1 = mktime(0, 0, 0, $aFecIni[2], $aFecIni[1], $aFecIni[3]);
	$date2 = mktime(0, 0, 0, $aFecFin[2], $aFecFin[1], $aFecFin[3]);

	return round(($date2 - $date1) / (60 * 60 * 24));
}

function nombre_sucursal($cod)
{
	global $DSN_Ifx, $DSN;

	session_start();
	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$sql = "SELECT sucu_nom_sucu FROM saesucu WHERE sucu_cod_sucu='$cod'";
	if ($oIfx->Query($sql)) {
		$nombre = $oIfx->f('sucu_nom_sucu');
	}

	return $nombre;
}

function nombre_transaccion($cod, $sucu, $modu)
{
	global $DSN_Ifx, $DSN;

	session_start();
	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$sql = "select tran_des_tran  from saetran where tran_cod_tran='$cod' and tran_cod_sucu='$sucu' and tran_cod_modu='$modu'";
	//echo $sql;exit;
	if ($oIfx->Query($sql)) {
		$nombre = $oIfx->f('tran_des_tran');
	}
	//echo $nombre;exit;
	return $nombre;
}




function verDiarioContable($aForm = '', $empr = 0, $sucu = 0, $ejer = 0, $mes = 0, $asto = '', $clpv_cod = '')
{

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	global $DSN_Ifx, $DSN;

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

	try {

		//LECTURA SUCIA1
		//////////////


		//sucursal
		$sql = "select sucu_nom_sucu from saesucu where sucu_cod_sucu = $sucu";
		$sucu_nom_sucu = consulta_string_func($sql, 'sucu_nom_sucu', $oIfx, '');


		$oReturn->assign("divTituloAsto", "innerHTML", $asto . ' - ' . $sucu_nom_sucu);

		if (count($arrayAsto) > 0) {

			$table .= '<table class="table table-striped table-condensed" align="center" width="98%">';
			$table .= '<tr>';
			$table .= '<td colspan="4" class="bg-primary">DIARIO CONTABLE</td>';
			$table .= '</tr>';

			foreach ($arrayAsto as $val) {
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
				$sql = "select tidu_des_tidu from saetidu where tidu_cod_tidu = '$asto_cod_tidu'";
				$tidu_des_tidu = consulta_string_func($sql, 'tidu_des_tidu', $oIfx, '');

				$table .= '<tr>';
				$table .= '<td>Diario:</td>';
				$table .= '<td>' . $asto_cod_asto . '</td>';
				$table .= '<td>Fecha:</td>';
				$table .= '<td>' . $asto_fec_asto . '</td>';
				$table .= '</tr>';

				$table .= '<tr>';
				$table .= '<td>Beneficiario:</td>';
				$table .= '<td colspan="3">' . $asto_ben_asto . '</td>';
				$table .= '</tr>';

				$table .= '<tr>';
				$table .= '<td>Modulo:</td>';
				$table .= '<td>' . $modu_des_modu . '</td>';
				$table .= '<td>Documento:</td>';
				$table .= '<td>' . $asto_cod_tidu . ' - ' . $tidu_des_tidu . '</td>';
				$table .= '</tr>';

				$table .= '<tr>';
				$table .= '<td>Detalle:</td>';
				$table .= '<td colspan="3">' . $asto_det_asto . '</td>';
				$table .= '</tr>';
				//sucursal, cod_prove, asto_cod, ejer_cod, prdo_cod
				$table .= '<tr>';
				$table .= '<td>Formato:</td>';
				$table .= '<td align="left">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario(' . $sucu . ', 0, \'' . $asto . '\', ' . $ejer . ', ' . $mes . ');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
				$table .= '<td>Valor:</td>';
				$table .= '<td class="bg-danger fecha_letra" align="left">' . number_format($asto_vat_asto, 2, '.', ',') . '</td>';
				$table .= '</tr>';
			} //fin foreach

			$table .= '</table>';

			$oReturn->assign("divInfo", "innerHTML", $table);
		}

		//directorio
		if (count($arrayDiario) > 0) {

			$tableDia .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableDia .= '<tr>';
			$tableDia .= '<td colspan="5" class="bg-primary">DIARIO</td>
						<td align="center">
							<div class="btn btn-primary btn-sm" onclick="vista_previa_diario(' . $sucu . ', 0, \'' . $asto . '\', ' . $ejer . ', ' . $mes . ');">
								<span class="glyphicon glyphicon-print"></span>
							</div>
						</td>';
			$tableDia .= '</tr>';
			$tableDia .= '<tr>';
			$tableDia .= '<td>Cuenta Contable</td>';
			$tableDia .= '<td>Centro Costos</td>';
			$tableDia .= '<td>Centro Actividad</td>';
			$tableDia .= '<td>Documento</td>';
			$tableDia .= '<td>Debito</td>';
			$tableDia .= '<td>Credito</td>';
			$tableDia .= '</tr>';
			$totalDeb = 0;
			$totalCre = 0;
			foreach ($arrayDiario as $val) {
				$dasi_cod_cuen = $val[0];
				$dasi_cod_cact = $val[1];
				$ccos_cod_ccos = $val[2];
				$dasi_dml_dasi = $val[3];
				$dasi_cml_dasi = $val[4];
				$dasi_det_asi = $val[5];
				$dasi_num_depo = $val[6];

				//clpv
				$cuen_nom_cuen = '';
				if (!empty($dasi_cod_cuen)) {
					$sql = "select cuen_nom_cuen from saecuen where cuen_cod_cuen = '$dasi_cod_cuen' and cuen_cod_empr = $empr";
					$cuen_nom_cuen = consulta_string_func($sql, 'cuen_nom_cuen', $oIfx, '');
				}

				$ccosn_nom_ccosn = '';
				if (!empty($ccos_cod_ccos)) {
					$sql = "select ccosn_nom_ccosn from saeccosn where ccosn_cod_ccosn = '$ccos_cod_ccos' and ccosn_cod_empr = $empr";
					$ccosn_nom_ccosn = consulta_string_func($sql, 'ccosn_nom_ccosn', $oIfx, '');
				}

				$cact_nom_cact = '';
				if (!empty($dasi_cod_cact)) {
					$sql = "select cact_nom_cact from saecact where cact_cod_cact = '$dasi_cod_cact' and cact_cod_empr = $empr";
					$cact_nom_cact = consulta_string_func($sql, 'cact_nom_cact', $oIfx, '');
				}

				$tableDia .= '<tr>';
				$tableDia .= '<td>' . $dasi_cod_cuen . ' - ' . $cuen_nom_cuen . '</td>';
				$tableDia .= '<td>' . $ccos_cod_ccos . ' - ' . $ccosn_nom_ccosn . '</td>';
				$tableDia .= '<td>' . $dasi_cod_cact . ' - ' . $cact_nom_cact . '</td>';
				$tableDia .= '<td>' . $dasi_num_depo . '</td>';
				$tableDia .= '<td align="right">' . number_format($dasi_dml_dasi, 2, '.', ',') . '</td>';
				$tableDia .= '<td align="right">' . number_format($dasi_cml_dasi, 2, '.', ',') . '</td>';
				$tableDia .= '</tr>';

				$totalDeb += $dasi_dml_dasi;
				$totalCre += $dasi_cml_dasi;
			} //fin foreach
			$tableDia .= '<tr>';
			$tableDia .= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
			$tableDia .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalDeb, 2, '.', ',') . '</td>';
			$tableDia .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalCre, 2, '.', ',') . '</td>';
			$tableDia .= '</tr>';
			$tableDia .= '</table>';

			$oReturn->assign("divDiario", "innerHTML", $tableDia);
		}

		//directorio
		if (count($arrayDirectorio) > 0) {

			$tableDir .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableDir .= '<tr>';
			$tableDir .= '<td colspan="6" class="bg-primary">DIRECTORIO</td>';
			$tableDir .= '</tr>';
			$tableDir .= '<tr>';
			$tableDir .= '<td>No.</td>';
			$tableDir .= '<td>Cliente/Proveedor</td>';
			$tableDir .= '<td>Transaccion</td>';
			$tableDir .= '<td>Factura</td>';
			$tableDir .= '<td>Credito</td>';
			$tableDir .= '<td>Debito</td>';
			$tableDir .= '</tr>';
			$totalDeb = 0;
			$totalCre = 0;
			foreach ($arrayDirectorio as $val) {
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
				if (!empty($dir_cod_cli)) {
					$sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $dir_cod_cli";
					$clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
				}

				$tableDir .= '<tr>';
				$tableDir .= '<td>' . $dir_cod_dir . '</td>';
				$tableDir .= '<td>' . $clpv_nom_clpv . '</td>';
				$tableDir .= '<td>' . $dir_cod_tran . '</td>';
				$tableDir .= '<td>' . $dir_num_fact . '</td>';
				$tableDir .= '<td align="right">' . number_format($dir_cre_ml, 2, '.', ',') . '</td>';
				$tableDir .= '<td align="right">' . number_format($dir_deb_ml, 2, '.', ',') . '</td>';
				$tableDir .= '</tr>';

				$totalCre += $dir_cre_ml;
				$totalDeb += $dir_deb_ml;
			} //fin foreach
			$tableDir .= '<tr>';
			$tableDir .= '<td align="right" class="bg-danger fecha_letra" colspan="4">TOTAL:</td>';
			$tableDir .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalCre, 2, '.', ',') . '</td>';
			$tableDir .= '<td align="right" class="bg-danger fecha_letra">' . number_format($totalDeb, 2, '.', ',') . '</td>';
			$tableDir .= '</tr>';
			$tableDir .= '</table>';

			$oReturn->assign("divDirectorio", "innerHTML", $tableDir);
		}

		//retencion
		if (count($arrayRetencion) > 0) {

			$tableRet .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableRet .= '<tr>';
			$tableRet .= '<td colspan="8" class="bg-primary">RETENCION</td>';
			$tableRet .= '</tr>';
			$tableRet .= '<tr>';
			$tableRet .= '<td>Cliente/Proveedor</td>';
			$tableRet .= '<td>Factura</td>';
			$tableRet .= '<td>Retencion</td>';
			$tableRet .= '<td>Codigo</td>';
			$tableRet .= '<td>Porcentaje</td>';
			$tableRet .= '<td>Base Imp.</td>';
			$tableRet .= '<td>Valor</td>';
			$tableRet .= '<td>Print</td>';
			$tableRet .= '</tr>';
			foreach ($arrayRetencion as $val) {
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
				if (!empty($ret_cod_clpv)) {
					$sql = "select clpv_nom_clpv from saeclpv where clpv_cod_clpv = $ret_cod_clpv";
					$clpv_nom_clpv = consulta_string_func($sql, 'clpv_nom_clpv', $oIfx, '');
				}

				//fprv
				$printRet = '';
				//if($asto_cod_modu == 4 || $asto_cod_modu == 6){

				//fecha fprv o minv
				$tipo_rd = '';
				if ($asto_cod_modu == 4) {
					$sql = "select fprv_fec_emis 
								from saefprv
								where fprv_cod_clpv = $ret_cod_clpv and
								fprv_num_fact = '$ret_num_fact' and
								fprv_cod_asto = '$asto' and
								fprv_cod_ejer = $ejer and
								fprv_cod_empr = $empr and
								fprv_cod_sucu = $sucu";
					$fechaEmis = consulta_string_func($sql, 'fprv_fec_emis', $oIfx, '');
					$tipo_rd = 5;
				} elseif ($asto_cod_modu == 10) {
					$sql = "select minv_fmov 
								from saeminv
								where minv_cod_clpv = $ret_cod_clpv and
								minv_fac_prov = '$ret_num_fact' and
								minv_comp_cont = '$asto' and
								minv_cod_ejer = $ejer and
								minv_cod_empr = $empr and
								minv_cod_sucu = $sucu";
					$fechaEmis = consulta_string_func($sql, 'minv_fmov', $oIfx, '');
					$tipo_rd = 6;
				}

				$printRet = '<div class="btn btn-primary btn-sm" onclick="genera_documento(' . $tipo_rd . ', \'' . $campo . '\',\'' . $fprv_clav_sri . '\' ,
																				 \'' . $ret_cod_clpv . '\'  , \'' . $ret_num_fact . '\', \'' . $ejer . '\',
																				 \'' . $asto . '\',  \'' . $fechaEmis . '\', ' . $sucu . ');">
									<span class="glyphicon glyphicon-print"></span>
								</div>';
				//}				


				$tableRet .= '<tr>';
				$tableRet .= '<td>' . $clpv_nom_clpv . '</td>';
				$tableRet .= '<td>' . $ret_num_fact . '</td>';
				$tableRet .= '<td>' . $ret_ser_ret . ' - ' . $ret_num_ret . '</td>';
				$tableRet .= '<td>' . $ret_cta_ret . '</td>';
				$tableRet .= '<td align="right">' . $ret_porc_ret . '</td>';
				$tableRet .= '<td align="right">' . number_format($ret_bas_imp, 2, '.', ',') . '</td>';
				$tableRet .= '<td align="right">' . number_format($ret_valor, 2, '.', ',') . '</td>';
				$tableRet .= '<td align="center">' . $printRet . '</td>';
				$tableRet .= '</tr>';
			} //fin foreach

			$tableRet .= '</table>';

			$oReturn->assign("divRetencion", "innerHTML", $tableRet);
		}

		//adjuntos
		if (count($arrayAdjuntos) > 0) {

			$tableAdj .= '<table class="table table-striped table-condensed table-bordered table-hover" align="center" width="98%">';
			$tableAdj .= '<tr>';
			$tableAdj .= '<td colspan="2" class="bg-primary">ARCHIVOS ADJUNTOS</td>';
			$tableAdj .= '</tr>';
			$tableAdj .= '<tr>';
			$tableAdj .= '<td>Titulo</td>';
			$tableAdj .= '<td>Ruta</td>';
			$tableAdj .= '</tr>';
			foreach ($arrayAdjuntos as $val) {
				$titulo = $val[0];
				$ruta = $val[1];

				$archivo_factura = "../../Include/Clases/Formulario/Plugins/reloj/$ruta";

				$tableAdj .= '<tr>';
				$tableAdj .= '<td>' . $titulo . '</td>';
				//$tableAdj .= '<td><a href="#" onclick="dowloand(\'' . $ruta . '\')">' . $ruta . '</a></td>';
				$tableAdj .= '<td><a href="' . $archivo_factura . '" target="_blank" >' . $ruta . '</a></td>';
				$tableAdj .= '</tr>';
			} //fin foreach

			$tableAdj .= '</table>';

			$oReturn->assign("divAdjuntos", "innerHTML", $tableAdj);
		}
	} catch (Exception $e) {
		$oReturn->alert($e->getMessage());
	}

	return $oReturn;
}


function genera_pdf_doc($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod)
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	global $DSN_Ifx;

	$oIfxA = new Dbo();
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();
	unset($_SESSION['pdf']);
	$oReturn = new xajaxResponse();

	$tipo     = $aForm['documento'];
	//SELECCION DEL TIPO DE FORMATO


	$sql = "select asto_cod_modu, asto_tipo_mov from saeasto where asto_cod_asto='$asto_cod'";
	$tipomov = consulta_string($sql, 'asto_tipo_mov', $oIfx, '');
	$codmodu = consulta_string($sql, 'asto_cod_modu', $oIfx, '');

	if ($tipomov == 'DI') {
		$sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
		$ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
		if (empty($ubi)) {
			$ubi = 'Include/Formatos/comercial/diario.php';
		}
		include_once('../../' . $ubi . '');
		$diario = formato_diario($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
	} elseif ($tipomov == 'EG') {
		$sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
		$ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
		if (empty($ubi)) {
			$ubi = 'Include/Formatos/comercial/egreso.php';
		}
		include_once('../../' . $ubi . '');
		$diario = formato_egreso($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
	} elseif ($tipomov == 'IN') {
		$sql = "select ftrn_ubi_web from saeftrn where ftrn_tip_movi='$tipomov' and ftrn_cod_modu=$codmodu and ftrn_ubi_web is not null";
		$ubi = consulta_string($sql, 'ftrn_ubi_web', $oIfx, '');
		if (empty($ubi)) {
			$ubi = 'Include/Formatos/comercial/ingreso.php';
		}
		include_once('../../' . $ubi . '');
		$diario = formato_ingreso($idempresa, $idsucursal, $asto_cod, $ejer_cod, $prdo_cod);
	}

	$_SESSION['pdf'] = $diario;

	$oReturn->script('generar_pdf2()');
	return $oReturn;
}


function genera_documento($tipo_documento = 0, $id = '', $clavAcce = 'no_autorizado', $clpv = 0,  $num_fact = '',  $ejer = 0,  $asto = '',  $fec_emis = '', $sucu = 0)
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	global $DSN_Ifx;

	$oReturn = new xajaxResponse();
	switch ($tipo_documento) {
		case 1:
			$_SESSION['pdf'] = reporte_factura($id, $clavAcce, $sucu);
			break;
		case 2:
			$_SESSION['pdf'] = reporte_notaDebito($id, $clavAcce);
			break;
		case 3:
			$_SESSION['pdf'] = reporte_notaCredito($id, $clavAcce, $sucu);
			break;
		case 4:
			$_SESSION['pdf'] = reporte_guiaRemision($id, $clavAcce, $sucu);
			break;
		case 5:
			$id = $_SESSION['sqlId'][$id];
			$_SESSION['pdf'] = reporte_retencionGasto($id, $clavAcce, $rutapdf, $clpv,  $num_fact,  $ejer,  $asto,  $fec_emis, $sucu);
			break;
		case 6:
			$id = $_SESSION['sqlId'][$id];
			$_SESSION['pdf'] = reporte_retencionInve($id, $clavAcce,  $rutapdf, $clpv,  $num_fact,  $ejer,  $asto,  $fec_emis, $sucu);
			break;
		case 7:
			$_SESSION['pdf'] = reporte_factura_export($id, $clavAcce);
			break;
		case 8:
			$_SESSION['pdf'] = reporte_factura_flor($id, $clavAcce);
			break;
		case 9:
			$_SESSION['pdf'] = reporte_factura_flor_export($id, $clavAcce);
			break;
		case 10:
			$_SESSION['pdf'] = reporte_guiaRemisionFlor($id, $clavAcce);
			break;
		case 12:
			$_SESSION['pdf'] = reporte_liqu_compras($id, $clavAcce);
			break;
	}

	$oReturn->script('generar_pdf()');

	return $oReturn;
}



/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
