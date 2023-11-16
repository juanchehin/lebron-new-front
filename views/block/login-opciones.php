<style type="text/css">
    .fbk, .google {
        color: #fff;
        text-align: center;
        width: 100%;
        font-size: 17px;
        vertical-align: middle;
        cursor: pointer;
        padding: 5px 10px;
    }

    .fbk {
        background: #3C5A9A;
    }

    .google {
        background: #C83C26;
    }
</style>
<div class="row">
    <div class="col-md-6 form-group">
        <?php $text = "Acced&eacute; con  "; ?>
        <?php if ( !$fbk_red ) : ?>
            <div class="fbk" href="javascript:void(0)" onclick="facebook_login(facebook_callback)"><i class="fa fa-facebook"></i> <?= $text ?> facebook</div>
            <?php if ( $login ) echo "<h4 class='text-center'> - O - </h4>"; ?>
        <?php endif; ?>
        <!-- -->
    </div>
    <div class="col-md-6 form-group">
        <?php if ( !$google_red ) : ?>
            <div class="google" href="javascript:void(0)" onclick="google_login()"><i class="fa fa-google-plus"></i> <?= $text ?> Google</div>
        <?php endif; ?>
    </div>
</div>
<?php if ( !$google_red ) : ?>
    <script type="text/javascript" src='//apis.google.com/js/client:platform.js'></script>
<?php endif; ?>
<?php if ( !$fbk_red ) : ?>
    <script type="text/javascript" src="//connect.facebook.net/es_LA/sdk.js#xfbml=1&amp;version=v2.8&amp;appId=<?= FACEBOOK_APP_ID ?>"></script>
<?php endif; ?>
<script type="text/javascript">
    <?php if(!$fbk_red) : echo "\n"; ?>
    var facebook_option = $('.fbk');
    facebook_login = function (callback) {
        FB.login(function (response) {
            callback.call(this, response);
        }, {
            scope: 'public_profile,email',
            auth_type: 'rerequest'
        });
    };

    function facebook_callback(response)
    {
        if ( response.status === 'connected' )
        {
            FB.api('/me', {'fields': 'last_name,first_name,gender,email,birthday'}, function (data) {
                data.token = response.authResponse.accessToken;
                facebook_option.prepend("<i class='fa fa-spin fa-spinner'></i>");
                $.post('!Usuarios/socialAuth', data, function (response) {
                    if ( response.success )
                    {
                        facebook_option.remove();
                        return;
                    }

                    if ( response.error )
                    {
                        jdialog(response.error);
                        facebook_option.find('.fa-spin').remove();
                        return;
                    }
                    if ( response.location )
                    {
                        location.href = response.location;
                    }
                }, 'json');
            });
        }
    }
    <?php endif; ?>
    <?php if( !$google_red ): echo "\n"; ?>
    var google_option = $('.google');

    function google_init()
    {
        try
        {
            gapi.client.setApiKey('<?=GOOGLE_API_KEY?>');
            gapi.client.load('plus', 'v1', function () {
            });
        } catch ( e )
        {
            console.log(e.message);
        }
    }

    function google_login()
    {
        //google_init();
        var myParams = {
            'clientid': '<?=GOOGLE_CLIENT_ID?>',
            'cookiepolicy': 'single_host_origin',
            'callback': 'google_login_callback',
            'approvalprompt': 'auto',
            'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
        };
        gapi.auth.signIn(myParams);
    }

    var google_login_callback = function (result) {
        if ( result['status']['signed_in'] )
        {
            gapi.client.load('plus', 'v1', function () {
                gapi.client.plus.people.get({'userId': 'me'}).execute(function (resp) {
                    var email = '';
                    if ( resp['emails'] )
                    {
                        for (var i = 0; i < resp['emails'].length; i++)
                        {
                            if ( resp['emails'][i]['type'] === 'account' )
                            {
                                email = resp['emails'][i]['value'];
                            }
                        }
                    }

                    var result = {
                        'id': resp['id'],
                        'last_name': resp['name']['familyName'],
                        'first_name': resp['name']['givenName'],
                        'image': resp['image']['url'].replace(/\?.*/, ""),
                        'gender': resp['gender'],
                        'ocupacion': resp['occupation'],
                        'email': email,
                        'url': resp['url'],
                        'google': 1
                    };

                    google_option.prepend('<i class="fa fa-spin fa-spinner"></i>');
                    $.post("!Usuarios/socialAuth", result, function (res) {
                        <?php if(!$login): echo "\n"; ?>
                        if ( res.success )
                        {
                            jdialog("Su perfil de Google se ha vinculado con &eacute;xito.");
                            google_option.remove();
                        }
                        <?php endif; ?>
                        if ( res.error )
                        {
                            jdialog(res.error);
                            google_option.find(".fa-spin").remove();
                            return;
                        }
                        if ( res.location )
                        {
                            location.href = res.location;
                        }
                    }, 'json');
                });
            });
        }

    };
    <?php endif; ?>
</script>