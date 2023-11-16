<div class="navbar navbar-inverse set-radius-zero">

    <div class="container">

        <div class="navbar-header">

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

            </button>

            <a class="navbar-brand" target="_blank" href="./"><h2><?= SITE_NAME ?></h2></a>

        </div>



        <div id="user-dropdown" class="text-right">

            <?php if ( $admin_user ) : ?>

                <div class="dropdown">

                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?= $admin_user->nombre_apellido ?>

                        <span class="caret"></span></button>

                    <ul class="dropdown-menu">

                        <li><a href="<?= CP_ADMIN ?>/perfil"><i class="fa fa-user"></i> Perfil</a></li>

                        <li><a href="<?= CP_ADMIN ?>/logout"><i class="fa fa-sign-out"></i> Salir</a></li>

                    </ul>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<!-- LOGO HEADER END-->

<section class="menu-section">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div id="navbar-collapse" class="navbar-collapse collapse text-uppercase">

                    <ul id="menu-top" class="nav navbar-nav navbar-right">

                        <li><a href="<?= CP_ADMIN ?>/">Inicio</a></li>

                        <li><a href="<?= CP_ADMIN ?>/usuarios">Usuarios registrados</a></li>

                        <li><a href="<?= CP_ADMIN ?>/mailing">Mailing</a></li>

                        <?php if ( false ) : ?>

                            <li>

                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown">UI ELEMENTS <i class="fa fa-angle-down"></i></a>

                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">

                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="ui.html">UI ELEMENTS</a></li>

                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">EXAMPLE LINK</a></li>

                                </ul>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- MENU SECTION END-->

<div class="content-wrapper">

    <div class="container">

        <div class="row pad-botm">

            <div class="col-md-12">

                <h4 class="header-line"><?= $header_title ?></h4>

                <?php if ( $boton_nuevo && (CURRENT_METHOD == Router::DEFAULT_METHOD) ) : ?>

                    <div class="form-group">

                        <?= $boton_nuevo ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

        <div class="row">

            <?= $block_modal ?>

            <div class="col-md-12">

                <?= $_view_content ?>

            </div>

        </div>

    </div>

</div>

<!-- CONTENT-WRAPPER SECTION END-->

<section class="footer-section">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                &copy; 2017

            </div>

        </div>

    </div>

</section>

<script type="text/javascript">

    /*const dt_table = '< ?CURRENT_CLASS?>';

     // var dt_params = {}; //en js/admin/custom.js

     var dt_storage = getLS(dt_table);

     if ( dt_storage )

     {

     dt_params = JSON.parse(dt_storage);

     get_rows();

     }



     function get_rows()

     {

     before_send();

     $.post('!< ?CURRENT_CLASS?>/getRows', dt_params, function (result)

     {

     $('table.data-table tbody').html(result);

     before_send();

     });



     setLS(dt_table, JSON.stringify(dt_params));

     }*/



    function get_id_row(opt)

    {

        return opt.closest('.dt_row').getAttribute('id').replace(/\w+[\_,\-]/, "");

    }



    function set_estado(opt)

    {

        var dt_row = get_id_row(opt);

        var _data = {

            'data_id': dt_row,

            'estado': opt.checked ? 1 : 0,

            'attr': opt.getAttribute('name')

        };

        //console.log(_data)

        $.post('!<?=CURRENT_CLASS?>/setEstado', _data);

    }



    function get_form_modal(method, params)

    {
        
        const m = $(_modal);

        if ( !method )

        {

            method = "getModalForm";

        }

        before_send();

        $.getJSON('!<?=CURRENT_CLASS?>/' + method, params, function (data)

        {

            m.find('#modal-title').html(data.title);

            m.find('#modal-body').html(data.content);

            m.modal();

            before_send();

        });

    }

</script>