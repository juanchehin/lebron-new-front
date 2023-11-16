<?php
$thead = "style='background:#eee;text-transform:uppercase'";
if ( true ) :
    ?>
    <div id="dv-print">
        <div style="width: 100%;display: inline-block;text-align: center">
            <h3>Listado al d&iacute;a <?= date("d/m/Y, H:i:s") ?></h3>
        </div>
        <table border="1" cellspacing="0" align="center" cellpadding="4" width="100%" style="font-size: 12px;">
            <thead>
            <tr>
                <th <?= $thead ?>>C&oacute;digo</th>
                <th <?= $thead ?>>Producto</th>
                <th <?= $thead ?>>Detalle</th>
                <th <?= $thead ?>>Cantidad</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articulos as $articulo) : ?>
                <tr>
                    <td align="center" style=""><?= $articulo->codigo ?></td>
                    <td><?= utf8_encode($articulo->nombre) ?></td>
                    <td><?= utf8_encode($articulo->detalle ?: " - ") ?></td>
                    <td><?php echo $articulo->cantidad_string ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <style>
        @media print {
            /*body {
                padding: 20px;
            }*/

            @page {
                margin: 15px 1cm;
            }

            #btn-print {
                display: none;
            }
        }

    </style>
<?php else : ?>
    <?php foreach ($articulos as $index => $articulo): ?>
        <div style="padding:5px 0;text-align:center;display: inline-block;width:24%;margin-right:0.25%; border: 1px dashed #ccc">
            <p style="font-size: 12px;margin:5px"><?= $nombre = $articulo->nombre ?></p>
            <div style="width: 94%;font-size: 12px">
                <?php
                $codigo = $articulo->codigo;
                $file = "media/barcode/{$codigo}.jpg";
                $image = preg_replace("#.*\,#", null, Barcode::getBarcode($codigo, true));
                file_put_contents($file, base64_decode($image));
                echo "<img src='{$file}' alt='{$nombre}'><br/>{$codigo}";
                ?>
            </div>
        </div>
        <?php
        if ( !(($index + 1) % 4) )
        {
            echo "<p></p>";
        }
        ?>
    <?php endforeach; ?>
<?php endif; ?>
