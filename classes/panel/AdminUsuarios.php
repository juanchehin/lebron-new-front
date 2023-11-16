<?php

class AdminUsuarios extends AdminMain
{
    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuUsuarios);
    }

    public function index()
    {
        $this->controlPermiso(Permiso::permisoVer);
        $this->setPageTitle("Usuarios registrados");
        $this->setBotonNuevo("Nuevo usuario");
        $table = new HDataTable();
        $table->setColumns(["Tipo de usuario", "Usuario", "Nombre", "Local", "E-mail", "Activo.text-center", "&nbsp;.text-center"]);
        $table->setHideDateRange();
        $table->setRows($this->getRows());
        $this->setParams('data_table', $table->drawTable());
        $this->setBody("usuarios-index");
    }

    public function getRows()
    {
        $text = trim($_POST['search_box']);
        $query = Usuario::where(['borrado' => 0, 'visible' => 1])->whereHas('hasPersona', function ($q) use ($text) {
            $q->where('apellido', 'LIKE', "%{$text}%")->orWhere('nombre', 'LIKE', "%{$text}%");
            $q->orWhere('email', 'LIKE', "%{$text}%");
        })->where('id_usuario', '<>', $this->admin_user->id_usuario);
        #--
        $count = $query->count();
        $usuarios = $query->orderBy('id_usuario', 'DESC')->paginate($this->x_page);
        #--
        $table_rows = null;
        foreach ($usuarios as $row)
        {
            $persona = $row->hasPersona;
            $table_rows .= "<tr id='" . ($id_usuario = $row->id_usuario) . "'>";
            $table_rows .= "<td>{$row->rol}</td>";
            $table_rows .= "<td>{$row->usuario}</td>";
            $table_rows .= "<td class='col-md-3'>{$persona->nombre_apellido}</td>";
            $table_rows .= "<td>{$row->local}</td>";
            $table_rows .= "<td class='col-md-3'>{$persona->email}</td>";
            $table_rows .= "<td class='text-center'>" . HForm::inputCheck("activo", $row->activo, 'set_estado(this)') . "</td>";
            $table_rows .= "<td class='text-center'>";
            if ( $this->controlPermiso(Permiso::permisoEditar, false) )
            {
                $table_rows .= "<a href='" . self::sysUrl . "/usuarios/editar/{$id_usuario}'><i class='fa fa-pencil-alt'></i></a>";
            }
            #--
            if ( $this->controlPermiso(Permiso::permisoBorrar, false) )
            {
                $table_rows .= "<a href='javascript:void(0)' onclick='dt_delete(this)'><i class='fa fa-trash text-danger'></i></a>";
            }
            $table_rows .= "</td>";
            $table_rows .= "</tr>";
        }
        #--
        $table_rows .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($usuarios->links())}</td></tr>";
        #--
        if ( self::isXhrRequest() )
        {
            die($table_rows);
        }

        return $table_rows;
    }

    public function perfilUsuario()
    {
        //$this->formPage($this->admin_user->id_usuario);
        $this->form($this->admin_user->id_usuario);
    }

    public function formCambioPass()
    {
        $this->cambiarClave();
    }

    public function cambiarClave()
    {
        $_POST['titulo'] = "Modificar contrase&ntilde;a";
        $body = static::renderView(self::ADMIN_VIEWS . "usuarios-cambiar-contrasena", $_POST);
        $this->setBlockModal($body);
    }

    public function savePassword()
    {
        $id_usuario = intval($_POST['id_usuario']);
        $pass = trim($_POST['contrasena']);
        $re_pass = trim($_POST['repetir_contrasena']);
        #--
        if ( !$id_usuario )
        {
            HArray::jsonError("Usuario no especificado");
        }

        if ( !$pass )
        {
            HArray::jsonError("Ingrese la nueva contraseña", "contrasena");
        }

        if ( $pass != $re_pass )
        {
            HArray::jsonError("Las contraseñas ingresadas no coinciden", "repetir_contrasena");
        }
        #--
        $usuario = Usuario::find($id_usuario);
        $usuario->contrasena = $pass;
        $usuario->save();
        #--
        $json['notice'] = "La contrase&ntilde;a fue modificada correctamente.";
        $json['ok'] = true;
        HArray::jsonResponse($json);
    }

    public function form($id_usuario = null)
    {
        $back_url = null;
        if ( $id_usuario != $this->admin_user->id_usuario )
        {
            $this->controlPermiso(Permiso::permisoEditar);
            $back_url = self::sysUrl . "/usuarios";
        }
        $data = Usuario::find($id_usuario);
        $titulo = "Datos de {$data->hasPersona->nombre_apellido}";
        if ( !$data )
        {
            $this->controlPermiso(Permiso::permisoCrear);
            $titulo = "Registrar nuevo usuario";
            $params['default_rol'] = Usuario::USR_PANEL_AUDITOR;
        }
        $this->setPageTitle($titulo);
        #--
        $selectLocal = "<label for='local'>Local</label>";
        $selectLocal .= "<select name='id_local' class='form-control' id='local'>";
        $selectLocal .= "<option value=''>Seleccionar</option>";
        foreach (Local::$_LOCALES as $id => $local)
        {
            $selectLocal .= "<option value='{$id}' " . ($data->id_local == $id ? 'selected' : '') . ">{$local}</option>";
        }
        $selectLocal .= "</select>";
        #-- Form
        $form = new PersonaForm();
        $form->setFormAction("!AdminUsuarios/guardarUsuario");
        $form->setData($data);
        $form->setEmailNoRequired();
        if ( $back_url )
        {
            $form->setTiposUsuario(Usuario::$_ROLES);
        }
        $form->setPuedeEditar($this->controlPermiso(Permiso::permisoEditar, false));
        $form->setBackUrl($back_url);
        $params['user_form'] = $form->drawForm();
        $params['adm_form'] = $back_url;
        $params['cambia_clave'] = $this->controlPermiso(Permiso::permisoClave, false);
        $params['permisos'] = $data->permiso; //en json
        $params['selectLocal'] = addslashes($selectLocal);
        $this->setParams($params);
        $this->setBody("usuario-form");
    }

    public function perfil()
    {
        $this->form($this->admin_user->id_usuario);
        $this->setParams('puede_editar', $this->admin_user->es_admin);
        $this->setParams('es_perfil', true);
        $this->setBody("usuarios-form");
    }

    public function permisosForm()
    {
        $rol = $_POST['rol'];
        $permisos = $_POST['actual'];
        #--
        $values['permisos'] = Permiso::getPermisos();
        $values['rol'] = $permisos ?: Permiso::permisoRol($rol);
        $this->setParams($values);
        $content = $this->loadView("admin/usuarios-permisos");
        die($content);
    }

    public function guardarUsuario()
    {
        /*if ( !$this->admin_user->esAdmin() )
        {
            HArray::jsonError("No tienes permisos para realizar cambios.");
        }*/
        $id_usuario = intval($_POST['id_usuario']);
        $tipo_usuario = trim($_POST['tipo_usuario']);
        $n_usuario = trim($_POST['username']);
        $clave = trim($_POST['contrasena']);
        $id_local = floatval($_POST['id_local']);
        $re_clave = trim($_POST['re_contrasena']);
        $permisos = (array)$_POST['permiso'];
        $permiso = null;
        foreach ($permisos as $item => $values)
        {
            $permiso[$item] = array_keys($values);
        }
        $persona = PersonaForm::guardar(Persona::ROL_USUARIO);
        $usuario = $persona->hasUsuario ?: new Usuario();
        $original = (array)$usuario['original'];
        #--
        if ( !$usuario->tipo && !key_exists($tipo_usuario, Usuario::$_ROLES) )
        {
            HArray::jsonError("Seleccionar tipo de usuario", "tipo_usuario");
        }
        #--
        if ( !$usuario->id_usuario )
        {
            #--
            if ( strlen($n_usuario) < 8 )
            {
                HArray::jsonError("Ingrese un Nombre de usuario de al menos 8 caracteres", "username");
            }

            if ( Usuario::where('id_usuario', '<>', $id_usuario)->where('usuario', $n_usuario)->first() )
            {
                HArray::jsonError("El Nombre de usuario ingresado existe. Elegir otro", "username");
            }

            if ( strlen($clave) < 6 )
            {
                HArray::jsonError("Ingresar una contraseña de al menos 6 caracteres", "contrasena");
            }

            if ( !$re_clave )
            {
                HArray::jsonError("Reingresar la Contraseña", "re_contrasena");
            }

            if ( $clave <> $re_clave )
            {
                HArray::jsonError("Las Contraseñas ingresadas no coinciden", "re_contrasena");
            }
        }
        #--
        $usuario->id_persona = $persona->id;
        if ( $tipo_usuario )
        {
            $usuario->tipo_usuario = $tipo_usuario;
        }
        if ( $n_usuario )
        {
            $usuario->usuario = strtolower($n_usuario);
        }
        #--
        if ( $clave )
        {
            $usuario->contrasena = $clave;
        }
        $usuario->id_local = $id_local;
        $usuario->permiso = $permiso;
        ##--
        $usuario->save();
        if ( $persona['estado']['cambios'] || array_diff($usuario['attributes'], $original) )
        {
            $json['notice'] = "Datos registrados correctamente.";
            if ( $usuario->id_usuario == $this->admin_user->id_usuario )
            {
                $usuario->setUserLogin(Usuario::PANEL_SESSION);
                $json['success'] = true;
            }
            else
            {
                //return;
                $json['location'] = self::sysUrl . "/usuarios";
            }
            HArray::jsonResponse($json);
        }
        return;
    }

    public function setEstado()
    {
        $id_usuario = intval($_POST['id']);
        $estado = $_POST['estado'];
        $columna = $_POST['attr'];
        #--
        $usuario = Usuario::find($id_usuario);
        $usuario->$columna = $estado;
        $usuario->save();
        HArray::jsonSuccess();
    }

    public function eliminar()
    {
        $id_usuario = intval($_POST['id']);
        $usuario = Usuario::find($id_usuario);
        $usuario->bajaLogica();
    }
}