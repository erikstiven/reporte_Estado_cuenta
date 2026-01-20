--execute procedure sp_estado_cuenta_prove_web(1, 359, '09/01/2019', '09/30/2019', 1, 1, '%', '%', '%', 1, 0, 0);
--execute procedure sp_estado_cuenta_prove_web(1, 1, '09/01/2019', '09/30/2019', 2, 1, '%', '%', '%', 1, 1);
-- drop procedure sp_estado_cuenta_prove_web;
-- NOTA IMPORTANTE: CONFIGURAR EN EL MAESTRO DE TRANSACCIONES DE PROVEEDORES EL CHECK DE ANTICIPOS, 
-- SOLO DEBE ESTR MARCADO LA TRANSACCION ANT Y CAT

create procedure "informix".sp_estado_cuenta_prove_web( in_empr integer,  in_prove integer, in_fecha_ini date, in_fecha_fin date, in_op integer, 
														in_sucu varchar(5), in_grupo varchar(20), in_zona varchar(5), in_num_fact varchar(20), 
														in_ord integer, in_anti integer, in_solo_anti integer )

returning  date as fecha_emision,  varchar(100) as tran_cod_tran,  varchar(255) as comprobante,  varchar(100) as factura,  date as fecha_vencimiento,
	varchar(255) as detalle, decimal(18,3) as debito, decimal(18,3) as credito,  varchar(255) as prove, varchar(100) as ruc, 
	integer as clpv_cod_clpv, integer as sucu_cod, integer as modu_cod, integer as ejer_cod, integer as prdo_cod ;

define fec_emis date;
define cod_tran varchar(100);
define comprob, ls_filtro varchar(255);
define num_fac 	varchar(100);
define fec_vence date;
define detalle varchar(255);
define deb decimal(18,3);
define cre decimal(18,3);
define proveedor, ls_filtro_tran varchar(255);
define ruc varchar(100);
define cod_clpv integer;
define msn, ls_tran varchar(10);

define sucu_cod integer;
define modu_cod integer;
define ejer_cod integer;
define prdo_cod integer;

