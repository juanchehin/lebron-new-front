<div class="row" style="display: flex;justify-content:center">
    <div class="col-md-7">
        <?= $form ?>
    </div>
</div>
<script>
   /* ["genre", "dob"].forEach(function ($trg) {
        document.getElementById(`${$trg}-group`).remove();
    });*/
    document.getElementById('row-2').append(document.getElementById('email-group'));
</script>