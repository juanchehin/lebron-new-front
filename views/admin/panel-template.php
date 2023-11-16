<style type="text/css">

    #user-row [role="button"] {

        color: #fff;

    }



    .page-header h2 {

        text-transform: uppercase;

        text-decoration: underline;

        color: #1B375C;

        font-style: italic;

    }



    .amount {

        font-family: Consolas, monospace;

        font-weight: 600;

        font-size: 14px;

        text-align: right;

    }



    mark {

        background: #FF0000;

        color: #fff;

        font-weight: 600;

        border-radius: 3px;

    }



    #notify-container {

        position: absolute;

        color: #fff;

    }



    #notify-container a {

        color: #fff;

        text-decoration: none;

    }



    li.dropdown:hover > .dropdown-menu {

        display: block;

    }

</style>

<div id="panel-header">

    <!-- main / large navbar -->

    <nav class="navbar navbar-default navbar-fixed-top bootstrap-admin-navbar bootstrap-admin-navbar-under-small" role="navigation">

        <div class="container">

            <div class="row">

                <div class="col-lg-12 text-right" id="user-row" style="margin-top:8px">

                    <div id="notify-container"></div>

                    <div class="dropdown" style="display: inline-block">

                        <?php if ( $admin_user->es_admin || ($admin_user->id_usuario == 25) || ($admin_user->id_usuario == 27) ): ?>

                            <a href="javascript:void(0)" style="cursor:pointer;color:#fff;margin-right:10px" onclick="get_modal_form({},'!AdminHome/configForm')"><i class="fa fa-cog"></i></a>

                        <?php endif; ?>

                        <a href="#" role="button" data-toggle="dropdown"><i class="fa fa-user-alt"></i> <?= $admin_user->nombre_apellido ?> <i class="caret"></i></a>

                        <ul class="dropdown-menu pull-right">

                            <li><a href="<?= $panel_uri ?>/perfil"><i class="fa fa-user-circle"></i> Datos</a></li>

                            <li><a href="<?= $panel_uri ?>/salir"><i class="fa fa-sign-out-alt"></i> Salir</a></li>

                        </ul>

                    </div>

                </div>

                <div class="col-lg-12">

                    <div class="navbar-header">

                        <a class="navbar-brand" title="Inicio" href="<?= $panel_uri ?>"><?= SITE_NAME ?></a>

                        <?php $show = $show_menu && $menu_principal[0]; ?>

                        <?php if ( $show ) : ?>

                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".main-navbar-collapse">

                                <span class="sr-only">Toggle navigation</span>

                                <span class="icon-bar"></span>

                                <span class="icon-bar"></span>

                                <span class="icon-bar"></span>

                            </button>

                        <?php endif; ?>

                    </div>

                    <?php if ( $show ) : ?>

                        <div class="collapse navbar-collapse main-navbar-collapse" style="padding-left: -90px;border-left-width: 0px;border-left-style: solid;margin-left: -130px;margin-right: -120px;">

                            <ul class="nav navbar-nav">

                                <?php foreach ($menu_principal as $item): ?>

                                    <li class="<?= $clase = $item['clase'] ?> <?= $item['activo'] ?>" id="<?= $item['id_item'] ?>">

                                        <a href="<?= $item['link'] ?>" rel="<?= $item['orden'] ?>" class="dropdown-toggle" <?php if ( $clase ) echo 'data-toggle="dropdown" role="button"' ?>>

                                            <i class="fa <?= $item['icono'] ?>"></i> <?= $item['label'] ?>

                                        </a>

                                        <?php if ( $submenu = $item['submenu'] ) : ?>

                                            <ul class="dropdown-menu">

                                                <?php foreach ($submenu as $child) : ?>

                                                    <li><a class="<?= $child['id_item'] ?>" href="<?= $child['link'] ?>" rel="<?= $child['orden'] ?>"><?= $child['label'] ?></a></li>

                                                <?php endforeach; ?>

                                            </ul>

                                        <?php endif; ?>

                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div><!-- /.navbar-collapse -->

                    <?php endif; ?>

                </div>

            </div>

        </div><!-- /.container -->

    </nav>

</div>

<style type="text/css">

    body {

        margin-left: 0 !important;

        padding-left: 0 !important;

    }



    #body-content {

        min-height: 70vh;

    }



    .required:before {

        content: "*";

        color: #FF0000;

        font-size: 12px;

        font-weight: 600;

        font-style: normal;

    }



    .modal {

        top: 6%;

    }

</style>

<div class="container">

    <!-- left, vertical navbar & content -->

    <div class="row">

        <!-- content -->

        <?= $block_modal ?>

        <div class="col-md-12">

            <div class="row">

                <div class="col-lg-12">

                    <div class="page-header text-center">

                        <h2><?= $header_title ?></h2>

                    </div>

                </div>

            </div>



            <div class="row">

                <div class="col-lg-12">

                    <div id="action-buttons">

                        <?= $boton_nuevo ?>

                        <?= $button_extra ?>

                    </div>

                    <div class="bootstrap-admin-panel-content text-muted" id="body-content">

                        <?= $_view_content ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- footer -->

<div class="navbar navbar-footer" id="footer" style="margin-bottom: 0">

    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <footer role="contentinfo">

                    <p class="left"><?= SITE_NAME ?></p>

                    <p class="right">&copy; 2017 - <?= date('Y') ?></p>

                </footer>

                <p>&nbsp;</p>

            </div>

        </div>

    </div>

</div>

<script>

    /*polling('!Polling/control', function (data) {

        let response = (data.content || {});

        if ( response["accion"] === "compra" )

        {

            if ( parseInt(< ?=$online?>) )

            {

                // Hay un cambio de estado

                get_rows();

                if ( (trow = document.getElementById(response["valor"])) )

                {

                    setTimeout(function () {

                        trow.classList.add("parpadea");

                    }, 600);

                }

            }

            SoundEffect.sendAlert();

            control_incidencias();

        }

    });*/

    <?php if ($admin_user): ?>

    function control_incidencias()

    {

        //reloj();

        const notifyContainer = document.getElementById('notify-container');

        notifyContainer.innerHTML = "<i class='fa fa-spin fa-spinner'></i>";

        fetch("!AdminVenta/getRows?count").then(function (result) {

            result.text().then(function ($res) {

                $link = `<a href='<?=$panel_uri?>/venta-online'><i class='fa fa-desktop'></i>&nbsp;${$res}</a>`;

                notifyContainer.innerHTML = $link;

            });

        });

    }



    control_incidencias();

    <?php endif; ?>

</script>