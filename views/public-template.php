<?php if ( !DEVELOPMENT ) : ?>

<?php endif; ?>
<div class="preloader">
    <img src="static/img/loader.gif" alt="Preloader image">
</div>
<nav class="navbar" id="navbar">
    <div class="container">
        <?php if ( $logged_user ) : ?>
            <p class="text-right" style="line-height:0;margin-right: 13px;">
                <a href="<?= HTTP_HOST ?>/gestion" style="color:#0B8CCE"><i class="fa fa-user"></i> <?= $logged_user->nombre_pila ?></a>
            </p>
        <?php endif; ?>
        <!-- Brand and toggle get grouped for better mobile display -->
        <?php if ( $show_header ) : ?>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./"><img src="<?= $logo_src ?>" alt="<?= SITE_NAME ?>"></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right main-nav">
                    <?php foreach ($menu_principal as $link => $label) : ?>
                        <li><a href="<?= $link ?>"><?= $label ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
<div class="" id="main-content">
    <?= $_main_content ?>
</div>
<footer id="footer">
    <div class="container">
        <div class="row text-center-mobile">
            <div class="col-sm-8">
                &copy; 2017
            </div>
            <div class="col-sm-4 text-right text-center-mobile">
                <ul class="social-footer">
                    <li><a href="javascript:void(0)"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="javascript:void(0)"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="javascript:void(0)"><i class="fa fa-google-plus"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<script type="text/javascript">
    <?php if( $logged_user ) : ?>
    setInterval(function () {
        $.getJSON('!Gestion/sessionControl', function (res) {
            if ( res.location )
            {
                location.href = res.location + '?timeout';
            }
        });
    }, 60000);
    <?php endif; ?>
</script>