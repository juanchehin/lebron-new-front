<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="!AdminProducto/productoForm" id="producto-frm" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="codigo">C&oacute;digo <i class="required"></i></label>
                            <?php if ( false ) : ?>
                                <div class="form-control"><?= $articulo->codigo ?></div>
                            <?php else : ?>
                                <div style="margin-left:40px;display: inline-block">
                                    <input type="checkbox" id="auto" name="auto" style="margin-top:0">
                                    <label class="small" for="auto" style="line-height:0">Generar</label>
                                </div>
                                <input type="tel" name="codigo" class="form-control" id="codigo" value="<?= $articulo->codigo ?>" required autofocus>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nombre">Producto <i class="required"></i></label>
                            <input type="text" name="nombre" class="form-control" id="nombre" value="<?= $articulo->producto ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="marca">Marca</label>
                            <?php if ( false ): ?>
                                <input type="text" class="form-control" id="marca" name="marca" value="<?= $articulo->marca ?>">
                            <?php endif; ?>
                            <?= $selectMarca ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="peso">Contenido/Medida <i class="required"></i></label>
                            <div class="input-group">
                                <input type="text" id="peso" maxlength="4" name="peso" class="form-control" value="<?= $articulo->int_peso ?>"/>
                                <span class="input-group-addon">
                                    <select name="unidad" style="height:20px" id="">
                                        <?php foreach ($unidades as $unidad) : ?>
                                            <option <?= ($unidad == $articulo->unidad) ? "selected" : null ?>><?= $unidad ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="sabor">Variante</label>
                            <select id="sabor" name="sabor" minlength="0" class="form-control">
                                <option value="">Seleccionar</option>
                                <?php foreach ($sabores as $res) : ?>
                                    <option><?= $res->articulo_sabor ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <?php if ( true ): ?>
                            <div class="form-group col-md-3">
                                <label for="precio">Precio: <i class="required"></i></label>
                                <input type="tel" name="precio" id="precio" class="form-control" value="<?= floatval($articulo->precio) ?>" required>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-3 form-group">
                            <label for="stock-alerta">Stock M&iacute;nimo <i class="required"></i></label>
                            <input type="tel" class="form-control" name="alerta" id="stock-alerta" value="<?= $articulo->stock_alerta ?: $alerta ?>" required>
                        </div>
                        <?php if ( !$articulo->id_producto ) : ?>
                            <div class="col-md-12 form-group">
                                <label for="">Cantidades por local</label>
                                <div class="row">
                                    <?php $cnt_deposito = (count($locales) + 1 - count(Local::$_puntosVenta)); ?>
                                    <?php foreach ($locales as $key => $local) : ?>
                                        <?php
                                        if ( in_array($key, Local::$_puntosVenta) )
                                        {
                                            continue;
                                        }
                                        ?>
                                        <div class="col-md-<?= ($cnt_deposito < 5) ? round(12 / $cnt_deposito) : 3 ?>" style="text-align:left;background:none;border:none;">
                                            <input type="tel" name="stock[<?= $key ?>]" class="form-control" value="<?= $articulo->cantidad_array[$key] ?>" placeholder="<?= $local ?>">
                                            <p class="small"><?= $local ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ( false ): ?>
                            <div class="col-md-12 form-group">
                                <label for="texto">Descripci&oacute;n</label>
                                <textarea name="texto" id="texto" class="form-control"><?= $articulo->texto ?></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group text-right">
                        <p class="small"><i class="required"></i> Datos requeridos</p>
                        <?php if ( $articulo->id_producto ) : ?>
                            <input type="hidden" name="id_producto" value="<?= $articulo->id_producto ?>"/>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="<?= $panel_uri ?>/productos" class="btn btn-default">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //$('#peso').decimal();
    /* new Jodit("#texto", {
         "uploader": {
             "insertImageAsBase64URI": true
         },
         "language": "es",
         "direction": "ltr",
         "askBeforePasteHTML": false,
         "askBeforePasteFromWord": false,
         "buttons": ",,,,,,,,,,,,,,,,fontsize,paragraph,|,file,video,link,|,align,undo,redo,\n,cut,hr,eraser,copyformat,|,symbol,selectall,print"
     });*/

    $('#producto-frm').submit(function (e) {
        e.preventDefault();
        submit_form(this);
    });
    $('#precio').decimal('.');
    selectSabor = document.getElementById('sabor');
    selectSabor.value = "<?=$articulo->articulo_sabor?>";
    $(selectSabor).selectar();

    $('#auto').click(function () {
        var codigo = $('#codigo');
        if ( this.checked )
        {
            codigo.val(new Date().getTime());
            return;
        }
        codigo.val("");
    });

    selectMarca = document.getElementById('id_marca');
    selectMarca.setAttribute('minLength', "0");
    selectMarca.value = "<?=$articulo->id_marca?>";
    $(selectMarca).selectar();
</script>