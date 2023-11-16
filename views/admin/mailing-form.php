<div class="">
    <form id="mailing-form" action="!<?= CURRENT_CLASS ?>/enviar">
        <div class="form-group">
            <label for="asunto">Asunto</label>
            <input type="text" name="asunto" id="asunto" class="form-control" value="<?= $mail->titulo ?>" autofocus required/>
        </div>
        <div class="form-group">
            <input type="hidden" name="id_mail" value="<?= $mail->id_contenido ?>"/>
            <label for="destinatario">Destinatario/s (e-mails separados por ;)</label>
            <textarea class="form-control" rows="4" id="destinatario" name="destinatario" required><?= $destinatario ?></textarea>
        </div>
        <div class="form-group">
            <label for="texto">Mensaje</label>
            <textarea name="mensaje" id="texto"><?= $mail->texto ?></textarea>
        </div>
        <div class="form-group">
            <?= HForm::inputCheck('check_enviar', true, null, "Y enviar"); ?><br/>
            <button type="submit" id="btn-send" class="btn btn-primary">Guardar</button>
            <a href="<?=CP_ADMIN?>/mailing" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>

<script type="text/javascript">
    CKEDITOR.replace("texto");

    $('input[name="check_enviar"]').click(function ()
    {
        const text = " y enviar";
        const btn = $('#btn-send');
        btn.html(btn.text().replace(text, ''));
        if ( this.checked )
        {
            btn.append(text);
        }
    }).triggerHandler('click');

    $('#mailing-form').submit(function (e)
    {
        e.preventDefault();
        const this_form = this;
        if ( $('input[name="check_enviar"]').is(':checked') )
        {
            jconfirm(function (res)
            {
                if(res)
                {
                    submit_form(this_form)
                }
            }, '¿Enviar este E-mail?');
            return;
        }
        submit_form(this_form)
    });

</script>