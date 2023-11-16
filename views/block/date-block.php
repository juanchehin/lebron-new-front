<?php

$year = date('Y');
?>
<div class="row">
    <div class="col-md-6">
        <label for="s-anio">Fecha</label>
        <select name="s-anio" id="s-anio">
            <option value="">A&ntilde;o</option>
            <?php for ($a = $year - 17; $a > ($year - 75); $a--) : ?>
                <option><?= $a ?></option>
            <?php endfor; ?>
        </select>

        <select name="s-mes" id="s-mes" disabled>
            <option value="">Mes</option>
            <?php for ($m = 1; $m < 13; $m++) : ?>
                <option><?= str_pad($m, 2, 0, STR_PAD_LEFT) ?></option>
            <?php endfor; ?>
        </select>

        <select name="s-dia" id="s-dia" disabled>
            <option value="">D&iacute;a</option>
        </select>
    </div>
</div>

<script type="text/javascript">
    const cbo_mes = $('#s-mes');
    const cbo_anio = $('#s-anio');
    const cbo_dia = $('#s-dia');
    cbo_anio.change(function ()
    {
        cbo_mes.attr('disabled', true);
        if ( this.value )
        {
            cbo_mes.attr({'disabled': false, 'required': true});
        }
        cbo_mes.trigger('change');
    });

    cbo_mes.change(function ()
    {
        var dias = 0;
        var options = "<option value=''>D&iacute;a</option>";
        cbo_dia.attr('disabled', true).val("");
        if ( this.value && cbo_anio.val() )
        {
            dias = daysInMonth(this.value, cbo_anio.val());
            for (var dia = 1; dia <= dias; dia++)
            {
                options += '<option>' + pad(dia, 2) + '</option>';
            }
            cbo_dia.attr({'disabled': false, 'required': true}).html(options);
        }
    });
</script>