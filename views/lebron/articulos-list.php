<div class="aa-product-catg">
    <?php foreach ($articulos as $articulo): ?>
        <div class="col-md-4 form-group">
            <?=FrontArticulo::drawArticulo($articulo)?>
        </div>
    <?php endforeach; ?>
</div>
<div class="text-center" style="margin-top: 15px">
    <?= preg_replace("#\<a\s+#", "<a onclick='paginar(this)' ", $articulos->links()); ?>
</div>