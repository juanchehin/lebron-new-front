<div class="panel panel-default">

    <div class="panel-body">

        <div class="row">

            <div class="col-md-6">

                <div class="form-group text-left" id="dv-options">

                    <a href="<?= $panel_uri ?>/productos" class="btn btn-default">Atr&aacute;s</a>

                </div>

                <form id="frm-attr" action="<?= ($frm_action = "!AdminProductoAtr/guardar") ?>" autocomplete="off">

                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="id_categoria">Categor&iacute;a: <i class="required"></i></label>

                            <select name="id_categoria" id="id_categoria" class="form-control">

                                <option value="">Seleccionar</option>

                                <?php

                                foreach ($categorias as $ctg)

                                {

                                    echo "<option value='{$ctg->id_item}' " . ($row->id_categoria == $ctg->id_item ? "selected" : null) . ">{$ctg->categoria}</option>";

                                }

                                ?>

                            </select>

                        </div>

                        <div class="form-group col-md-6">

                            <label for="id_producto_rel">Articulo relacionado:</label>

                            <select name="id_producto_rel" minlength="0" id="id_producto_rel" class="form-control" disabled>

                                <option value=""></option>

                                <?php if ( false ): foreach ($articulos as $articulo): ?>

                                    <option value="<?= $articulo->id_producto ?>" rel="<?= $articulo->online_price ?>"><?= "{$articulo->id_producto} - " . $articulo->nombre_producto ?></option>

                                <?php endforeach; endif; ?>

                            </select>

                        </div>

                        <div id="dv-parent-data" style="display: inline-block;">

                            <div id="dv-block-fields"></div>

                            <div class="form-group col-md-6">

                                <br/>

                                <?php

                                $exc[] = ($id_producto = $row->id_producto);

                                foreach ($row->items_promo as $item)

                                {

                                    $exc[] = $item->id_producto;

                                    $itemsPromo[$item->id_producto] = array('label' => $item->nombre_producto, 'precio' => $item->ecommerce_precio);

                                }

                                #--

                                $val = $row->dimension_array;

                                $inputs = null;

                                /*foreach (array('alto', 'ancho', 'largo', 'peso') as $item)

                                {

                                    $inputs .= "<div class='input-group-addon' style='text-align:left;padding:2px 0;background: none;border: none'>";

                                    $inputs .= "<label for='js-{$item}'>" . ucfirst($item) . "</label>";

                                    $inputs .= "<input type='tel' id='js-{$item}' name='dimension[{$item}]' value='{$val[$item]}' class='form-control'>";

                                    $inputs .= "</div>";

                                }*/

                                foreach (array('n' => "Normal", 'o' => "Oferta") as $k=>$item)

                                {

                                    $inputs .= "<div class='input-group-addon' rel='web-opt' style='text-align:left;padding:2px 0;background: none;border: none'>";

                                    $inputs .= "<label for='js-{$k}' class='btn btn-warning'>";

                                    $inputs .= "<input type='radio' id='js-{$k}' name='web_option' value='{$k}'> " . ucfirst($item);

                                    $inputs .= "</label>";

                                    $inputs .= "</div>";

                                }

                                echo $inputs;

                                echo '<input type="hidden" id="itemsPromo" name="itemPromo" value="'.json_encode(array_keys($itemsPromo), JSON_FORCE_OBJECT).'"/>';

                                ?>

                            </div>

                            <div class="form-group col-md-3">

                                <h4 style="margin:0 0 4px;">$ <span id="label-precio"><?= ($precio = $row->ecommerce_precio) ?></span></h4>

                                <label for="publicado" class="btn btn-default">

                                    <input type="checkbox" id="publicado" name="publicado" <?= ($row->publicado ? "checked" : null) ?>>

                                    &nbsp;Publicado

                                </label>

                            </div>

                            <div class="form-group col-md-3">

                                <label for="precio"><span>Recargo</span> (%):</label>

                                <label for="check-precio" style="position: absolute;top:26px;left:20px">

                                    <input id="check-precio" name="descuento" type="checkbox">

                                </label>

                                <input type="tel" rel="<?= $precio ?>" id="precio" name="precio" class="form-control">

                            </div>

                            <div class="col-md-12 form-group">

                                <label for="texto">Texto:</label>

                                <textarea name="texto" id="texto" rows="10" class="form-control"><?= $row->texto ?></textarea>

                            </div>

                        </div>

                        
                        <div class="row col-md-12">

                            <!-- *** Vencimiento ** -->

                            <div class="form-group col-md-6">

                                <label for="">Vencimiento:  </label>

                                <input value="<?= $row->fecha_vencimiento ?>" type="date" id="fecha_vencimiento" name="fecha_vencimiento"> 

                            </div>


                            <!-- ***** -->

                        </div>

                        <div class="row col-md-12" id="iva_desc">

                         <!-- *** IVA ** -->

                            <div class="form-group col-md-6">

                                <label for="">IVA:  </label>

                                <select name="iva" id="iva" class="form-control">

                                    <option <?php if($row->iva == 'N'){echo("selected");}?>>No</option>
                                    <option <?php if($row->iva == 'S'){echo("selected");}?>>Si</option>

                                </select>

                            </div>


                            <!-- ***** -->


                            <!-- *** Descuento ** -->

                            <div class="form-group col-md-6">

                                <label for="">Descuento proveedor:  </label>

                                <input value="<?= $row->descuento_prov ?>" type="text" id="desc_prov" name="desc_prov"> 

                            </div>


                            <!-- ***** -->

                              <!-- *** Costo ** -->

                                <div class="form-group col-md-12">

                                    <label for="">Costo:  </label>

                                    <input value="<?= $row->costo ?>" type="text" id="costo" name="costo"> 

                                </div>


                                <!-- ***** -->
                        </div>

                        <div class="row col-md-12" id="iva_desc">

                        </div>


                        <?php

                        $inputHidden = "<input type='hidden' name='id_producto' value='{$id_producto}'/>";

                        echo $inputHidden;

                        ?>

                        <div class="col-md-12">

                            <button type="submit" id="save-btn" class="btn btn-primary">Aceptar</button>

                        </div>

                    </div>

                </form>

            </div>

            <div class="col-md-6 form-group">

                <form id="frm-image" action="<?= $frm_action . "?img=1" ?>">

                    <div class="row">

                        <br/>

                        <input type="hidden" name="id_imagen" id="id_imagen">

                        <div class="form-group col-md-5">

                            <?= $inputFile ?>

                            <br/>

                            <button type="submit" class="btn btn-primary">Agregar</button>

                        </div>

                        <div class="form-group col-md-7">

                            <h5 class="text-uppercase">Im&aacute;genes</h5>

                            <div id="imagenes"></div>

                        </div>

                    </div>

                    <?= $inputHidden ?>

                </form>



            </div>

        </div>

    </div>

