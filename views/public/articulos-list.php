<?php if ( !$articulos[0] ) : ?>
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-body text-center">
                <h4>No se encontraron art&iacute;culos</h4>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($articulos as $articulo): ?>
        <div class="col-md-6">
            <div class="class_box">
                <?php
                $data = null;
                $id = $articulo->id_producto;
                $href_url = "{$site_url}/articulo/{$id}";
                ?>
                <div class="col-md-5 class-image">
                    <a href="<?= $href_url ?>">
                        <img src="<?= $articulo->imagenes(true) ?>" alt="<?= $id ?>">
                    </a>
                </div>
                <div class="col-md-7">
                    <div id="dv-articulo-<?= $id ?>">
                        <h3 style="font-weight:bold;font-style:italic;min-height:53px;font-size:22px">
                            <a href="<?= $href_url ?>"><?= $articulo->nombre ?></a>
                        </h3>
                        <h5>Tipo: <?= $articulo->categoria ?: "-" ?></h5>
                        <h5>Marca: <?= $articulo->marca ?></h5>
                        <h5>Contenido: <?= $articulo->peso ?></h5>
                        <?php if ( !$articulo->hasAtributo->count() && $articulo->sabor != "OTRO" ) : ?>
                            <h5>Sabor: <?= $articulo->articulo_sabor ?></h5>
                        <?php endif; ?>
                    </div>
                    <?= FrontArticulo::articuloOpcion($articulo); ?>
                    <p class="clearfix"></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-md-12 text-center">
        <?= preg_replace("#\<a\s+#", "<a onclick='paginar(this)' ", $articulos->links()); ?>
    </div>
    <script src="static/public/js/JsArticulo.js?ver=<?= time() ?>" type="text/javascript"></script>
<?php endif; ?>
