<body style="background:#ddd;font-family: Helvetica, Arial, sans-serif">
<table width="700" border="0" align="center" cellspacing="0" bgcolor="#FFF">
    <tbody>
    <tr style="background:#eee">
        <td style="padding: 12px 25px 0;font-size:25px;border-bottom:1px solid #ddd;" valign="midle">
            <a href="<?= HTTP_HOST ?>" style="text-decoration:none">
                <img src="<?= HTTP_HOST ?>/assets/img/YaPago.png" width="130" alt="<?= SITE_NAME ?>"/>
            </a>
        </td>
    </tr>
    <tr>
        <td>
            <div style="padding:35px;min-height:70vh;font-size:0.97em;">
                <?= $body ?>
            </div>
        </td>
    </tr>
    <tr>
        <td style="padding:0 15px;background:#eee;line-height: 0" valign="top">
            <div style="padding:15px 0;width:100%;">
                <div style="width:25%;display: inline-block">
                    <?php $style = 'display:inline-block;vertical-align:middle;'; ?>
                    <span style="<?= $style ?>">
                    <a href=""><img src="<?= HTTP_HOST ?>/assets/img/social/rs1.png" alt="facebook"/></a>
                    </span>
                    <span style="<?= $style ?>">
                    <a href=""><img src="<?= HTTP_HOST ?>/assets/img/social/rs2.png" alt="twitter"/></a>
                    </span>
                </div>
                <div style="width:70%;display:inline-block;text-align:right;font-size:12px">
                    <?php $style .= 'padding-left:25px;text-align:left'; ?>
                    <div style="<?= $style ?>">
                        <a href="http://cace.org.ar">
                            <img src="<?= HTTP_HOST ?>/assets/img/logo_cace.png" width="95" alt="Sello CACE" title="Sello CACE"/>
                        </a>
                    </div>
                    <div style="<?= $style ?>">
                        <img src="<?= HTTP_HOST ?>/assets/img/logo_telecom.png" width="95" alt="Telecom"/>
                    </div>

                    <div style="<?= $style ?>">
                        <img src="<?= HTTP_HOST ?>/assets/img/Logopdp.png" width="95" alt="PDP"/>
                    </div>
                </div>
            </div>
            <div style="font-size:11px;padding-bottom:12px"><?= SITE_NAME ?> S.R.L&nbsp;&reg;&nbsp;2017</div>
        </td>
    </tr>
    </tbody>
</table>
</body>