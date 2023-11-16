<?php



class FrontArticuloMayoristas extends FrontMainMayoristas

{

    public function index()

    {

        $this->setPageTitle("Catálogo");

        ob_start();

        ?>

        <div class="form-group">

            <?= $this->allProducts() ?>

        </div>

        <?php

        $this->setBody(ob_get_clean(), true);

    }



    public function detalle($id_articulo)

    {

        $this->addStyle("static/plugin/smooth/smoothproducts.css");

        $this->addScript("static/plugin/smooth/smoothproducts.min.js");

        $detalle = array();

        if ( !$articulo = Articulo::find($id_articulo) )

        {

            Router::redirect(self::siteUrl);

        }

        #--

        $detalle[0] = "Precio: $ {$articulo->ecommerce_precio}";

        $detalle[] = "<b>Marca:</b> {$articulo->marca}";

        $detalle[] = $articulo->peso_label;

        if ( !$articulo->hasAtributo->count() && !$articulo->no_sabor )

        {

            $detalle[] = $articulo->sabor_label;

        }

        #--

        if ( $ctg = $articulo->categoria )

        {

            $detalle[] = "<b>Tipo:</b> {$ctg}";

        }

        $this->addFbProperty("og:image", self::siteUrl . "/{$articulo->imagenes(true)}");

        $this->addFbProperty("og:description", strip_tags(implode(". ", $detalle)));

        $this->setPageTitle($articulo->nombre);

        unset($detalle[0]);

        $attr = array(

            'item' => $articulo,

            'imagenes' => $articulo->imagenes(false, false),

            'detalle_articulo' => implode("<br/>", $detalle)

        );



        $data = new FrontInicio();

        $attr['related'] = $data->articulos($articulo->id_categoria, 4);

        $attr['cart'] = static::articuloOpcionMayoristas($articulo);

        $this->setParams($attr);

        $this->setBody("detalle-articulo");

    }



