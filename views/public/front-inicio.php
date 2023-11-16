<div class="content-top" style="padding-top: 0">
    <div id="dv-promos" class="owl-carousel">
        <?php foreach ($promociones as $promocion): ?>
            <a class="item" href="<?= $site_url . "/articulo/{$promocion->id_producto}" ?>">
                <img width="" src="<?= $promocion->imagenes(true) ?>" alt="1">
                <div class="promo-title">
                    <?= $promocion->nombre ?>
                    <h4>$ <?= HFunctions::formatPrice($promocion->ecommerce_precio) ?></h4>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <script>
        $("#dv-promos").owlCarousel({
            "items": 4,
            "loop": true,
            "margin": 10,
            "responsiveClass": true,
            "navigation": false,
            "pagination": false,
            "autoplay": true,
            "responsive": {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                800: {
                    items: 3,
                    nav: false
                },
                1000: {
                    items: 4,
                    nav: true,
                    loop: false
                }
            }
        });
    </script>
    <div class="row" id="dv-filter-group">
        <div class="col-md-6 form-group">
            <label for="ctg">Seleccionar Categor&iacute;a ▼</label>
            <select class="form-control" id="ctg" onchange="set_categoria(this)">
                <option value="">Categoría</option>
                <?php foreach ($categorias as $ctg): if ( !$ctg->hasArticulo->count() ) continue; ?>
                    <option value="<?= $ctg->id_item ?>" <?= (($_GET['ctg'] == $ctg->id_item) ? "selected" : null) ?>><?= $ctg->titulo ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 form-group">
            <?php if ( false ) : ?>
                <div class="input-group">
                    <input type="text" id="in-buscar" name="buscar" placeholder="Búsqueda" class="form-control">
                    <div class="input-group-addon" style="padding:0;">
                        <button type="button" id="btn-buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            <?php endif; ?>
            <label for="marca">Seleccionar Marca ▼</label>
            <select class="form-control" id="marca" rel="1" onchange="set_categoria(this)">
                <option value="">Buscar por Marca</option>
                <?php foreach ($marcas as $marca) : ?>
                    <?php
                    if ( !($count = $marca->hasArticuloMarca()->whereRaw("!`id_parent` AND `publicado` AND (`precio` OR `precio_online`)")->count()) )
                    {
                        continue;
                    }
                    ?>
                    <option value="<?= $marca->id_item ?>" <?= ($_GET['marca'] == $marca->id_item) ? "selected" : null ?>><?= $marca->titulo ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php //endif; ?>
    </div>
    <div id="list-title" style="border-top:1px solid #ddd;padding-top:5px"></div>
</div>
<style>
    #articulos-list {
        background-position: center;
        background-repeat: no-repeat;
        min-height: 60vh;
        background-image: url("static/images/back-<?=rand(1,3)?>.png");
        background-size: contain;
    }

    #ctg-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    [id*="spn-"] {
        background: #0D3349;
        color: #fff;
        margin-right: 12px;
        padding: 4px 12px;
        display: inline-block;
    }

    [id*="spn-"] span {
        cursor: pointer;
        background: #FF0000;
        border-radius: 6px;
        padding: 0 4px;
    }

    /*.flickity-viewport {
        min-height: 280px;
    }*/

    .promo-title h4 {
        margin: 5px;
        text-align: right;
        font-weight: bold;
    }

    .promo-title {
        position: absolute;
        background: #FF0000;
        opacity: .85;
        bottom: 12px;
        border-radius: 6px;
        padding: 5px;
        width: 90%;
        font-weight: 600;
        color: #fbff8e;
    }
</style>
<div class="classes_wrapper" style="padding-top:0">
    <div class="row" id="articulos-list">
        <?= $articulos_list ?>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <?php AdSense::setAutoAdsense("4555277788"); ?>
        </div>
        <div class="col-md-6 form-group">
            <?php AdSense::setAutoAdsense("6789956247"); ?>
        </div>
    </div>
</div>
<script>
    /*$('#dv-promociones').flickity({
        // options
        "cellAlign": 'left',
        "pageDots": false,
        "autoPlay": 3000,
        "wrapAround": true,
        //"draggable":false
    });*/
    caption = [];
    <?php
    unset($_GET['p']);
    foreach ($_GET as $key => $value): ?>
    select = document.getElementById('<?=$key?>');
    if ( parseInt(select.value) )
    {
        caption.push(`<h4 id="spn-<?=$key?>">${select[select.selectedIndex].innerHTML} <span onclick='quitar_tag("<?=$key?>")'>&times;</span></h4>`);
    }
    <?php endforeach; ?>
    document.getElementById('list-title').innerHTML = caption.join("");

    /*ctgList = document.getElementById('ctg');
    if ( parseInt(< ?=empty(array_values($_GET))?>) )
    {
        ctgList.value = 1;
        ctgList.onchange();
    }*/
</script>
