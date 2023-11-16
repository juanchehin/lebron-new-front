<?php

class AdminRubro extends AdminMain
{
    const SECCION_MODEL = 'Seccion';
    const categoriaModel = 'Categoria';
    protected $items;
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->numeroNiveles();
        $this->rubrosEnPortada();
        $this->enableCheckOrden();
    }

    protected function setModel($value)
    {
        $this->model = $value;
        return;
    }

    protected function enableCheckOrden($value = true)
    {
        $this->setParams('check_orden', $value);
    }

    protected function numeroNiveles($value = 2)
    {
        $this->setParams('max_depth', $value);
    }

    protected function rubrosEnPortada($value = 2)
    {
        $this->setParams('rubro_portada', $value);
    }

    protected function botonPrincipal($value)
    {
        $this->setBotonNuevo("Nueva {$value}", "javascript:void(0)");
    }

    public function index()
    {
        $nestable = "plugins/nestable";
        $this->addStyle(self::ASSETS_DIR . "{$nestable}/nestable.css");
        $this->addScript(self::ASSETS_DIR . "{$nestable}/jquery.nestable.js");
        $this->addScript(self::ASSETS_DIR . "{$nestable}/nestable.js");
        $this->setParams('items', $this->items);
        $this->setBody('rubro-index');
    }

    public function form($imagen = null)
    {
        $id = intval($_POST['id']);
        $item = Categoria::find($id);
        $content = static::renderView(self::ADMIN_VIEWS . "rubro-form", ['item' => $item, 'imagen' => $imagen]);
        HArray::jsonResponse('body', $content);
    }

    public function guardar()
    {
        $model = $this->model;
        $id_item = intval($_POST['id_item']);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $imagen = $_FILES['input_file'];
        $activo = isset($_POST['activo']);
        #----
        if ( !$nombre )
        {
            HArray::jsonError("Proporcione un nombre a la {$model}", "nombre");
        }
        $info = pathinfo($imagen['name']);
        $item = $model::findOrNew($id_item);
        if ( $imagen['name'] )
        {
            if ( $imagen['error'] || !$imagen['size'] || !in_array($info['extension'], Categoria::$_EXTENSION_IMAGEN) )
            {
                HArray::jsonError("El archivo que intenta subir no es una imagen o contiene errores. Verifique el archivo.", "input_file");
            }

            if ( $imagen['size'] > 1024 )
            {
                HArray::jsonError("El tamaño en MB de la imagen ingresada supera lo permitido (1MB). Elija otra", "input_file");
            }

            $_file = "item_" . date('Ymd_His.') . $info['extension'];
            if ( move_uploaded_file($imagen['tmp_name'], IMAGE_DIR . "/{$_file}") )
            {
                @unlink($imagen->imagen);
                $item->imagen = $_file;
            }
        }
        $item->nombre = mb_strtolower($nombre);
        $item->descripcion = mb_strtolower($descripcion);
        if ( !$item->estatico )
        {
            $item->activo = $activo;
        }
        $item->save();
        // HArray::jsonResponse('ok',true);
        HArray::jsonSuccess();
    }

    public function setEstado()
    {
        $id = intval($_POST['id']);
        $estado = intval($_POST['estado']);
        $columna = $_POST['attr'];
        $count_portada = intval($_POST['count_portada']);
        $item = Categoria::find($id);
        if ( $columna == 'activo' && !$estado )
        {
            $item->hasSubcategoria()->update(['activo' => 0]);
            $item->portada = 0;
        }

        if ( $columna == 'portada' && (!$item->activo || ($count_portada <= 0 && $estado)) )
        {
            return;
        }
        $item->$columna = $estado;
        $item->save();
    }

    public function eliminar()
    {
        $id_item = intval($_POST['id']);
        $items = Categoria::where('id_item_padre', $id_item)->orWhere('id_item', $id_item);
        $items->delete();
        HArray::jsonSuccess();
    }

    public function ordenar()
    {
        $array = json_decode($_POST['output'], true);
        foreach ($array as $k => $val)
        {
            $id = $val['id'];
            foreach ($val['children'] as $i => $v)
            {
                $children = Categoria::find($v['id']);
                $children->orden = $i;
                $children->id_item_padre = $id;
                $children->save();
            }
            $seccion = Categoria::find($id);
            $seccion->id_item_padre = 0;
            $seccion->orden = $k;
            $seccion->save();
        }
    }

    #---
    public function selectRubro($tipo)
    {
        $term = trim($_GET['term'] ?: $_POST['term']);
        $rubros = Categoria::where(['borrado' => 0, 'tipo' => $tipo])->where('nombre', "LIKE", "%{$term}%")->orderBy("nombre")->get();
        $result = array();
        foreach ($rubros as $rubro)
        {
            $data['id'] = $rubro->id_rubro;
            $data['text'] = $data['value'] = $rubro->rubro;
            $result[] = $data;
        }
        #--
        \HArray::jsonResponse($result);
    }

    public function selectCategoria()
    {
        $this->selectRubro(Categoria::TIPO_CATEGORIA);
    }

    public function formCategoria()
    {
        $this->formRubro(Categoria::TIPO_CATEGORIA);
    }

    public function formRubro($tipo)
    {
        $id_rubro = intval($_POST['id_rubro']);
        $data['Categoria'] = Categoria::find($id_rubro);
        $data['id_producto'] = intval($_POST['id']);
        $data['titulo'] = "Editar \"{$data['Categoria']->rubro}\"";
        if ( !$data['Categoria']->hasSubcategoria[0] )
        {
            $data['rubros'] = Categoria::where(["id_item_padre" => 0, "tipo" => $tipo])->where("id_item", "<>", $id_rubro)->get();
        }
        $modal = self::renderView("admin/rubros-modal-form", $data);
        $this->setBlockModal($modal);
    }

    public function save()
    {
        $id_rubro = intval($_POST['id_rubro']);
        $id_rubro_padre = intval($_POST['id_rubro_padre']);
        $nombre = trim($_POST['nombre']);
        #----
        if ( strlen($nombre) < 4 )
        {
            HArray::jsonError("Asignar un nombre de al menos 4 caracteres", "nombre");
        }
        #--
        $data = Categoria::admRubro($id_rubro, $nombre, $id_rubro_padre);
        $result['name'] = $data->titulo;
        $result['ok'] = true;
        HArray::jsonResponse($result);
    }
}