    // ***** Mayoristas *****
    public static function articuloOpcionMayoristas(Articulo $item)
    {

        if ( !$item->id_producto )

        {

            return null;

        }

        $id = $item->id_producto;

        $data = array();

        $href_url = self::siteUrl . "/articulo/{$id}";

        $precio = $item->precio_compra;
        
        if( !$precio )
        {
            $id_prod_relacionado = $item->id_parent;

            $result = Articulo::find($id_prod_relacionado);

            $precio = $result->precio_compra;

        }

        $articulos = $item->getItemsPromoAttribute() ?: array($item);

        $esPromo = ($item->id_categoria == Categoria::ctgPromo);

        $data_attr = "";

        foreach ($item->array_items_promo as $i => $v)

        {

            $data_attr .= "data-" . ($i + 1) . "='{$v}' ";

        }

        //HArray::varDump($articulo->hasAtributo()->count(), false);

        ob_start();

        ?>

        <form id="<?= $id ?>" <?= $data_attr ?> rel="<?= ($stock = floatval($item->cantidad_online)) ?>" autocomplete="off" onsubmit="return false">

            <?php foreach ($articulos as $idx => $articulo): ?>

                <?php

                $showStock = true;

                $variantes = $articulo->hasAtributo()->where('borrado', 0)->get();

                if ( $id != $articulo->id_producto )

                {

                    echo "<h4 style='margin:10px 0 2px'>● {$articulo->nombre}</h4>";

                }

                ?>

                <?php if ( count($variantes) ): ?>

                    <?php

                    $sabor = "| " . $articulo->variante;

                    $showStock = false;

                    $variantes[] = $articulo;

                    ?>

                    <select id="cbo-<?= ($cup = $articulo->id_producto) ?>" rel="<?= ($idx + 1) ?>" name="cbo-<?= $id ?>" onchange="change_opt(this)">

                        <?php foreach ($variantes as $attr) : ?>

                            <option value="<?= $attr->id_producto ?>" data-qty="<?= ($qty = $attr->cantidad_online) ?>" rel="<?= $precio ?>" <?= ($attr->id_producto == $cup) ? "selected" : "" ?>>

                                <?= $attr->variante . " ({$qty})" ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

            <?php endforeach; ?>

            <?php if ( $showStock || $esPromo ): ?>

                <p id="stock-disponible" style="margin:0;color:#0e76a8;">Disponibles: <?= $stock ?></p>

            <?php endif; ?>

            <div id="dv-actions" style="text-align: left;">

                <div class="input-group">

                    <?php

                    $data[] = $item->nombre;

                    $data[] = $item->marca;

                    $data[] = $item->peso;

                    $data[] = $sabor;

                    ?>

                    <div style="color: #000;padding:5px 0;font-size: 18px" class="hh-up-down">

                        <button class="btn-sb" type="button" id="btn-dwn-<?= $id ?>" onclick="up_down(this)" rel="1">-</button>

                        <span id="sp-cantidad-<?= $id ?>">1</span>

                        <button class="btn-sb" type="button" onclick="up_down(this)">+</button>

                    </div>

                    <div class="input-group-addon" style="text-align:right;font-size:22px;background: none;border:none;padding-top:0 ">

                        <?php 
                        
                            if ( $id == 4669 )
                            {
                                $precio = '19900';
                            }

                            if ( $id == 4678 )
                            {
                                $precio = '9900';
                            }
                            
                        ?>
                        
                        $ <span id="precio-<?= $id ?>"><?= number_format($precio, 2, ".", "") ?></span>


                    </div>

                </div>

                <div class="buttons_class"> 
                    

                    <button type="button" rel="<?= $id ?>" name="1" onclick="comprarMayoristas(this)">Comprar Ahora</button>

                    <button type="button" class="aa-button" rel="<?= $id ?>" onclick="comprarMayoristas(this)">Agregar <i class="fa fa-cart-plus"></i></button>

                    <?php if ( false ): ?>

                        <button type="button" onclick="location.href='<?= $href_url ?>'" id="aa-ver">Detalle</button>

                    <?php endif; ?>

                </div>

            </div>

        </form>

        <?php

        return ob_get_clean();

    }


    public static function drawArticulo(Articulo $articulo)

    {

        $data = null;

        $id = $articulo->id_producto;

        $href_url = self::siteUrl . "/articulo/{$id}";

        ob_start();

        ?>

        <div class="product-item" id="articulo-<?= $id ?>">

            <figure>

                <a class="aa-product-img" href="<?= $href_url ?>">

                    <img src="<?= $articulo->imagenes(true) ?>" alt="<?= $id ?>">

                </a>

                <figcaption>

                    <h4 class="aa-product-title"><a href="<?= $href_url ?>"><?= $articulo->nombre ?></a></h4>

                    <?= static::articuloOpcionMayoristas($articulo); ?>

                </figcaption>

            </figure>

        </div>

        <?php

        return ob_get_clean();

    }



    public function listadoPrecios()

    {

        $this->setPageTitle("Listado de Precios Mayorista");

        $html = '<iframe frameborder="0" style="width:100%;height:100vh" src="//docs.google.com/spreadsheets/d/e/2PACX-1vQWO0aO8RyYwFyF-UjJFeu4th56_VSdROCnG736ftUa0qXoFnh0V3EVxbZtrPfvOFKtoXFSA0MbpiuR/pubhtml?widget=true&amp;headers=false"></iframe>';

        $this->setParams(['hidden_header' => 1, 'hidden_footer' => 1]);

        $this->setBody($html, true);

    }



    public function listaPrecios()

