<?php

//ini_set("display_errors", "On");
class AdminMain extends MainTemplate
{
    const ADMIN_VIEWS = 'admin/';
    const ADMIN_TEMPLATE = self::ADMIN_VIEWS . 'panel-template';
    const PANEL_URI = self::appUrl . CP_ADMIN;
    const sysUrl = self::PANEL_URI;
    const idLocalSession = "ses_id_local";
    const sesReporte = "ses_reporte";
    protected $header_title;
    protected $admin_user;
    protected $es_admin;
    protected $x_page;
    protected $menu_principal;
    protected $current_item;
    protected $modulo;
    protected $id_local_ses;
    protected static $_IP_PERMITIDA = array(192, 143, 190);
    protected static $_adminCp;

    #--
    public function __construct()
    {
        parent::__construct();
        //$this->template = self::ADMIN_TEMPLATE;
        $this->setViewSubdir($subdir = "admin");
        $this->template = "{$subdir}/panel-template";
        $this->addStyle("static/panel/css/bootstrap-admin-theme.css?ver=" . time());
        $this->addScript("static/sound/SoundControl.js");
        $this->addScript("static/panel/js/custom.js", true);
        $this->_prepare();
    }

    private function _prepare()
    {
        $this->admin_user = Usuario::getLoggedUser(Usuario::PANEL_SESSION);
        if ( !$this->admin_user )
        {
            $this->loginRedirect();
        }
        $this->es_admin = $this->admin_user->es_admin;
        static::$_adminCp = $this->es_admin;
        $this->x_page = $this->config['por_pagina'] ?: 30;
        $this->id_local_ses = $this->admin_user->id_local ?: HSession::getSession(self::idLocalSession);
        $values = array(
            'panel_uri' => self::sysUrl,
            'id_local_ses' => $this->id_local_ses,
            'admin_user' => $this->admin_user,
            'es_admin' => $this->es_admin,
            'show_menu' => true // ($this->admin_user->id_usuario == 2)
        );
        $this->setParams($values);
        $this->setBlockModal();
        #--
        // $anioMes = date('Y-m');
        // $diasMes = HDate::daysInMonth() - 1;
        // $dolar = MainModel::getInfoDolar();
        // if ( !$dolar[$anioMes] || $diasMes == date('d') )
        // {
        //     $apiDolar = new CurlClass("https://api-dolar-argentina.herokuapp.com/api");
        //     $apiDolar->setPath("dolarblue");
        //     $res = $apiDolar->callAPI();
        //     $periodo = substr(str_ireplace("/", "-", $res['fecha']), 0, 7);
        //     if ( $value = $res['venta'] )
        //     {
        //         $dolar[$periodo] = $value;
        //         file_put_contents("conf/dolar.json", json_encode($dolar));
        //     }
        // }
        //$this->setBotonNuevo();
    }

    protected function setPageTitle($value = null)
    {
        $title[] = SITE_NAME;
        $title[] = $value;
        $this->setPageHeader($value);
        return parent::setPageTitle(implode(" | ", array_filter($title)));
    }

