<?php foreach ($permisos as $modulo => $permiso) : ?>
    <div class="form-group">
        <h5 class="modulo-titulo"><?= $modulo ?></h5>
        <div class="checks-permiso">
            <?php foreach ($permiso as $i => $item) : ?>
                <?php
                $item_name = "permiso[{$modulo}][{$item}]";
                $checked = in_array($item, $rol[$modulo]) ? "checked" : null;
                ?>
                <div class="check">
                    <label for="<?= ($id = $modulo . "_{$i}") ?>" class="btn btn-default">
                        <input type="checkbox" id="<?= $id ?>" name="<?= $item_name ?>" value="1" <?= $checked ?>/>
                        <?= ucwords(str_ireplace("_", " ", $item)) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
<style type="text/css">
    .modulo-titulo {
        margin-top: 0;
        border-bottom: 1px solid #eee;
        padding-bottom:3px;
        text-transform: uppercase;
        font-weight: 600;
        width: 100%;
    }

    .check {
        display: inline-block;
        padding-bottom: 5px;
    }

    .check .btn {
        padding: 3px 8px
    }

    .check input[type="checkbox"] {
        margin-top: 0;
    }
</style>
<script type="text/javascript">
    $('.check input[type="checkbox"]').click(function () {
        let id = this.getAttribute('name');
        let control = "<input type='hidden' name='" + id + "' value='1'>";
        if ( this.checked )
        {
            $('#frm-persona').append(control);
        }
        else
        {
            $('#frm-persona input[name="' + id + '"]').remove()
        }
    });

    $('.checks-permiso input[type="checkbox"]').each(function () {
        $(this).triggerHandler('click')
    });
</script>