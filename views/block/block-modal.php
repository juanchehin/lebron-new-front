<div id="<?= $modal = "modal-" . uniqid() ?>" class="modal fade" role="dialog" rel="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title"><?= $modal_title ?></h4>
            </div>
            <div class="modal-body" id="modal-body"><?= $modal_body ?></div>
            <?php if ( $modal_close ) : ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    //params : json key:value type
    function get_form_modal(params, method)
    {
        var html_modal = $('#<?=$modal?>');
        if ( !method )
        {
            method = "modalForm";
        }
        before_send();
        $.post('!<?=CURRENT_CLASS?>/' + method, params, function (response) {
            html_modal.find('#modal-title').html(response.title);
            html_modal.find('#modal-body').html(response.body);
            html_modal.modal();
            before_send();
        }, 'json');
    }
</script>