-- PROVEEDOR  SELECCCIONADO
if( in_op==1 ) then
	if in_ord = 1 then -- POR FACTURA
		if in_solo_anti = 1 then
			foreach 		
				SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
						saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
						saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
						saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
						saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
				INTO    fec_emis,  	            cod_tran,  		        comprob,  
						num_fac,   	            fec_vence, 	            detalle,  
						deb,   	                cre,		            proveedor,
						ruc, 		            cod_clpv,               sucu_cod,
						modu_cod, 				ejer_cod,				prdo_cod
						
				FROM saeclpv, saedmcp   
				WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
				AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
				AND	saedmcp.dmcp_cod_empr = in_empr   
				AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
				AND  saeclpv.clpv_cod_clpv  = in_prove
				AND	saeclpv.clpv_clopv_clpv = 'PV' 
				AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
				AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
				AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
				AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
				AND saedmcp.dmcp_cod_tran in (select  tran_cod_tran	
													from saetran
													where  tran_cod_modu  = 4
													and tran_ant_tran = 1
													and tran_cod_empr = in_empr
													and tran_cod_sucu ||''like in_sucu)	
				ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
				RETURN fec_emis, cod_tran,  comprob,  
				   num_fac,  fec_vence, detalle,  
				   deb,   	 cre,		proveedor,
				   ruc , 	 cod_clpv,  sucu_cod, 
				   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;				   
			end foreach;
		else		
			if in_anti == 1 then -- SIN ANTICIPOS
				foreach 		
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
					AND  saeclpv.clpv_cod_clpv  = in_prove
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					AND saedmcp.dmcp_cod_tran not in (select  tran_cod_tran	
														from saetran
														where  tran_cod_modu  = 4
														and tran_ant_tran = 1
														and tran_cod_empr = in_empr
														and tran_cod_sucu ||''like in_sucu)	
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
					   num_fac,  fec_vence, detalle,  
					   deb,   	 cre,		proveedor,
					   ruc , 	 cod_clpv,  sucu_cod, 
					   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;				   
				end foreach;
			else
				foreach 		
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
					AND  saeclpv.clpv_cod_clpv  = in_prove
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 				
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
					   num_fac,  fec_vence, detalle,  
					   deb,   	 cre,		proveedor,
					   ruc , 	 cod_clpv,  sucu_cod, 
					   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end foreach;
			end if	
		end if	
	else -- ORDERNA POR FECHA
		if in_solo_anti = 1 then
			foreach
				SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
						saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
						saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
						saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
						saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
				INTO    fec_emis,  	            cod_tran,  		        comprob,  
						num_fac,   	            fec_vence, 	            detalle,  
						deb,   	                cre,		            proveedor,
						ruc, 		            cod_clpv,               sucu_cod,
						modu_cod, 				ejer_cod,				prdo_cod
						
				FROM saeclpv, saedmcp   
				WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
				AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
				AND	saedmcp.dmcp_cod_empr = in_empr   
				AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
				AND  saeclpv.clpv_cod_clpv  = in_prove
				AND	saeclpv.clpv_clopv_clpv = 'PV' 
				AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
				AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
				AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
				AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
				AND saedmcp.dmcp_cod_tran  in (select  tran_cod_tran	
													from saetran
													where  tran_cod_modu  = 4
													and tran_ant_tran = 1
													and tran_cod_empr = in_empr
													and tran_cod_sucu ||''like in_sucu)
				ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
				RETURN fec_emis, cod_tran,  comprob,  
				   num_fac,  fec_vence, detalle,  
				   deb,   	 cre,		proveedor,
				   ruc , 	 cod_clpv,  sucu_cod, 
				   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
			end foreach;
		else 	
			if in_anti = 1 then -- SIN ANTICIPOS	
				foreach
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
					AND  saeclpv.clpv_cod_clpv  = in_prove
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					AND saedmcp.dmcp_cod_tran not in (select  tran_cod_tran	
														from saetran
														where  tran_cod_modu  = 4
														and tran_ant_tran = 1
														and tran_cod_empr = in_empr
														and tran_cod_sucu ||''like in_sucu)
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
					   num_fac,  fec_vence, detalle,  
					   deb,   	 cre,		proveedor,
					   ruc , 	 cod_clpv,  sucu_cod, 
					   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end foreach;
			else 
				foreach
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin 
					AND  saeclpv.clpv_cod_clpv  = in_prove
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 				
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
					   num_fac,  fec_vence, detalle,  
					   deb,   	 cre,		proveedor,
					   ruc , 	 cod_clpv,  sucu_cod, 
					   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end foreach;
			end if			
		end if
	end if	
end if;


