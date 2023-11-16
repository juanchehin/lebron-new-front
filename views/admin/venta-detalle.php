<div class="<?= $hidden ? "" : "panel panel-default"; ?>">
    <?php if ( !$hidden ) : ?>
        <div class="panel-heading">
            Detalle de venta Nro <?= $data->id_venta ?>
        </div>
    <?php endif; ?>
    <div class="panel-body">
        <div id="print-area">
            <div class="form-group" id="ticket">
                <?php list($fecha, $hora) = preg_split("#\s+#", $data->fecha); ?>
                <h5>Fecha: <?= $fecha ?>&nbsp;&nbsp;Hora: <?= $hora ?></h5>
                <h5 class="text-center">
                    <?php if ( $cliente = $data->hasPersona ) : ?>
                        A <?= $cliente->nombre_apellido ?>
                    <?php else : ?>
                        A Consumidor Final
                    <?php endif; ?>
                </h5>
                <div class="header" style="display:none;width: 99%">
                    <div class="col-50">Producto</div>
                    <div class="col-25">Importe</div>
                </div>
                <div class="lineas">
                    <?php foreach ($data->hasLineaVenta as $linea) : ?>
                        <div class="linea">
                            <div class="col-50"><?= $linea->producto ?> (x<?= $linea->cantidad ?>)</div>
                            <div class="col-25"><?= HFunctions::formatPrice($linea->subtotal) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="total-group">Total $ <span class="pull-right"><?= HFunctions::formatPrice($data->total) ?></span></div>
            </div>
            <style type="text/css">
                #ticket {
                    /*font-family: "Times New Roman", "Liberation Serif";*/
                    font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
                    font-size: 12px;
                    border: 2px dashed #222;
                    padding: 8px;
                }

                #ticket .header {
                    text-transform: uppercase;
                    margin-bottom: 6px;
                    font-weight: 700;
                }

                #ticket .lineas {
                    height: 190px;
                    overflow-y: auto;
                }

                #ticket .linea, #ticket .header {
                    padding: 3px 0;
                    width: 100%;
                    display: inline-block;
                }

                #ticket [class*="col-"] {
                    display: inline-block;
                }

                #ticket .col-50 {
                    width: 71%;
                }

                #ticket .col-25 {
                    width: 25%;
                    text-align: right;
                }

                #total-group {
                    /*text-align: right;*/
                    padding-right: 8px;
                    font-size: 17px;
                    font-weight: bold;
                    text-transform: uppercase;
                }

                @media print {
                    #ticket {
                        width: 283px;
                        margin: auto;
                    }

                    .lineas {
                        height: auto;
                        overflow-y: auto;
                    }
                }
            </style>
        </div>
        <?php if ( !$hidden ) : ?>
            <div class="form-group text-right">
                <button type="button" class="btn btn-primary" onclick="imprimir()">Imprimir</button>
                <button type="button" class="btn btn-default" id="btn-close" data-dismiss="modal">Cerrar</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function imprimir($callback)
    {
        $('#print-area').printArea({}, $callback);
    }
</script>