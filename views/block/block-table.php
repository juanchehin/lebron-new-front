<div class="">
    <div class="row">
        <?php if ( $search_box ) : ?>
            <div class="col-md-4" id="search-container">
                <input type="text" id="in-search" placeholder="" class="alphanumeric form-control"/>
            </div>
        <?php endif; ?>
        <div class="col-md-12">
            <br/>
            <table class="data-table" id="<?= CURRENT_CLASS ?>">
                <?php if ( $columns ) : ?>
                    <thead>
                    <tr>
                        <?php foreach ($columns as $column) : ?>
                            <th id="<?= $column['id'] ?>" class="<?= $column['class'] ?>"><?= $column['label'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                <?php endif; ?>
                <tbody id="table-body">
                <?= $table_rows ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    const table = '<?=CURRENT_CLASS?>';
    var values = {};
    var storage = getLS(table);
    if ( storage )
    {
        values = JSON.parse(storage);
        get_rows();
    }

    $('#in-search').keyup(function ()
    {
        var txt = this.value;
        if ( !txt || txt.length > 2 )
        {
            //$('tbody#table-body').html('<i class="fa fa-spin fa-spinner"></i>');
            values.search = txt;
            values.p = 1;
            get_rows()
        }
    }).val(values.search);

    function get_rows()
    {
        before_send();
        $.post('!<?=CURRENT_CLASS?>/getTable', values, function (result)
        {
            $('tbody#table-body').html(result);
            before_send();
        });

        setLS(table, JSON.stringify(values));
    }
    //get_rows();
</script>