-- TODOS LOS PROVEEDORES  
if( in_op==2 ) then
	if in_ord = 1 then -- POR FACTURA
		if in_solo_anti = 1 then
			FOREACH 
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					AND saedmcp.dmcp_cod_tran in (select  tran_cod_tran	
														from saetran
														where  tran_cod_modu  = 4
														and tran_ant_tran = 1
														and tran_cod_empr = in_empr
														and tran_cod_sucu ||''like in_sucu)
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
						   num_fac,  fec_vence, detalle,  
						   deb,   	 cre,		proveedor,
						   ruc , 	 cod_clpv,  sucu_cod, 
						   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
			end foreach;
		else	
			if in_anti = 1 then -- SIN ANTICIPOS			
				FOREACH 
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					AND saedmcp.dmcp_cod_tran not in (select  tran_cod_tran	
														from saetran
														where  tran_cod_modu  = 4
														and tran_ant_tran = 1
														and tran_cod_empr = in_empr
														and tran_cod_sucu ||''like in_sucu)
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
						   num_fac,  fec_vence, detalle,  
						   deb,   	 cre,		proveedor,
						   ruc , 	 cod_clpv,  sucu_cod, 
						   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end foreach;
				--raise exception -746,0,'sql: sin anticipos'||ls_filtro_tran;	
			else --	TODOS LOS MOVIMINETOS INCLUIDO LOS ANTICIPOS
				FOREACH 
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dmcp_num_fac, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
						   num_fac,  fec_vence, detalle,  
						   deb,   	 cre,		proveedor,
						   ruc , 	 cod_clpv,  sucu_cod, 
						   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end foreach;	
			end if
		end if		
	else -- POR FECHA
		if in_solo_anti = 1 then
			FOREACH
				SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
						saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
						saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
						saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
						saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
				INTO    fec_emis,  	            cod_tran,  		        comprob,  
						num_fac,   	            fec_vence, 	            detalle,  
						deb,   	                cre,		            proveedor,
						ruc, 		            cod_clpv,               sucu_cod,
						modu_cod, 				ejer_cod,				prdo_cod
						
				FROM saeclpv, saedmcp   
				WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
				AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
				AND	saedmcp.dmcp_cod_empr = in_empr   
				AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
				AND	saeclpv.clpv_clopv_clpv = 'PV' 
				AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
				AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
				AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
				AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
				AND saedmcp.dmcp_cod_tran in (select  tran_cod_tran	
													from saetran
													where  tran_cod_modu  = 4
													and tran_ant_tran = 1
													and tran_cod_empr = in_empr
													and tran_cod_sucu ||''like in_sucu)
				ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
				RETURN fec_emis, cod_tran,  comprob,  
					   num_fac,  fec_vence, detalle,  
					   deb,   	 cre,		proveedor,
					   ruc , 	 cod_clpv,  sucu_cod, 
					   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
			end FOREACH;
		else
			if in_anti = 1 then -- SIN ANTICIPOS			
				FOREACH
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 
					AND saedmcp.dmcp_cod_tran not in (select  tran_cod_tran	
														from saetran
														where  tran_cod_modu  = 4
														and tran_ant_tran = 1
														and tran_cod_empr = in_empr
														and tran_cod_sucu ||''like in_sucu)
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
						   num_fac,  fec_vence, detalle,  
						   deb,   	 cre,		proveedor,
						   ruc , 	 cod_clpv,  sucu_cod, 
						   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end FOREACH;
			else --TODOS LOS MOVIMINETOS INCLUIDO LOS ANTICIPOS
				FOREACH
					SELECT	saedmcp.dcmp_fec_emis,	saedmcp.dmcp_cod_tran,	saedmcp.dcmp_num_comp,   
							saedmcp.dmcp_num_fac,   saedmcp.dmcp_fec_ven,   saedmcp.dmcp_det_dcmp,   
							saedmcp.dcmp_deb_ml,    saedmcp.dcmp_cre_ml,    saeclpv.clpv_nom_clpv,    
							saeclpv.clpv_ruc_clpv,  saeclpv.clpv_cod_clpv,  saedmcp.dmcp_cod_sucu,
							saedmcp.dmcp_cod_modu,  dmcp_cod_ejer,			 month(dcmp_fec_emis) as prdo_cod
					INTO    fec_emis,  	            cod_tran,  		        comprob,  
							num_fac,   	            fec_vence, 	            detalle,  
							deb,   	                cre,		            proveedor,
							ruc, 		            cod_clpv,               sucu_cod,
							modu_cod, 				ejer_cod,				prdo_cod
							
					FROM saeclpv, saedmcp   
					WHERE saedmcp.clpv_cod_clpv = saeclpv.clpv_cod_clpv   
					AND saeclpv.clpv_cod_empr = saedmcp.dmcp_cod_empr     
					AND	saedmcp.dmcp_cod_empr = in_empr   
					AND	saedmcp.dcmp_fec_emis BETWEEN  in_fecha_ini  AND  in_fecha_fin    
					AND	saeclpv.clpv_clopv_clpv = 'PV' 
					AND saeclpv.clpv_cod_sucu   ||'' like in_sucu  
					AND	saeclpv.grpv_cod_grpv	||'' like in_grupo  
					AND	saeclpv.clpv_cod_zona	||'' like in_zona  		
					AND	saedmcp.dmcp_num_fac	||'' like in_num_fact 				
					ORDER BY saeclpv.clpv_nom_clpv, saedmcp.dcmp_fec_emis, saedmcp.dcmp_deb_ml DESC
					RETURN fec_emis, cod_tran,  comprob,  
						   num_fac,  fec_vence, detalle,  
						   deb,   	 cre,		proveedor,
						   ruc , 	 cod_clpv,  sucu_cod, 
						   modu_cod, ejer_cod,  prdo_cod  WITH RESUME;
				end FOREACH;		
			end if	
		end if	
	end if
end if;
end procedure                                                                            
