<?php

class AdminRegistro extends AdminMain
{
    public static $_columnas = array("#ID", "DNI/CUIT", "Nombre", "E-mail", "Dirección","Zona", "Teléfono", "&nbsp;");

    public function __construct()
    {
        parent::__construct();
    }

    public function index($model = null)
    {
        if ( isset($_GET['dt']) && self::isXhrRequest() )
        {
            $_Model = trim($_POST['model'] ?: Cliente::class);
            $link = strtolower($_Model);
            $_Model = ucfirst($_Model);
            $search = trim($_POST['search_box']);
            $zona = trim($_POST['zona']);

            $all = intval($_POST['all']);
            #--
            $revendedor = !$model && !$all ? "CAST(JSON_EXTRACT(`json_otros_datos`, '$.revende') AS UNSIGNED) AND " : "";
            //$revendedor = "";
            $query = $_Model::whereRaw("!`borrado` AND {$revendedor} (`dni` LIKE '{$search}%' OR `apellido` LIKE '%{$search}%' OR `nombre` LIKE '%{$search}%' OR `email` LIKE '%{$search}%')");
            if($zona != 'N')
            {
                $query->where("zona","=",$zona);
            }
            /*if ( !$model && !$all )
            {
                $query->whereHas("hasCuenta", function ($sql){
                    //$sql->where('modulo', Movimiento::moduloCuenta);
                });
            }*/
            $cantidad = $query->count();
            $registros = $query->paginate($this->x_page);
            $trow = null;
            foreach ($registros as $reg)
            {
                if ( !$reg->array_otros_datos['revende'] && $reg->saldo )
                {
                    $reg->otros_datos_json = array('revende' => 1);
                    $reg->save();
                }
                /*if ( !$reg->array_otros_datos['saldo_update'] )
                {
                    $_saldo = array_pop(CuentaCliente::calculoSaldos($reg->id, ($_Model == Proveedor::class)));

                    $reg->saldo = $_saldo;
                    $reg->otros_datos_json = ['saldo_update' => date('Y-m-d')];
                    $reg->save();

                }*/
                $trow .= "<tr id='" . ($id = $reg->id) . "' class='" . ($reg->saldo > 0 ? 'unpaid' : '') . "'>";
                $trow .= "<td>{$id}</td>";
                $trow .= "<td>{$reg->dni}</td>";
                $trow .= "<td>";
                if ( $_Model == Cliente::class && ($this->admin_user->id_usuario != 29))
                {
                    $trow .= "<a target='_blank' href='" . self::appUrl . "?auth={$reg->auth_token}'><i class='fa fa-sign-in-alt'></i></a>&nbsp;";
                }
                $trow .= $reg->nombre_apellido . "</td>";
                $trow .= "<td>" . ($reg->email ?: "-") . "</td>";
                $trow .= "<td>" . ($reg->str_direccion ?: "-") . "</td>";                

                switch ($reg->zona) {
                    case 'S':
                        $trow .= "<td> Santiago </td>";
                        break;
                    case 'T':
                        $trow .= "<td> Tucumán Sur </td>";
                        break;
                    case 'G':
                        $trow .= "<td> Gimnasio y centro </td>";
                        break;
                    case 'N':
                        $trow .= "<td> No especificado </td>";
                        break;
                    default:
                    $trow .= "<td> No especificado </td>";
                }

                $trow .= "<td>" . ($reg->celular ?: "-") . "</td>";
                //$trow .= "<td class='amount'>" . HFunctions::formatPrice($reg->saldo) . "</td>";
                $trow .= "<td class='text-center'>";
                if ( $this->controlPermiso(Permiso::permisoCuentaVer, false) )
                {
                    $trow .= "<a href='" . self::sysUrl . "/{$link}/{$id}/saldos' title='Cuenta'><i class='fa fa-credit-card text-warning'></i></a>";
                }
                #--
                if ( $this->controlPermiso(Permiso::permisoEditar, false) )
                {
                    $trow .= "<a href='javascript:void(0)' onclick='get_modal_form({\"mdl\":\"{$link}\",\"id\":\"{$id}\"})'><i class='fa fa-edit'></i></a>";
                }
                #--
                if ( $this->controlPermiso(Permiso::permisoBorrar, false) )
                {
                    $trow .= "<a href='javascript:void(0)' onclick='dt_delete(this)'><i class='fa fa-trash-alt text-danger'></i></a>";
                }
                $trow .= "</td>";
                $trow .= "</tr>";
            }
            $trow .= "<tr class='not' data-count='{$cantidad}'><td colspan='" . count(static::$_columnas) . "'>{$this->replaceLinks($registros->links())}</td></tr>";
            die($trow);
        }
        #--
        if ( !$model )
        {
            $this->setPageTitle("Clientes & Cuentas");
        }
        #--
        if ( $model == "Sucursales" )
        {
            $this->setPageTitle("Sucursales");
        }
        #--
        if ( $model == "Gastos" )
        {
            $this->setPageTitle("Gastos varios");
        }
        #--
        if ( $model == "Remitos" )
        {
            $this->setPageTitle("Remitos varios");
        }
        #--
        if ( $model == "Quimicos" )
        {
            $this->setPageTitle("Venta de quimicos");
        }
        #--
        if ( $this->controlPermiso(Permiso::permisoCrear, false) )
        {
            $this->setBotonNuevo("Registrar", "javascript:void(0)");
        }
        $tabla = new HDataTable();
        $tabla->setColumns(static::$_columnas);
        if ( !$model )
        {
            $tabla->setHtmlControl("<label for='check-all'><input type='checkbox' id='check-all' /> Todos</label>");
        }
        $tabla->setHideDateRange();
        $tabla->setFiltroZona();
        $tabla->setKeys('model', $model ?: Persona::rolCliente);
        //$tabla->setKeys('all', 0);
        $tabla->setDataSource(static::class . "/index");
        $this->setParams('clientes_table', $tabla->drawTable());
        $this->setBody("registro-index");
    }


