<style>
.discount-section {
    margin-right: -1px; 
    display:flex;
    flex-wrap: wrap;
    justify-content:center;
}
</style>
<div class="row">
    <?php if ( $articulos[0] ): ?>
        <div class="col-md-12">
            <hr>
            <h2 class="text-center"><?= $titulo ?></h2>
        </div>
        <div class="discount-section">
            <?php foreach ($articulos as $articulo): ?>
                <div class="col-md-3 form-group">
                    <?php
                    if ( $articulo->precio_online < 0 )
                    {
                        echo "<span class='aa-badge aa-sale'>" . round($articulo->precio_online ?: -15) . " %!</span>";
                    }
                    echo FrontArticulo::drawArticulo($articulo)
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>