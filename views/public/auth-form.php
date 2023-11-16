<div class="row" style="background: #f8f8f8;">
    <div class="col-md-12 text-center">
        <h2>Complet&aacute; tus Datos</h2>
        <p class="text-info">Para poder realizar la compra necesitamos tus datos para reservar tu orden y <br/>poder realizar el envío si corresponde</p>
        <br/>
    </div>
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <?php
        list($direccion, $piso, $depto, $cp) = explode("&", $user->direccion);
        $masDatos = "<div class='col-md-12 form-group'>";
        $masDatos .= "<label for='nombre'>Nombre <i class='required'></i></label>";
        $masDatos .= "<input type='text' id='nombre' name='nombre' maxlength='60' class='form-control' value='{$user->nombre}' required>";
        $masDatos .= "</div>";
        $masDatos .= "<div class='col-md-12 form-group'>";
        $masDatos .= "<label for='apellido'>Apellido <i class='required'></i></label>";
        $masDatos .= "<input type='text' id='apellido' name='apellido' maxlength='40' class='form-control' value='{$user->apellido}' required>";
        $masDatos .= "</div>";
        /*$masDatos .= "<div class='col-md-6 form-group'>";
        $masDatos .= "<label for='direccion'>Direcci&oacute;n</label>";
        $masDatos .= "<input type='text' id='direccion' name='direccion' oninput='input_direccion(this.value)' placeholder='Calle y Número' class='form-control' value='{$direccion}'>";
        $masDatos .= "</div>";
        $masDatos .= "<div class='col-md-6 form-group'>";
        $masDatos .= "<div class='input-group-addon'>";
        $masDatos .= "<label for='piso'>Piso</label>";
        $masDatos .= "<input type='tel' id='piso' name='piso' maxlength='3' class='form-control' value='{$piso}' disabled>";
        $masDatos .= "</div>";
        $masDatos .= "<div class='input-group-addon'>";
        $masDatos .= "<label for='depto'>Depto.</label>";
        $masDatos .= "<input type='text' id='depto' maxlength='4' name='depto' class='form-control' value='{$depto}' disabled>";
        $masDatos .= "</div>";
        $masDatos .= "<div class='input-group-addon'>";
        $masDatos .= "<label for='cp'>C&oacute;d. Postal</label>";
        $masDatos .= "<input type='tel' id='cp' name='cp' maxlength='5' class='form-control' value='{$cp}' disabled>";
        $masDatos .= "</div>";
        $masDatos .= "</div>";
        $masDatos .= "<div class='form-group col-md-6'>";
        $masDatos .= "<label for='telefono'>N&deg; Tel&eacute;fono</label>";
        $masDatos .= "<input type='tel' name='telefono' maxlength='12' id='telefono' class='form-control' value='{$user->celular}'>";
        $masDatos .= "</div>";*/
        ?>
        <div class="panel panel-success">
            <div class="panel-body" style="">
                <form action="!FrontUsuario/continuar?frm" autocomplete="off" id="frm-auth">
                    <div class="row">
                        <?php if ( !$user->id ): ?>
                            <div class="col-md-12 text-center" id="fbk-group">
                                <a href="javascript:void(0)" style="display: block" class="btn btn-primary" onclick="facebook_login(status_callback)">Acceder con Facebook</a>
                                <br/>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-12 form-group" id="email-group">
                            <label for="email">E-mail <i class="required"></i></label>
                            <?php if ( $correo = $user->email ): ?>
                                <h4><?= $correo ?></h4>
                            <?php else : ?>
                                <input type="email" id="email" name="email" maxlength="80" class="form-control" required>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12 form-group" id="dni-group">
                            <label for="dni">DNI <i class="required"></i></label>
                            <?php if ( $dni = $user->dni ): ?>
                                <h4><?= $dni ?></h4>
                            <?php else: ?>
                                <input type="tel" id="dni" name="dni" maxlength="8" class="form-control" required>
                            <?php endif; ?>
                        </div>
                        <div id='extra-data-container'>
                            <?php
                            if ( $idc = $user->id )
                            {
                                echo $masDatos;
                            }
                            ?>
                        </div>
                        <div class="col-md-12 form-group" style="text-align:right">
                            <p style="margin:0;font-size:12px;text-align: left"><i class="required"></i>&nbsp;Datos requeridos</p>
                            <button type="submit" name="sve" class="btn btn-default">Continuar</button>
                            <input type="hidden" id="ok" name="idc" value="<?= $idc ?>"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <h4>Procesamos tu pago con</h4>
        <img src="static/images/mercado-pago.jpg" width="100%" alt="mercadopago"/>
    </div>
</div>
<style scoped>
    input[type="email"], input[type="text"], select {
        text-transform: none;
    }

    h4, p {
        margin: 0;
    }

    .input-group-addon {
        background: none;
        border: none;
        padding: 6px 0;
        text-align: left;
    }
</style>
<script type="text/javascript">
    document.getElementById('frm-auth').onsubmit = function (evt) {
        evt.preventDefault();
        let tform = this;
        submit_form(tform, function (res) {
            after_submit(res);
        });
    };

    function input_direccion($this)
    {
        ["piso", "depto", "cp"].forEach(function ($id) {
            $item = document.getElementById($id);
            if ( $this )
            {
                $item.setAttribute("required", true);
                $item.removeAttribute("disabled");
                return;
            }
            $item.removeAttribute("required");
            $item.setAttribute("disabled", true);
        });
    }

    function after_submit(res)
    {
        if ( typeof res !== "object" )
        {
            return;
        }
        if ( res.ok )
        {
            if ( res.dni && (inputDni = document.getElementById('dni')) )
            {
                inputDni.remove();
                document.getElementById('dni-group').insertAdjacentHTML("beforeend", `<h4>${res["dni"]}</h4>`);
            }
            if ( res.correo && (inputCorreo = document.getElementById('email')) )
            {
                inputCorreo.remove();
                document.getElementById('email-group').insertAdjacentHTML("beforeend", `<h4>${res["correo"]}</h4>`);
                document.getElementById('fbk-group').remove();
            }
            document.getElementById('extra-data-container').innerHTML = `<?=addslashes($masDatos)?>`;
            delete res["correo"];
            delete res["dni"];
            for (let i in res)
            {
                document.getElementById(i).value = res[i];
                if ( i === "direccion" )
                {
                    input_direccion(res[i]);
                }
            }
            document.getElementById('nombre').focus();
            sessionStorage.setItem(sesAuth, true);
        }
        if ( res.location )
        {
            location.href = res["location"];
        }
    }

    //input_direccion("< ?=$direccion?>");
</script>