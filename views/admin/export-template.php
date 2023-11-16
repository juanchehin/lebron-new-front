<table width="100%" border="1" cellspacing="0">
    <thead>
    <tr>
        <?php foreach ($columns as $column) : ?>
            <?php list($label, $clase) = preg_split("#\.\w+#", $column) ?>
            <th style="text-transform:uppercase;background:#eee;font-weight:600"><?= $label ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?= $rows ?>
    </tbody>
</table>