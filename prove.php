<?

/********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? include_once('../_Modulo.inc.php'); ?>
<? include_once(HEADER_MODULO); ?>
<? if ($ejecuta) { ?>
    <? /********************************************************************/ ?>
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" type="text/css"
        href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.buttons.min.css"
        media="screen">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css"
        href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" type="text/css"
        href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skinsfolder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/dataTables/dataTables.bootstrap.min.css"
        media="screen">
    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--JavaScript-->
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.flash.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.jszip.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.pdfmake.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.vfs_fonts.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.html5.min.js"></script>
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_INCLUDE"] ?>js/dataTables/dataTables.buttons.print.min.js"></script>
    <!-- AdminLTE App -->
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>dist/js/adminlte.min.js"></script>
    <!-- Echarts -->
    <script type="text/javascript" language="JavaScript"
        src="<?= $_COOKIE["JIREH_COMPONENTES"] ?>bower_components/echarts/js/echarts.min.js"></script>

    <script src="js/lenguajeusuario_.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style type="text/css">
        /*PARA CREACION DE PESTA?AS*/
        .contenedor {
            width: 96%;
            margin: auto;
            background-color: #EBEBEB;
            color: bisque;
            padding: 10px 15px 10px 25px;
            border-radius: 10px;
            box-shadow: 0 10px 10px 0px rgba(0, 0, 0, 0.8);
        }

        .contenedorConsulta {
            width: 300px;
            margin: auto;
            background-color: #EBEBEB;
            color: bisque;
            padding: 5px 15px 25px 25px;
            border-radius: 10px;
            /*box-shadow: 0 10px 10px 0px rgba(0, 0, 0, 0.8);*/
        }

        #pestanas {
            background-color: #EBEBEB;
            float: top;
            font-size: 3ex;
            font-weight: bold;
        }

        #pestanas ul {
            margin-left: -40px;
        }

        #pestanas li {
            list-style-type: none;
            float: left;
            text-align: center;
            margin: 0px 2px -2px -0px;
            background: #A6C4E1;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            border: 2px #808080;
            border-bottom: dimgray;
            padding: 0px 20px 0px 20px;
        }

        #pestanas a:link {
            text-decoration: none;
            color: white;
        }

        #contenidopestanas {
            clear: both;
            background: #D3D3D3;
            padding: 10px 0px 10px 10px;
            border-radius: 5px;
            border-top-left-radius: 0px;
            border: 2px #808080;
        }

        .pull-left {
            float: left !important;
        }

        /*FIN DE CREACION DE PESTA?AS*/
        .copiar {
            font-size: 20px;
            color: white;
            margin: 7px;
        }

        .contenedor_copiar {
            border-radius: 50%;
            background-color: #337ab7 !important;
            text-align: center;
        }

        .pdf {
            font-size: 20px;
            color: white;
            margin: 7px;
        }

        .contenedor_pdf {
            border-radius: 50%;
            background-color: #dc2f2f !important;
            text-align: center;
        }

        .excel {
            font-size: 20px;
            color: white;
            margin: 7px;
        }

        .contenedor_excel {
            border-radius: 50%;
            background-color: #3ca23c !important;
            text-align: center;
        }

        .csv {
            font-size: 20px;
            color: white;
            margin: 7px;
        }

        .contenedor_csv {
            border-radius: 50%;
            background-color: #007c7c !important;
            text-align: center;
        }

        .imprimir {
            font-size: 20px;
            color: white;
            margin: 7px;
        }

        .contenedor_imprimir {
            border-radius: 50%;
            background-color: #8766b1 !important;
            text-align: center;
        }
    </style>
    <script>
        /* xajax.callback.global.onResponseDelay = show_load;
xajax.callback.global.onComplete = hide_load;
 */

        function cerrar_ventana() {
            CloseAjaxWin();
        }

        function f_filtro_sucursal(data) {
            xajax_f_filtro_sucursal(xajax.getFormValues("form1"), data);
        }
        // function cargar_sucursal(){
        // xajax_genera_cabecera_formulario('sucursal', xajax.getFormValues("form1"));
        // }        

        function cargar_prove() {
            xajax_genera_cabecera_formulario('proveedor', xajax.getFormValues("form1"));
        }

        function cargar_grupo() {
            xajax_genera_cabecera_formulario('grupo', xajax.getFormValues("form1"));
        }

        function cerrar_ventana() {
            CloseAjaxWin();
        }

        function reporte(op) {
            jsShowWindowLoad();
            xajax_reporte(xajax.getFormValues("form1"), op);
        }

        function seleccionaItem(empr, sucu, ejer, mes, asto, clpv) {
            $("#miModal").modal("show");
            $("#divInfo").html('');
            $("#divDirectorio").html('');
            $("#divRetencion").html('');
            $("#divDiario").html('');
            $("#divAdjuntos").html('');
            xajax_verDiarioContable(xajax.getFormValues("form1"), empr, sucu, ejer, mes, asto, clpv);

        }

        function dowloand(ruta) {
            document.location = "../oportunidades/dowloand.php?ruta=" + ruta;
        }

        function f_filtro_sucursal(data) {
            xajax_f_filtro_sucursal(xajax.getFormValues("form1"), data);
        }

        function eliminar_lista_sucursal() {
            var sel = document.getElementById("sucursal");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadir_elemento_sucursal(x, i, elemento) {
            var lista = document.form1.sucursal;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.sucursal.value = i;
        }

        function cambioFiltroFecha(op) {
            xajax_cambioFiltroFecha(xajax.getFormValues("form1"), op);
        }

        function cargarMes() {
            xajax_cargarMes(xajax.getFormValues("form1"));
        }

        function eliminarCampo() {
            var sel = document.getElementById('mes');
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }

        function anadirElementoCampo(x, i, elemento) {
            var lista = document.form1.mes;
            var option = new Option(elemento, i);
            lista.options[x] = option;
        }

        function buca_proveedor_id() {
            //alert('HOLA....');
            $("#myModalProveedores").modal("show");
            var table = $('#table_proveedor').DataTable();
            table.destroy();
            listar_proveedores();

        }

        function buca_proveedor(event, id) {
            if (event.keyCode == 13 || event.keyCode == 115) { // F4   
                $("#myModalProveedores").modal("show");
                var table = $('#table_proveedor').DataTable();
                table.destroy();
                listar_proveedores();
            }
        }

        function bajar_proveedores(id, nombre) {
            document.getElementById("clpv_cod_clpv").value = id;
            document.getElementById("cliente").value = nombre;
            $("#myModalProveedores").modal("hide");
        }

        function f_pdf() {
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=370, top=255, left=130";
            var pagina = '../../Include/documento_pdf_estados.php?sesionId=<?= session_id() ?>';
            window.open(pagina, "", opciones);

        }

        function cambioRemesa(id) {
            if (id == 'remesa') {
                document.getElementById('anticipos').checked = false;

            } else {
                document.getElementById('remesa').checked = false;
            }
        }

        function vista_previa_diario(sucursal, cod_prove, asto_cod, ejer_cod, prdo_cod) {
            /*var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=750, height=380, top=255, left=130";
            var pagina = '../contabilidad_comprobante/vista_previa.php?sesionId=<?= session_id() ?>&sucursal='+  sucursal+'&cod_prove='+cod_prove+'&asto='+asto_cod+'&ejer='+ejer_cod+'&mes='+prdo_cod;
            window.open(pagina, "", opciones);*/
            var empresa = document.getElementById("empresa").value;
            xajax_genera_pdf_doc(empresa, sucursal, asto_cod, ejer_cod, prdo_cod);
        }

        function generar_pdf2() {
            var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=370, top=255, left=130";
            var pagina = '../../Include/documento_pdf.php?sesionId=<?= session_id() ?>';
            window.open(pagina, "", opciones);
        }

        function genera_documento(tipo_documento, id, clavAcce, clpv, num_fact, ejer, asto, fec_emis, sucu) {
            xajax_genera_documento(tipo_documento, id, clavAcce, clpv, num_fact, ejer, asto, fec_emis, sucu);
        }


        function generar_pdf() {
            if (ProcesarFormulario() == true) {
                var opciones = "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=370, top=255, left=130";
                var pagina = '../../Include/documento_pdf.php?sesionId=<?= session_id() ?>';
                window.open(pagina, "", opciones);
            }
        }

        function initTablaEdit(table, titulo) {
            var search = '<?= $ruc ?>';
            var table = $('#' + table).DataTable({
                scrollY: '100vh',
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: titulo,
                        footer: true,
                        titleAttr: 'Click para descargar como Excel',
                        text: '<div class="contenedor_excel"><i class="fa fa-file-excel-o excel"></i><label class="labe"></label></div>',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9],
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        footer: true,
                        orientation: 'landscape',
                        title: titulo,
                        titleAttr: 'Click para descargar como PDF',
                        pageSize: 'A3',
                        text: '<div class="contenedor_pdf"><i class="fa fa-file-pdf-o pdf"></i><label class="labe"></label></div>',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9],
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    },
                ],
                processing: "<i class='fa fa-spinner fa-spin' style='font-size:30px; color: #34495e;'></i>",
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Buscar",
                    'paginate': {
                        'previous': 'Anterior',
                        'next': 'Siguiente'
                    },
                    "zeroRecords": "No se encontro datos",
                    "info": "Mostrando _START_ a _END_ de  _TOTAL_ Total",
                    "infoEmpty": "",
                    "infoFiltered": "(Mostrando _MAX_ Registros Totales)",
                },
                "ordering": true,
                "info": true,
            });
            //table.buttons().remove();
            table.search(search).draw();
        }

        function nuevoFormulario() {
            $("#form1")[0].reset();
            location.reload();
        }

        function generar_reporte(base64Data) {
            var jsonData1 = atob(base64Data);

            // Convertir la cadena JSON a un objeto JavaScript
            var datos = JSON.parse(jsonData1);

            // Inicializar DataTable
            $('#table_est_cta').DataTable().destroy();
            $('#table_est_cta').DataTable({
                "scrollY": '50vh',
                "scrollX": true,
                "searching": true,
                "pageLength": 100,
                "bDeferRender": true,
                "dom": "Bfrtip",
                "sPaginationType": "full_numbers",
                "data": datos.data, // Usar los datos definidos en la variable
                "columns": [{
                        "data": "num"
                    },
                    {
                        "data": "sucursal"
                    },
                    {
                        "data": "ruc"
                    },
                    //{ "data": "nombre" },
                    {
                        "data": "nombre",
                        "render": function(data, type, row) {
                            if (data.comprobante) {
                                return `<a href="#" onclick="seleccionaItem(${data.id_empresa}, ${data.cod_sucu}, ${data.ejer_cod}, ${data.prdo_cod}, '${data.comprobante}');" style="color:blue;">${data.comprobante}&nbsp;</a>`;
                            } else {
                                return `<a href="#">${data}&nbsp;</a>`;
                            }
                        }
                    },
                    {
                        "data": "telefono"
                    },
                    {
                        "data": "num_depo"
                    },
                    {
                        "data": "vence"
                    },
                    {
                        "data": "direccion"
                    },
                    {
                        "data": "debito"
                    },
                    {
                        "data": "credito"
                    },
                    {
                        "data": "saldo"
                    }
                ],
                "keys": {
                    "columns": ":not(:first-child)",
                    "editor": "editor"
                },
                "buttons": [{
                        extend: 'copy',
                        title: 'Reporte',
                        titleAttr: 'Click para Copiar',
                        text: '<div class="contenedor_copiar"><i class="fa fa-clipboard copiar fa-1x" style="color: #045fb4;"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];
                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }
                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }
                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Reporte',
                        titleAttr: 'Click para descargar como Excel',
                        text: '<div class="contenedor_excel"><i class="fa fa-file-excel-o excel"></i><label class="labe"></label></div>',

                        action: function(e, dt, node, config) {
                            exportToExcel('table_est_cta');
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Reporte',
                        titleAttr: 'Click para descargar como CSV',
                        text: '<div class="contenedor_csv"><i class="fa fa-file-text-o csv"></i><label class="labe"></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];
                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }
                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }
                                    return respuesta;
                                }
                            },
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        titleAttr: 'Click para Imprimir PDF',
                        title: function() {
                            return "Estado de Cuenta Proveedores";
                        },
                        footer: true,
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<div class="contenedor_pdf"><i class="fa fa-file-pdf-o pdf"></i><label ></label></div>',
                        exportOptions: {
                            format: {
                                body: function(data, row, column, node) {
                                    var retorno = "",
                                        tag, respuesta = "",
                                        reponer = [];

                                    tag = $(node).find('input');
                                    if (tag.length > 0) {
                                        retorno = retorno + ($(tag).map(function() {
                                            return $(this).val();
                                        }).get().join(','));
                                    }

                                    respuesta = (retorno != "") ? retorno : $.trim($(node).text());
                                    for (i = 0; i < reponer.length; i++) {
                                        $(node).append(reponer[i]);
                                    }

                                    return respuesta;
                                }
                            },
                        }
                    }
                ],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningun dato disponible en esta tabla",
                    "sInfo": "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Filtrar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Ultimo",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });

            jsRemoveWindowLoad();

        }

        function exportToExcel(id) {
            var table = $(`#${id}`).DataTable();
            var data = table.rows().data().toArray();

            // Crea un nuevo libro de Excel
            var wb = XLSX.utils.book_new();
            var ws_data = [];

            // Obtener los encabezados de la tabla dinámicamente
            var headers = [];
            $(`#${id} thead tr th`).each(function() {
                headers.push($(this).text().trim());
            });

            // Agregar los encabezados obtenidos al array de datos
            ws_data.push(headers);

            // Agregar los datos de la tabla al array ws_data de manera dinámica
            data.forEach(function(row) {
                var nombre = row.nombre;
                if (row.nombre.comprobante) {
                    nombre = row.nombre.comprobante;
                }

                ws_data.push([
                    row.num,
                    row.sucursal,
                    row.ruc,
                    nombre,
                    row.telefono,
                    row.vence,
                    row.direccion,
                    row.debito,
                    row.credito,
                    row.saldo
                ]);
            });

            // Crear la hoja de cálculo
            var worksheet = XLSX.utils.aoa_to_sheet(ws_data);

            // Obtener nombre y apellido desde localStorage
            let nombre = localStorage.getItem('U_NOMBRE') || 'Desconocido';
            let apellido = localStorage.getItem('U_APELLIDO') || '';

            // Añadir la hora de generación del reporte y el nombre del generador
            let currentDateTime = new Date().toLocaleString();
            let generatedByCellAddress = XLSX.utils.encode_cell({
                c: 0,
                r: ws_data.length
            }); // Añadir en la fila siguiente a los datos
            worksheet[generatedByCellAddress] = {
                t: "s",
                v: `Reporte generado por: ${nombre} ${apellido} el: ${currentDateTime}`
            };

            // Ajustar el rango si es necesario
            var range = XLSX.utils.decode_range(worksheet['!ref']);
            range.e.r = ws_data.length; // Ajustar para incluir la nueva fila
            worksheet['!ref'] = XLSX.utils.encode_range(range);

            // Añadir la hoja al libro
            XLSX.utils.book_append_sheet(wb, worksheet, `Reporte_${id}`);

            // Guardar el archivo
            XLSX.writeFile(wb, `Reporte_${id}.xlsx`);
        }
    </script>
    <div align="center">
        <form id="form1" name="form1" action="javascript:void(null);" onkeypress="if (event.keyCode == 13) { return false; }">
            <a type="hidden" id="enlace_descarga"></a>
            <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                <tr>
                    <td valign="top" align="center">
                        <div id="DivPresupuesto"></div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <div id="Paginacion"></div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <div id="Reporte_Xml23"></div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- <div id="divReporteOnus"></div> -->
                                            <div class="table responsive" style="width: 100%;">
                                                <table id="table_est_cta" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;" align="center">
                                                    <thead>
                                                        <tr>
                                                            <th class="bg-primary">N°</th>
                                                            <th class="bg-primary">Emisión</th>
                                                            <th class="bg-primary">Tran</th>
                                                            <th class="bg-primary">Documento</th>
                                                            <th class="bg-primary">N° Factura</th>
                                                            <th class="bg-primary">Comprobante</th>
                                                            <th class="bg-primary">Vence</th>
                                                            <th class="bg-primary">Detalle</th>
                                                            <th class="bg-primary">Debito</th>
                                                            <th class="bg-primary">Credito</th>
                                                            <th class="bg-primary">Saldo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- <div id="DivReporte"></div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <div id="Listado"></div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <div style="width: 100%;">
                            <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">DIARIO CONTABLE <span id="divTituloAsto"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <!-- Nav tabs -->
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li role="presentation" class="active"><a href="#divInfo" aria-controls="divInfo" role="tab" data-toggle="tab">Informacion</a></li>
                                                    <li role="presentation"><a href="#divDirectorio" aria-controls="divDirectorio" role="tab" data-toggle="tab">Directorio</a></li>
                                                    <li role="presentation"><a href="#divRetencion" aria-controls="divRetencion" role="tab" data-toggle="tab">Retencion</a></li>
                                                    <li role="presentation"><a href="#divDiario" aria-controls="divDiario" role="tab" data-toggle="tab">Diario</a></li>
                                                    <li role="presentation"><a href="#divAdjuntos" aria-controls="divAdjuntos" role="tab" data-toggle="tab">Adjuntos</a></li>
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    <div role="tabpanel" class="tab-pane active" id="divInfo">...</div>
                                                    <div role="tabpanel" class="tab-pane" id="divDirectorio">...</div>
                                                    <div role="tabpanel" class="tab-pane" id="divRetencion">...</div>
                                                    <div role="tabpanel" class="tab-pane" id="divDiario">...</div>
                                                    <div role="tabpanel" class="tab-pane" id="divAdjuntos">...</div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div id="myModalProveedores" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">LISTA DE PROVEEDORES</h4>
                                        </div>
                                        <div class="modal-body">
                                            <table id="table_proveedor" class="table table-striped table-bordered table-hover table-condensed" style="width: 100%;" align="center">
                                                <thead>
                                                    <tr>
                                                        <td colspan="5" class="bg-primary">LISTA PROVEEDORES</td>
                                                    </tr>
                                                    <tr class="info">
                                                        <td>Ruc</td>
                                                        <td>Nombre</td>
                                                        <td>Seleccionar</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        xajax_genera_cabecera_formulario()
    </script>
    <? /********************************************************************/ ?>
    <? /* NO MODIFICAR ESTA SECCION*/ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /********************************************************************/ ?>