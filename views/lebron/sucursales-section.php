<div id="map-container" style="display: flex;justify-content: flex-start">
    <div id="dv-direcciones" style="width: 100%">
        <h3>Sucursales y Horarios</h3>
        <?php foreach ($sucursales as $sucursal) : ?>
            <div class="form-group">
                <i class="fa fa-map-marker-alt"></i>&nbsp;<?= $sucursal['direccion'] ?>
                <?php foreach ($sucursal['horario'] as $dia => $horario): ?>
                    <div class="hours"><?= $dia . " {$horario}" ?></div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ( $showMap ): ?>
        <div id="dv-map" style="width: 100%">
            <?php
            $map = new HMap();
            $map->noInputLocation();
            foreach ($sucursales as $location)
            {
                list($lat, $lng) = explode(",", $location['latlng']);
                $map->setMarkers($location['direccion'], $lat, $lng);
            }
            $parm['location'] = $map->drawMap();
            echo $map->drawMap()
            ?>
        </div>
    <?php endif; ?>
</div>