</div>

<style>

    #articulo-imagen img {

        border: 1px solid #ccc;

    }



    .image-actions {

        position: absolute;

        right: 10px;

        padding: 5px 10px;

    }



    .image-actions a {

        background: #eee;

        border-radius: 3px;

        padding: 3px 5px;

        border: 1px solid #ccc;

    }



    #dv-block-fields {

        position: absolute;

        z-index: 5;

        left: 15px;

        right: 15px;

        border: none;

        background: none;

        overflow-y: auto;

        height: 0;

    }

</style>

<script>

    window.onload = function() {
          // Chequeo usuario
        var usuario_actual = <?php echo json_encode($admin_user->id_usuario); ?>;

        if(usuario_actual != 1 && usuario_actual != 25)
        {
            $("#iva_desc").hide();
        }

    };

    document.getElementsByClassName('page-header')[0].insertAdjacentHTML("beforeend", "<?=$row->detalle . " ({$row->cantidad_online}) {$itemUrl}"?>");

    new Jodit("#texto", {

        "uploader": {

            "insertImageAsBase64URI": true

        },

        "height": 300,

        "language": "es",

        "direction": "ltr",

        "askBeforePasteHTML": false,

        "askBeforePasteFromWord": false,

        "buttons": ",,,,,,,,,,,,,,,,fontsize,paragraph,|,file,video,link,|,align,undo,redo,\n,cut,hr,eraser,copyformat,|,symbol,selectall,print"

    });

    var selectOpts = {}, elementParent = "<?=$row->id_parent?>";



    itemsPromo = JSON.parse(sessionStorage.getItem("itemsPromo_<?=$id_producto?>") || '{}');

    esPromo = false;



    parentArticulo = document.getElementById('id_producto_rel');

    blockFields = document.getElementById("dv-block-fields");

    saveBtn = document.getElementById('save-btn');

    checkPublicado = document.getElementById('publicado');

    checkPrecio = document.getElementById('check-precio');

  


    document.getElementById('js-<?=$row->dimension ?: "n"?>').setAttribute("checked", true);



    $(iptPrecio = document.getElementById('precio')).decimal(".");

    checkPrecio.onclick = function () {

        span = "Recargo";

        iptPrecio.removeAttribute("required");

        iptPrecio.value = 0;

        if ( this.checked )

        {

            span = "Descuento";

            iptPrecio.setAttribute("required", 1);

            iptPrecio.value = "<?= abs(floatval($row->precio_online)) ?>";

            iptPrecio.focus();

        }

        iptPrecio.onkeyup();

        document.querySelector('[for="precio"] span').innerHTML = span;

    };



    iptPrecio.onkeyup = function () {

        monto = parseFloat(this.getAttribute("rel"));

        thisValue = parseFloat(this.value || 0);

        if ( checkPrecio.checked )

        {

            thisValue *= -1;

        }

        //showPromoItems(1);

        document.getElementById('label-precio').innerText = (monto += (monto * (thisValue / 100))).toFixed(2);

    };



    if ( (selectCategoria = document.getElementById('id_categoria')) )

    {

        selectCategoria.onchange = function () {

            id_marca = "<?=$row->id_marca?>";

            let id_categoria = "";

            selectOpts["exc"] = JSON.stringify(Object.keys(itemsPromo).concat(['<?=$id_producto?>']));

            cssJs = {"height": 0};

            if ( !this.getAttribute("rel") )

            {

                if ( (aaVerItems = document.getElementById('aa-ver-items')) )

                {

                    aaVerItems.remove();

                }

                if ( (esPromo = (this.value === "<?=Categoria::ctgPromo?>")) )

                {

                    parentArticulo.insertAdjacentHTML("beforebegin", "<a class='pull-right' onclick='showPromoItems()' href='javascript:void(0)' id='aa-ver-items'>Artículos</a>");

                    id_marca = id_categoria = 0;

                    elementParent = 0;

                }

                showPromoItems(1);//1

                selectOpts["id_categoria"] = id_categoria;

                selectOpts["id_marca"] = id_marca;

                fetch("!AdminArticulo/selectArticulo", {

                    "method": "POST",

                    "body": new URLSearchParams(selectOpts)

                }).then(function (ntwRes) {

                    ntwRes.json().then(function (jsonRes) {

                        htmlOptions = "<option value=''></option>";

                        for (const id in jsonRes)

                        {

                            item = jsonRes[id];

                            htmlOptions += `<option value="${id}" rel="${item.precio}">${id} - ${item.label}</option>`;

                        }

                        parentArticulo.innerHTML = htmlOptions;

                        parentArticulo.value = elementParent;

                        parentArticulo.removeAttribute("disabled");

                    })

                });

            }

            this.removeAttribute("disabled");

            saveBtn.type = "submit";

            saveBtn.removeAttribute("disabled");

            //blockFields.remove();

            if ( parseInt(elementParent) && !this.getAttribute("rel") )

            {

                this.value = "";

                saveBtn.type = "";

                this.setAttribute("disabled", true);

                saveBtn.setAttribute("disabled", true);

                cssJs["height"] = "83%";

                //dvParentData.insertAdjacentElement("afterbegin", blockFields);

            }

            this.removeAttribute("rel");

            Object.assign(blockFields.style, cssJs);

        };



        selectCategoria.onchange();

    }



    checkPrecio.checked = (parseInt(<?=intval($row->precio_online < 0)?>) || esPromo);

    checkPrecio.onclick();



    function showPromoItems(hide)

    {

        if ( hide )

        {

            checkPublicado.removeAttribute("disabled");

            if ( !esPromo )

            {

                return;

            }

            //itemsHtml = "<span style='cursor:pointer;position: absolute;top:0;font-size:20px;right:20px' onclick='showPromoItems()'>&#8864;</span>";

            itemsHtml = "";

            total_promo = 0;

            for (const cup in itemsPromo)

            {

                itemInfo = itemsPromo[cup];

                itemsHtml += `<div id="${cup}" class="list-group-item">`;

                itemsHtml += `<a href='javascript:void(0)' class="text-danger" rel="item_${cup}"><i class="fa fa-trash"></i></a> `;

                itemsHtml += itemInfo.label;

                itemsHtml += `</div>`;

                total_promo += parseFloat(itemInfo.precio);

            }

            document.getElementById('modal-body').innerHTML = itemsHtml;

            document.querySelectorAll('[rel^="item_"]').forEach(function (opcion) {

                opcion.onclick = function () {

                    key = this.getAttribute("rel").replace(/.+_/g, "");

                    delete itemsPromo[key];

                    //showPromoItems(1);//3

                    selectCategoria.onchange();

                };

            });



            iptPrecio.setAttribute("rel", total_promo);

            iptPrecio.onkeyup();

            if ( Object.keys(itemsPromo).length < 2 )

            {

                if ( checkPublicado.checked )

                {

                    //checkPublicado.checked = false;

                    checkPublicado.click();

                }

                checkPublicado.setAttribute("disabled", true);

            }

            sessionStorage.setItem("itemsPromo_<?=$id_producto?>", Object.keys(itemsPromo).length ? JSON.stringify(itemsPromo) : '<?=json_encode((array)$itemsPromo, JSON_FORCE_OBJECT)?>');

            document.getElementById('itemsPromo').value = JSON.stringify(Object.keys(itemsPromo));

            return;

        }

        $('[rel="modal"]').modal();

        /*if ( !parseInt(blockFields.style["height"]) )

        {

            showPromoItems(1);//2

            Object.assign(blockFields.style, {"background": "#fff", "border": "2px solid #000", "padding": "25px 20px", "height": "180px"});

            return;

        }

        blockFields.innerHTML = "";

        blockFields.removeAttribute("style");*/

    }



    $(parentArticulo).val(elementParent).on("change", function () {

        let option = this.options[this.selectedIndex];

        if ( (btnAddItem = document.getElementById('btn-add-item')) )

        {

            btnAddItem.remove();

        }

        if ( !esPromo )

        {

            elementParent = this.value;

            selectCategoria.setAttribute("rel", 1);

            selectCategoria.onchange();

        }

        else

        {

            if ( this.value )

            {

                dvOkBtn = `<button type="button" id="btn-add-item" style="position: absolute" class="btn btn-success">✓</button>`;

                this.parentElement.insertAdjacentHTML("beforeend", dvOkBtn);

                document.getElementById('btn-add-item').onclick = () => {

                    itemsPromo[this.value] = {"label": option.innerText, "precio": option.getAttribute("rel")};

                    this.value = "";

                    $(this).change();

                    selectCategoria.onchange();

                };

            }

        }

        //document.getElementById('precio').value = option.getAttribute("rel");

    }).selectar();





    document.getElementById('frm-attr').onsubmit = function (evt) {

        evt.preventDefault();

        submit_form(this, function () {

            selectCategoria.onchange();

        });

    };



    document.getElementById('frm-image').onsubmit = function (e) {

        e.preventDefault();

        if ( !document.getElementsByName('input_file')[0].value )

        {

            jdialog("Seleccionar una imagen");

            return;

        }

        let thisForm = this;

        submit_form(thisForm, function () {

            document.getElementById('cropit-image-zoom').value = 1;

            document.getElementsByClassName('cropit-preview-image')[0].setAttribute("src", "");

            document.getElementsByClassName('cropit-image-input')[0].value = "";

            document.getElementById('cropit-values').value = "";

            imagenes();

        });

    };



    checkPublicado.onclick = function () {

        fetch("!AdminProducto/setEstado", {

            "method": "POST",

            "body": new URLSearchParams({

                "id_articulo": "<?=$id_producto?>",

                "estado": Number(this.checked),

                "attr": this.name

            })

        });

    };



    function img_borrar(id)

    {

        jconfirm(function ($true) {

            if ( !$true )

            {

                return;

            }

            fetch("!AdminProductoAtr/borrarImagen?id=" + id).then(function ($res) {

                $res.json().then(function ($js) {

                    if ( $js.error )

                    {

                        jdialog($js.error);

                        return;

                    }

                    imagenes();

                })

            });

        }, "Esta imagen se eliminará. ¿Continuar?");

    }



    function imagenes()

    {

        let imageContainer = document.getElementById('imagenes');

        imageContainer.innerHTML = "<i class='fa fa-spin fa-spinner'></i>";

        fetch("!AdminProductoAtr/imagenesArticulo", {

            "method": "POST",

            "body": new URLSearchParams({

                "cup": "<?=$row->id_producto?>"

            })

        }).then(function ($res) {

            return $res.text().then(function ($html) {

                imageContainer.innerHTML = $html;

            });

        });



        for (const k in sessionStorage)

        {

            key = k.replace(/\w+_/g, "");

            if ( key !== "<?=$row->id_producto?>" )

            {

                delete sessionStorage[k];

            }

        }

    }



    imagenes();

</script>