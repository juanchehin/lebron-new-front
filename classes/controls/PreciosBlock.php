<?php


class PreciosBlock
{
    private $mayorista;
    private $utilidad;
    private $is_parent = false;

    public function setMayorista($valor, $unidad)
    {
        $this->mayorista['valor'] = $valor;
        $this->mayorista['unidad'] = $unidad;
        return;
    }

    public function setUtilidad($valor, $unidad)
    {
        $this->utilidad['valor'] = $valor;
        $this->utilidad['unidad'] = $unidad;
        return;
    }

    public function isParent($bool)
    {
        $this->is_parent = !$bool;
        return;
    }

    public function draw()
    {
        $monedas = MainModel::$_monedas;
        $css0 = 'text-align:left;background:none;border:0;padding:3px 0';
        ob_start();
        ?>
        <div class="row" id="precios-group">
            <div class="col-md-6 form-group" id="first-group">
                <label for="precio">Precio Mayorista <i class="required"></i></label>
                <?php if ( $this->is_parent ) : ?>
                    <div class="input-group-addon" style="<?= $css0 ?>;">
                        <input type="tel" name="precio_compra[]" id="precio" class="form-control" value="<?= floatval($this->mayorista['valor']) ?>" required>
                    </div>
                    <div class="input-group-addon" style="<?= $css0 ?>;">
                        <select name="precio_compra[]" id="moneda" class="form-control" required>
                            <option value="">Unidad</option>
                            <?php foreach ($monedas as $moneda) : ?>
                                <option><?= $moneda ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else : ?>
                    <h4 style="margin:4px 0"><?= $this->mayorista['valor'] . " " . $this->mayorista['unidad'] ?></h4>
                <?php endif; ?>
            </div>
            <div class="col-md-6 form-group" id="second-group">
                <label for="utilidad">Utilidad </label>
                <?php if ( $this->is_parent ): ?>
                    <div class="input-group-addon" style="<?= $css0 ?>;">
                        <input type="tel" name="utilidad[]" id="utilidad" class="form-control" value="<?= floatval($this->utilidad['valor']) ?>">
                    </div>
                    <div class="input-group-addon" style="<?= $css0 ?>">
                        <select name="utilidad[]" id="utilidad-moneda" class="form-control">
                            <option value="">Unidad</option>
                            <?php foreach ($monedas as $moneda) : ?>
                                <option><?= $moneda ?></option>
                            <?php endforeach; ?>
                            <option>%</option>
                        </select>
                    </div>
                <?php else : ?>
                    <h4 style="margin:4px 0"><?= $this->utilidad['valor'] . " " . $this->utilidad['unidad'] ?></h4>
                <?php endif; ?>
            </div>
            <?php if ( false ): ?>
                <div class="form-group">
                    <label for="precio">Precio <i class="required"></i></label>
                    <input type="tel" name="precio" id="precio" class="form-control" value="<?= $data->precio ?>" required>
                </div>
            <?php endif; ?>
        </div>
        <script>
            <?php if($this->is_parent): ?>
            $('#precio, #utilidad').decimal(".");
            document.getElementById('moneda').value = "<?=$this->mayorista['unidad']?>";
            document.getElementById('utilidad-moneda').value = "<?=$this->utilidad['unidad']?>";
            <?php endif; ?>
        </script>
        <?php
        return ob_get_clean();
    }
}