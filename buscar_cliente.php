<?
    if (isset($_REQUEST['opSearchClpv']))
        $opSearchClpv = $_REQUEST['opSearchClpv'];
    else
        $opSearchClpv = '';

    if (isset($_REQUEST['cliente']))
        $cliente = $_REQUEST['cliente'];
    else
        $cliente = '';
?>

<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Clientes</title>
        <!--CSS-->    
        <link rel="stylesheet" href="media/css/bootstrap.css">
        <link rel="stylesheet" href="media/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="media/font-awesome/css/font-awesome.css">
        <!--Javascript-->    
        <script src="media/js/jquery-1.10.2.js"></script>
        <script src="media/js/jquery.dataTables.min.js"></script>
        <script src="media/js/dataTables.bootstrap.min.js"></script>          
        <script src="media/js/bootstrap.js"></script>
        <script src="media/js/lenguajeusuario_.js"></script>   
        <script src="js/teclaEvent.js" type="text/javascript"></script>  
        <script>

            shortcut.add("Esc", function() {
                close();
            });

            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip(); 
            });

            function seleccionaItem(a, b){
                window.opener.cargarDatosClpv(a, b);
                window.close();
            }
        </script>   
    </head>

    <body>
        <div class="container-fluid">
            <div class="col-md-12 table-responsive"> 
                <input type="hidden" name="opSearch" id="opSearch" value="<?=$opSearchClpv?>">
                <input type="hidden" name="nomClpv" id="nomClpv" value="<?=$cliente?>">
                <table id="divClientes" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                    <thead>
                        <tr class="info">
                            <th>Codigo</th>
                            <th>RUC</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr class="info">
                            <th>Codigo</th>
                            <th>RUC</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>        
            </div>
        </div>
    </body>
</html>