    public function modalForm()
    {
        //ini_set("display_errors", "On");
        $id = floatval($_POST['id']);
        list($rol, $select) = explode("*", trim(($modal = strtolower($_POST['mdl'])) ?: $_GET['run']));
        if ( !$select && !$this->controlPermiso($id ? Permiso::permisoEditar : Permiso::permisoCrear) )
        {
            return;
        }

        if ( !$rol )
        {
            $rol = Persona::rolCliente;
        }
        #--
        if ( isset($_GET['run']) && self::isXhrRequest() && $_POST )
        {
            $result = PersonaForm::guardar($rol);
            $estado = $result['estado'];
            if ( $estado['cambios'] && !$select )
            {
                $json['notice'] = "Datos de {$result->nombre_apellido} guardados correctamente";
            }
            $json['ok'] = $result->id;
            $json['label'] = $result->nombre_apellido;
            HArray::jsonResponse($json);
        }
        $titulo = "Nuevo Registro";
        if ( $registro = Persona::find($id) )
        {
            $titulo = "Editar \"{$registro->nombre_apellido}\"";
        }
        $this->setPageTitle($titulo);
        #--
        $form = new PersonaForm();
        //$form->setDniRequired();
        $form->setNoEsUsuario();
        $form->setEmailNoRequired();
        //$form->setBackUrl(self::sysUrl . "/{$rol}");
        $form->setBackUrl("javascript:void(0)");
        $form->setData($registro);
        $form->setFormAction("!" . static::class . "/modalForm?run={$modal}");
        $form->setEsModal();
        ob_start();
        ?>
        <script id="sc-model" rel="<?= $rol ?>">
            ["dob-group", "genre-group", "detalle-group"].forEach(function (id) {
                if ( (dvGroup = document.getElementById(id)) )
                {
                    dvGroup.remove();
                }
            });
            <?php if($modal == "cliente"): ?>
            document.getElementById('otros-datos-group').innerHTML = "<label for='revende'><input type='checkbox' id='revende' name='json[revende]' value='1' <?=$registro->array_otros_datos['revende'] ? "checked" : ""?>> Revendedor</label>";
            <?php endif; ?>
            if ( document.getElementById('sc-model').getAttribute("rel") === "<?=Persona::rolProveedor?>" )
            {
                document.getElementById('last-group').remove();
                (dvNameGroup = document.getElementById('name-group')).classList.remove("col-md-6");
                dvNameGroup.classList.add("col-md-12");
                //document.getElementById('otros-datos-group').innerHTML = "<label for='cbu'>Cuenta</label>";
            }

            document.getElementById('frm-persona').onsubmit = function (ev) {
                ev.preventDefault();
                submit_form(this, function (result) {
                    if ( (id = result["ok"]) && (select = document.getElementById('<?=$select?>')) )
                    {
                        select.insertAdjacentHTML("afterbegin", `<option value='${id}' selected>${result["label"]}</option>`);
                    }
                    else if ( typeof get_rows() === "function" )
                    {
                        get_rows();
                    }
                    //--
                    if ( (btnModalClose = document.getElementById('btn-close')) )
                    {
                        btnModalClose.click();
                    }
                });
            };
        </script>
        <?php
        $htmlForm = $form->drawForm(false) . ob_get_clean();
        if ( $modal )
        {
            $this->setBlockModal($htmlForm);
        }
        /*$this->setParams('form', $htmlForm);
        $this->setParams('rol', $rol);
        $this->setBody("registro-form");*/
    }

    public function setEstado($attr = null, $value = null)
    {
        $id = floatval($_POST['id']);
        $field = trim($attr ?: $_POST['attr']);
        $estado = trim($value ?: $_POST['estado']);
        if ( $row = Persona::find($id) )
        {
            $row->{$field} = $estado;
            $row->save();
        }
    }

    public function eliminar()
    {
        $this->setEstado("borrado", 1);
    }
}