    protected function setBotonNuevo($label = "Nuevo", $ref = null, $btn_extra = null)
    {
        if ( !$ref )
        {
            $ref = $_SERVER['REQUEST_URI'] . "/nuevo";
        }
        $boton_nuevo = null;
        if ( $label )
        {
            $boton_nuevo = "<a href='{$ref}' id='aa-nuevo' class='btn btn-success'><i class='fa fa-plus'></i> {$label}</a>";
        }
        $boton_nuevo .= $btn_extra;

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function setBotonNuevoCliente($label = "Nuevo")
    {
        $ref = $_SERVER['REQUEST_URI'] . "/modal_alta_cliente";
        
        if ( $label )
        {
            $boton_nuevo = "<a href='{$ref}' id='aa-nuevo' class='btn btn-success'><i class='fa fa-plus'></i> {$label}</a>";
        }

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function setBotonesIva($label = "Nuevo", $ref = null, $btn_extra = null)
    {
        if ( !$ref )
        {
            $ref = $_SERVER['REQUEST_URI'] . "/nuevo";
        }
        $boton_nuevo = null;
        if ( $label )
        {
            // $boton_nuevo = "<a href='{$ref}' id='aa-nueva-iva-compra' class='btn btn-success'><i class='fa fa-plus'></i> Iva compra</a>";
            // $boton_nuevo .= "<a href='{$ref}' id='aa-nueva-iva-venta' class='btn btn-success'><i class='fa fa-plus'></i> Iva venta</a>";

        }
        $ref_iva_compra = $_SERVER['REQUEST_URI'] . "/modalFormIvaCompra";
        $ref_iva_venta = $_SERVER['REQUEST_URI'] . "/modalFormIvaVenta";

        $boton_nuevo .= $btn_extra;
        $boton_nuevo .= "<a href='{$ref_iva_compra}' id='aa-alta-iva-compra' class='btn btn-success'><i class='fa fa-plus-square'></i> Iva compra</a>";
        $boton_nuevo .= "  <a href='{$ref_iva_venta}' id='aa-alta-iva-venta' class='btn btn-success'><i class='fa fa-minus-square'></i> Iva venta</a>";

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function setBotonNuevoRedirectNuevaVenta()
    {
        $ref = $_SERVER['REQUEST_URI'] . "/nuevo";

        $boton_nuevo = "<a href='{$ref}' id='' class='btn btn-success'><i class='fa fa-plus'></i> Nueva venta quimico</a>";
    
        // $boton_nuevo .= $btn_extra;

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function setBotonesAdministracion($label = "Nuevo", $ref = null, $btn_extra = null)
    {
        if ( !$ref )
        {
            $ref = $_SERVER['REQUEST_URI'] . "/nuevo";
        }
        $boton_nuevo = null;
        if ( $label )
        {
            $boton_nuevo = "<a href='{$ref}' id='aa-nuevo' class='btn btn-success'><i class='fa fa-plus'></i> {$label}</a>";
        }
        $ref_postulantes = $_SERVER['REQUEST_URI'] . "/postulantes";
        $ref_mayoristas = $_SERVER['REQUEST_URI'] . "/clientes-mayoristas";

        $boton_nuevo .= $btn_extra;
        $boton_nuevo .= "<a href='{$ref_postulantes}' id='aa-nuevo' class='btn btn-success'><i class='fa fa-address-book'></i> Postulantes</a>";
        $boton_nuevo .= "  <a href='{$ref_mayoristas}' class='btn btn-success'><i class='fa fa-address-card'></i> Mayoristas</a>";

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function setBotonesInversores($label = "Nuevo", $ref = null, $btn_extra = null)
    {
        if ( !$ref )
        {
            $ref = $_SERVER['REQUEST_URI'] . "/nuevo";
        }
        $boton_nuevo = null;
        if ( $label )
        {
            $boton_nuevo = "<a href='{$ref}' id='aa-nuevo' class='btn btn-success'><i class='fa fa-plus'></i> {$label}</a>";
        }
        // $ref_postulantes = $_SERVER['REQUEST_URI'] . "/simulador";

        $boton_nuevo .= $btn_extra;
        $boton_nuevo .= "<a href='javascript:void(0)' class='btn btn-success' onclick='get_modal_form_simulador_inversion()'><i class='fa fa-calculator'></i>Simulador</a>";

        $boton_nuevo .= $btn_extra;
        $boton_nuevo .= "<a href='javascript:void(0)' class='btn btn-success' onclick='get_modal_form_simulador_inversion_compuesta()'><i class='fa fa-calculator'></i>Simulador - Compuesto</a>";

        // $boton_nuevo .= "<a id='simulador-costos' class='btn btn-success'><i class='fa fa-calculator'></i>Simulador</a>";

        $this->setParams('boton_nuevo', $boton_nuevo);
    }

    protected function reporte($data = null)
    {
        if ( $data )
        {
            HSession::setSession(self::sesReporte, serialize($data));
        }
        return unserialize(HSession::getSession(self::sesReporte));
    }

    protected function setItemSeleccionado($value, $modulo = null)
    {
        $this->current_item = $value;
        $this->modulo = $modulo ?: $value;
        $this->menuPrincipalPanel();
        return $this;
    }

    public function setLocal($local = null)
    {
        $id_local = intval($local ?: $_POST['id_local']);
        HSession::setSession(self::idLocalSession, $id_local);
    }

    protected function _selectLocal($tipo = null)
    {
        $selectLocal = "<select id='local' name='id_local' class='form-control'>";
        $selectLocal .= "<option value=''>Local</option>";
        foreach (Local::$_LOCALES as $id => $nombre)
        {
            if ( $tipo == 1 && Local::esDeposito($id) )
            {
                continue;
            }
            elseif ( $tipo > 1 && !Local::esDeposito($id) )
            {
                continue;
            }
            $selectLocal .= "<option value='{$id}'>{$nombre}</option>";
        }
        $selectLocal .= "</select>";

        return $selectLocal;
    }

    private function menuPrincipalPanel()
    {
        $this->menu_principal = $menu_parent = array();
        $main_level = MenuPanel::getItemsParent();
        $titulo_seccion = $icono_seccion = null;
        foreach ($main_level as $item)
        {
            $menu = array(
                'id_item' => $item['id_item'],
                'label' => $item['label'],
                'link' => self::sysUrl . "/" . $item['enlace'],
                'icono' => $item['icono'],
                'clase' => null,
                'activo' => null,
                'orden' => $item['orden'],
                'submenu' => array()
            );
            $menu_parent[] = $menu;

            if ( $item['id_item'] == $this->current_item )
            {
                $menu['activo'] = 'active';
                $icono_seccion = $item['icono'];
                $titulo_seccion = $item['label'];
            }

            $next_level = MenuPanel::getItemsChild($item['id_item']);
            if ( $next_level[0] )
            {
                $menu['link'] = 'javascript:void(0)';
                $menu['clase'] = 'dropdown';
                $menu['label'] .= " <b class='caret'></b>";
                foreach ($next_level as $subitem)
                {
                    $submenu = array(
                        'id_item' => $subitem['id_item'],
                        'label' => $subitem['label'],
                        'link' => $subitem['enlace'] ? self::sysUrl . "/" . $subitem['enlace'] : "javascript:void(0)",
                        'icono' => $subitem['icono'],
                        'orden' => $subitem['orden']
                    );

                    if ( $subitem['id_item'] == $this->current_item )
                    {
                        $menu['activo'] = 'active';
                        $titulo_seccion = $subitem['label'];
                        $icono_seccion = $subitem['icono'];
                    }

                    $menu['submenu'][] = $submenu;
                }
            }

            $this->menu_principal[] = $menu;
        }
        //HArray::varDump($this->menu_principal);
        $params = array(
            'menu_principal' => $this->menu_principal,
            'titulo_seccion' => $titulo_seccion,
            'icono_seccion' => $icono_seccion,
            'menu_parent' => $menu_parent,
            'log' => $this->config,
        );
        $this->setParams($params);
    }

    protected function setPageHeader($value = null)
    {
        $this->header_title = $value;
        $this->setParams('header_title', $this->header_title);
        return $this;
    }

    /*protected function setBody($view = null, $is_html = false)
    {
        if ( !$is_html )
        {
            $view = self::ADMIN_VIEWS . $view;
        }
        parent::setBody($view, $is_html);
    }*/

    protected function controlPermiso($accion = null, $salir = true)
    {
        $usuario_permisos = $this->admin_user->permisos;
        $tiene_acceso = in_array($accion, $usuario_permisos[$this->current_item]);
        if ( $salir && !$tiene_acceso )
        {
            $url = $_SERVER['HTTP_REFERER'] ?: self::sysUrl;
            if ( self::isXhrRequest() )
            {
                HArray::jsonError("No puedes realizar esta acción");
            }
            #--
            Router::redirect($url);
            exit;
        }
        return $tiene_acceso;
    }

    public function logout()
    {
        Usuario::logoutUser(Usuario::PANEL_SESSION);
        Router::redirect(self::sysUrl);
        //$this->loginRedirect();
    }

    protected function loginRedirect()
    {
        $this->setPageTitle();
        if ( DEVELOPMENT )
        {
            $usuario = "admincp";
            $contrasena = "admincp";
        }
        ob_start();
        ?>
        <div class='container' style="min-height:71vh">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h1 style="padding-bottom:6%;text-align: center;font-style: italic"><?= SITE_NAME ?></h1>
                    <div class="panel panel-default">
                        <div class="panel-heading text-center" style="font-style:italic;font-size: 20px">Acceso</div>
                        <div class="panel-body">
                            <form action='!AdminAcceso/ingresar' id='loginForm' autocomplete='off' onsubmit="return submit_form(this);">
                                <div class="form-group">
                                    <label for="u">Usuario</label>
                                    <input type='text' id="u" class='form-control' name='username' value="<?= $usuario ?>" required autofocus/>
                                </div>
                                <?php //if ( !DEVELOPMENT ):
                                ?>
                                <div class="form-group">
                                    <label for="p">Contrase&ntilde;a</label>
                                    <input type='password' id="p" class='form-control' value="<?= $contrasena ?>" name='password' required/>
                                </div>
                                <?php //endif;
                                ?>
                                <footer class="form-group text-right">
                                    <button class="btn btn-primary" type='submit' id="submit">Acceder</button>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            document.body.classList.add("bootstrap-admin-without-padding");
            document.getElementById('panel-header').remove();
            //history.pushState({}, "", document.URL.replace("salir",""))
        </script>
        <?php
        $this->setBody(ob_get_clean(), true);
    }
}