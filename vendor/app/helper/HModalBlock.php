<?php


class HModalBlock
{

    private $modal_close = false;

    private $modal_title;

    private $modal_body;

    private $class;



    public function __construct()
    {
        $this->setClass(CURRENT_CLASS);
    }



    public function setModalClose()
    {
        $this->modal_close = true;
        return;
    }

    /**

     * @param string $modal_title

     */

    public function setModalTitle($modal_title)
    {
        $this->modal_title = $modal_title;
        return;
    }

    /**

     * @param string $modal_body

     */
    public function setModalBody($modal_body)
    {
        $this->modal_body = $modal_body;
        return;
    }
    /**

     * @param string $class

     */

    public function setClass($class)
    {

        $this->class = $class;

        return;

    }

    public function drawModal()
    {

        /*if ( !$this->class )

        {

            return "Clase no Definida!.";

        }*/

        ob_start();

        ?>
        <!-- ======= Modal generico ======== -->
        <div id="<?= $modal = "modal-" . uniqid() ?>" class="modal fade" role="dialog" rel="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" title="Cerrar" id="cerrar-modal" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="modal-title"><?= $this->modal_title ?></h4>
                    </div>

                    <div class="modal-body" id="modal-body"><?= $this->modal_body ?></div>
                    <?php if ( $this->modal_close ) : ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style type="text/css">

            <?php

            echo "#{$modal} .form-group { margin-bottom: 6px; }\n";

            echo "#{$modal} {padding-right:0 !important}\n";

            ?>

        </style>

        <script type="text/javascript">

            var id_input;

            //params : json key:value type
            // *** Levanta un modal para editar un producto ****
            function get_modal_form(params, method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();

                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            
            //params : json key:value type
            function get_modal_form_editar_venta(params, method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_editar_tipo_venta(params, method)
            {
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();

                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // *** Levanta un modal para cargar un gasto ***
            function get_modal_form_nuevo_gasto(method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();

                $.post(url, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});


            // *** Levanta un modal para cargar un iva compra ***
            function get_modal_form_nuevo_iva_compra(method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();

                $.post(url, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // *** Levanta un modal para cargar un iva venta ***
            function get_modal_form_nuevo_iva_venta(method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();

                $.post(url, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // *** Levanta un modal para actualizar precios de categorias ***
             function get_modal_form_precios_categoria(params, method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + ("form_precios_categorias");
                console.log('url::: ', url);

                // if ( method && method.search(/\//) >= 0 )
                // {
                //     url = method;
                // }
                before_send();

                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});


            // *** ***
            function get_modal_form_descuento_efectivo(id)
            {
                id_input = id;

                var html_modal = $('#<?=$modal?>');
                
                html_modal.find('#modal-body').html(`

                <div class="panel panel-success">

                    <div class="panel-body">

                            <div class="row">

                            <div class="col-md-6 form-group">

                                    <h4>Descuento efectivo%</h4>

                                    <h6>Ingrese el porcentaje de descuento.</h6>

                                    <input type="text" id="descuento_efectivo" placeholder="0" name="descuento_efectivo" maxlength="3" class="form-control" required>

                            </div>
                      
                        </div>

                    <div class="form-group" style="text-align:right">
                        <button id="btnDesc" name="sve" class="btn btn-default">Continuar</button>
                    </div>
                </div>

                `);
                html_modal.modal();


            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // *** Levanta un modal para cargar la cantidad de bultos ****
            function get_modal_form_bulto(nro_remito)
            {
                id_remito = nro_remito;

                var html_modal = $('#<?=$modal?>');

                html_modal.find('#modal-body').html(`

                <div class="panel panel-success">

                    <div class="panel-body">

                            <div class="row">

                            <div class="col-md-6 form-group">

                                    <h4>Bultos</h4>

                                    <h6>Ingrese la cantidad de bultos.</h6>

                                    <input type="text" id="cantidad_bultos" placeholder="0" name="cantidad_bultos" maxlength="3" class="form-control" required>
                            </div>
                    
                        </div>

                    <div class="form-group" style="text-align:right">
                        <button id="btnBulto" name="sve" class="btn btn-default">Continuar</button>
                    </div>
                </div>

                `);
                html_modal.modal();
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

             //params : json key:value type
             function get_modal_form_editar_comprobante(params, method)
            {

                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modalForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_editar_venta_quimico(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "edicionVentaQuimico");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_confirmar_pedido(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "confirmar_pedido_modal");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_editar_producto_vencido(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "edicionProductoVencido");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_editar_cuenta(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "edicionCuentaCorriente");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_editar_inversor(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "edicionInversor");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

             //params : json key:value type
             function get_modal_form_nueva_inversion(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "nuevaInversion");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function get_modal_form_pago_inversion(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "pagoInversionForm");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

             //params : json key:value type
             function dt_delete_inversor(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "eliminarInversor");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // ========== simulador inversion ========================
            function get_modal_form_simulador_inversion(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "simulador");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // ========== simulador inversion compuesta========================
            function get_modal_form_simulador_inversion_compuesta(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "simulador_compuesta");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // ==========  ========================
            function get_modal_facturacion_electronica(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modal_facturacion_electronica");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            // ==========  ========================
            function get_modal_editar_cliente(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modal_editar_cliente");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //params : json key:value type
            function dt_delete_cliente(params)
            {
                method = '';
                
                var html_modal = $('#<?=$modal?>');

                var url = "!<?=$this->class?>/" + (method || "modal_delete_cliente");

                if ( method && method.search(/\//) >= 0 )
                {
                    url = method;
                }
                before_send();
                
                $.post(url, params, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');
            }
            $('#<?=$modal?> .modal-header').css({"border-bottom": "none", "padding": "12px 15px 0"});

            //Use this code if button is appended in the DOM
            $(document).on('click','#btnDesc',function(){
                var str = $("#descuento_efectivo").val();

                if(str <= 0)
                {
                    window.localStorage.setItem('descuento_efectivo',0 );
                }
                else
                {
                    window.localStorage.setItem('descuento_efectivo',str );

                    // disparar evento para hacer calculo
            
                    let porcentaje_descuento = window.localStorage.getItem('descuento_efectivo');

                    importe_efectivo_descuento = monto * ( (100-porcentaje_descuento) / 100);

                    $(`#monto-${id_input}`).val(importe_efectivo_descuento);
                    
                    labelMontoCompra = labelMontoCompra - (importe_efectivo_descuento);

                    labelTotal.querySelector('span').innerText = +labelTotal.querySelector('span').innerText - (monto - importe_efectivo_descuento);

                    in_monto = monto - parseFloat(importe_efectivo_descuento);   // monto input (en gris)

                    pc_pagos[id_input + "&" + 1] = importe_efectivo_descuento;
                    
                }

                $("#cerrar-modal").click();

            });

            //Use this code if button is appended in the DOM
            $(document).on('click','#btnBulto',function(){
                var cantidad_bultos = $("#cantidad_bultos").val();
                
                var url = "!<?=$this->class?>/" + ("guardarBulto");

                $.post(url, { cantidad_bultos: cantidad_bultos, nro_remito: id_remito}, function (response) {
                    before_send();
                    if ( response.error )
                    {
                        return;
                    }
                    // html_modal.find('#modal-title').html(response.title);
                    html_modal.find('#modal-body').html(response.body);
                    html_modal.modal();
                }, 'json');

                $("#cerrar-modal").click();
                $("#a-reset").click();
                


            });


        </script>

        <?php

        //ob_end_flush();

        return ob_get_clean();

    }

}



?>