    {

        list($tv, $from_modal) = explode("*", ($param = $_POST['mdl']) ?: $_GET['dt']);

        if ( in_array($tv, ["presupuesto", ""]) )

        {

            $tv = "publico";

        }

        #--

        if ( isset($_GET['dt']) && self::isXhrRequest() )

        {

            $where[] = "!`borrado` AND !`id_parent`";

            $where[] = "`id_categoria` <> '" . Categoria::ctgPromo . "'";

            $where[] = "`id_marca` NOT IN ('" . Categoria::mrkForrejeria . "')";

            #--

            if ( $id_marca = floatval($_POST['id_marca']) )

            {

                $where[] = "`id_marca` = '{$id_marca}'";

            }

            #--

            if ( ($categoria = floatval($_POST['id_categoria'])) )

            {

                $where[] = "`id_categoria` = '{$categoria}'";

            }

            #--

            if ( $txt = trim($_POST['txt']) )

            {

                $where[] = "`producto` LIKE '%{$txt}%'";

            }

            #--

            $query = Articulo::whereRaw(implode(" AND ", $where))->orderBy('id_marca')->orderBy("id_producto", "DESC");

            $result = $query->get();

            $data = null;

            foreach ($result as $index => $articulo)

            {

                if ( $result[$index - 1]->id_marca != $articulo->id_marca )

                {

                    $data .= "<tr><td colspan='2' style='text-align:center;background:#ffe116;font-weight:600'>{$articulo->marca}</td></tr>";

                }

                $option = "<a href='javascript:void(0)' onclick='seleccionar(\"{$articulo->codigo}&:id\")'><i class='fa fa-plus-circle'></i></a>";

                $data .= "<tr id='{$articulo->codigo}' rel='{$articulo->array_precios[$tv]}&'>";

                $data .= "<td>";

                $data .= $articulo->id_producto . " - " . $articulo->nombre;

                $data .= ". <b>{$articulo->marca}</b>. {$articulo->peso}";

                $childs = $articulo->hasAtributo()->where('borrado', 0)->get();

                $childs->push([

                    'cantidad_online' => $articulo->cantidad_online,

                    'articulo_sabor' => $articulo->articulo_sabor,

                    'stock' => $articulo->cantidad_array[$from_modal]

                ]);

                #--

                if ( $childs[0]['articulo_sabor'] )

                {

                    $data .= "<h4 style='margin:0;font-style:italic'>Sabores / Variantes</h4>";

                    foreach ($childs as $child)

                    {

                        if ( !$child['stock'] )

                        {

                            $child['stock'] = $child->cantidad_array[$from_modal];

                        }

                        #--

                        if ( !($sabor = $child['articulo_sabor']) )

                        {

                            continue;

                        }

                        #--

                        $data .= "<p style='margin:0'>";

                        $data .= "- {$sabor} (" . floatval($child[$from_modal ? 'stock' : 'cantidad_online']) . ") ";

                        if ( $from_modal )

                        {

                            $data .= preg_replace("#\:id#", $child->codigo, $option);

                        }

                        $data .= "</p>";

                    }

                }

                else

                {

                    $data .= " (" . ($from_modal ? floatval($articulo->cantidad_array[$from_modal]) : $articulo->cantidad_online) . ") ";

                    if ( $from_modal )

                    {

                        $data .= preg_replace("#\:id#", "", $option);

                    }

                }

                $data .= "</td>";

                if ( !$from_modal )

                {

                    $data .= "<td style='text-align:right;mso-number-format: \"0.00\"'>";

                    foreach ($articulo->array_precios as $label => $precio)

                    {

                        $data .= strtoupper($label . " <b>{$precio}</b>") . "<br/>";

                    }

                    $data .= "</td>";

                }

                $data .= "</tr>";

            }

            die($data);

        }

        $this->setPageTitle("Lista de Precios");

        #--

        $tipo = Categoria::tipoMarca;

        $cols[0] = "Art&iacute;culo";

        $input = "<input type='text' minlength='3' id='txt' class='form-control'>";

        if ( !$from_modal )

        {

            $cols[1] = "Precio.text-center";

            $tipo = Categoria::tipoCategoria;

            $where['activo'] = 1;

            $input = null;

            $page_body = "<h3 style='margin:0'>Dólar Productos Nacionales: $ {$this->config['precio_dolar']}</h3>";

            $page_body .= "<h3 style='margin:0'>Dólar Productos Importados: $ {$this->config['dolar_paralelo']}</h3>";

        }

        $where['tipo'] = $tipo;

        $control = $input;

        $control .= "<select id='id_{$tipo}' name='id_{$tipo}' class='form-control' required>";

        $control .= "<option value=''>" . ($tipo = mb_strtoupper($tipo)) . "</option>";

        foreach (Categoria::where($where)->orderBy("orden")->get() as $marca)

        {

            $control .= "<option value='{$marca->id_item}'>{$marca->titulo}</option>";

        }

        $control .= "</select>";

        $dataTable = new HDataTable();

        $dataTable->setColumns($cols);

        $dataTable->setDisableFunciones();

        $dataTable->setHideSearchBox();

        $dataTable->setHtmlControl($control);

        $tbody = "#table-body";

        if ( $from_modal )

        {

            $dataTable->setFixedHead();

            $tbody = ".table-fixed tbody";

        }

        $dataTable->setHideDateRange();

        $dataTable->setRows(null);

        $page_body .= $dataTable->drawTable();

        ob_start();

        ?>

        <script>

            //tblBody = document.getElementById('table-body');

            (tbodyTF = document.querySelector("[rel='table-body']")).id = "tb-<?=time()?>";

            filterGroupCtrls = Array.from(document.getElementById('filter-group').children);

            document.getElementById('btn-search').remove();

            params = {};



            function dt_draw_list(stop = false)

            {

                tbodyTF.innerHTML = emptyRow = "<tr><td colspan='2' align='center'><i class='fa fa-spin fa-spinner'></i></td></tr>";

                fetch("!<?=self::class?>/listaPrecios?dt=<?=$param?>", {

                    "method": "POST",

                    "body": new URLSearchParams(params)

                }).then(function (result) {

                    result.text().then(function (htmlRows) {

                        tbodyTF.innerHTML = htmlRows;

                        if ( !(num_rows = parseInt(tbodyTF.childElementCount)) )

                        {

                            tbodyTF.innerHTML = emptyRow.replace(/<i.+i>/gi, "Sin resultados")

                        }

                        document.getElementById('records_count').innerHTML = num_rows;

                    });

                });

            }



            filterGroupCtrls.forEach(function (ctrl) {

                if ( ["input", "select"].includes(ctrlName = ctrl.localName) )

                {

                    ctrl[`on${(ctrlName === "select") ? "change" : "keyup"}`] = function () {

                        valor = ctrl.value.trim();

                        if ( !valor || (valor.length >= parseInt(ctrl.getAttribute("minlength") || 1)) )

                        {

                            params[ctrl.id] = valor;

                            dt_draw_list();

                        }

                    };

                }

            });



            document.getElementById('a-reset').onclick = function (evt) {

                evt.preventDefault();

                filterGroupCtrls.forEach(function (ctrl) {

                    ctrl.value = "";

                    if ( Object.values(params)[0] )

                    {

                        params = {};

                        dt_draw_list();

                    }

                });

            };

            <?php if($from_modal): ?>

            tbodyTF.style["height"] = "330px";

            document.getElementById('modal-body').insertAdjacentHTML("beforeend", "<br/><button data-dismiss='modal' class='btn btn-default'>Cerrar</button>");

            <?php endif; ?>

            dt_draw_list(parseInt(<?=$from_modal?>));

        </script>

        <?php

        $page_body .= ob_get_clean();

        if ( $from_modal )

        {

            $this->setBlockModal($page_body);

            die;

        }

        $this->setParams(['hidden_header' => 1, 'hidden_footer' => 1]);

        $this->setBody($page_body, true);

    }

}