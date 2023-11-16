<?php

use Illuminate\Support\Facades\DB;

class AdminCategoria extends AdminMain

{

    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuCategorias);

        //$this->setModel(self::categoriaModel);

    }



    public function index()

    {

        $this->setPageTitle("Categorias & Marcas");

        $this->setBotonNuevo("Nueva", "javascript:void(0)");

        #--table

        $grid = new HDataTable();

        $grid->setHideDateRange();

        $grid->setColumns(["#ID", "Nombre", "Activo.text-center", "&nbsp;.col-md-2"]);

        $grid->setRows($this->getRows());

        //$this->setParams('html_table', $grid->drawTable());

        $this->setParams('html_table', $this->getRows());

        $this->setBody("categorias-index");

        //$this->setParams('items', $this->getRows(true));

        //$this->setBody("rubro-index");

    }



    public function getRows($return = false)

    {

        $search = trim($_POST['q']);

        $tag = trim($_POST["tag"]);

        $categorias = Categoria::categorias($search, false, $tag);

        $trow = "<div id='sortable-1' rel='ids_parent'>";

        foreach ($categorias as $categoria)

        {

            $subcategorias = $categoria['subitems'];

            $subitems = null;

            if ( $subcategorias[0] )

            {

                //$titulo = "<a  data-toggle='collapse' href='#toggle-{$categoria->id_item}'>{$categoria->titulo}</a>";

                $subitems = "<div class='subitem' rel='{$categoria->id_item}'>";

                foreach ($subcategorias as $subcategoria)

                {

                    $subitems .= $this->_drawTableRow($subcategoria->id_item, $subcategoria->titulo . "&", $subcategoria->activo, $subcategoria->estatico);

                }

                $subitems .= "</div>";

            }

            $trow .= $this->_drawTableRow($categoria->id_item, $categoria->titulo, $categoria->activo, $categoria->estatico, $subitems);

        }

        $trow .= "</div>";

        //$trow .= "<script>enable_sort()</script>";

        #--

        if ( !$categorias[0] )

        {

            $trow = "<h4 class='alert alert-info'>No se encontraron registros</h4>";

        }

        #--

        if ( self::isXhrRequest() )

        {

            die($trow);

        }

        return $return ? $categorias : $trow;

    }



    private function _drawTableRow($id, $titulo, $activo, $static = false, $subitems = null)

    {

        $label = preg_replace("#\&#", "", $titulo, -1, $child);

        $trow = "<div class='trow' id='{$id}'>";

        $trow .= "<div class='trow-content'>";

        $trow .= "<div class='drag'></div>";

        $trow .= "<div class='col-md-5' style='top:5px'>{$id} - {$label}</div>";

        $trow .= "<div class='text-center flex-col'>";

        $trow .= HForm::inputCheck('activo', $activo, 'set_estado(this)', true);

        $trow .= "</div>";

        $trow .= "<div style='position:absolute;right:18px;margin-top:-22px'>";

        if ( $child && false )

        {

            $trow .= "<a href=''></a>";

        }

        //$trow .= "<a href='javascript:void(0)' onclick='get_modal_form({\"id\":\"{$id}\"},\"modalOpciones\")' title='Opciones'><i class='fa fa-list'></i></a>";

        // $trow .= "<a href='" . self::sysUrl . "/productos/{$id}'><i class='fa fa-boxes'></i></a>";

        // id usuario 29: luciana villagra
        // dd($this->admin_user->id_usuario);
        if (! ($this->admin_user->id_usuario == 29) )
        {
            $trow .= "<a href='" . self::sysUrl . "/estadistica/tag&{$id}' class='text-warning'><i class='fa fa-chart-bar'></i></a>";

            $trow .= "<a href='javascript:void(0)' onclick='get_form(\"{$id}\")'><i class='fa fa-edit'></i></a>";
        }


        if ( !$static )
        {
            if (! ($this->admin_user->id_usuario == 29) )
            {
                $trow .= "<a href='javascript:void(0)' onclick='delete_row(this)'><i class='fa fa-trash text-danger'></i></a>";
            }
        }

        $trow .= "<a href='javascript:void(0)' onclick='get_form_precio_categoria(\"{$id}\")'><i class='fa fa-bars'></i></a>";

        $trow .= "</div>";

        $trow .= "</div>";

        $trow .= $subitems;

        $trow .= "</div>";

        return $trow;

    }



    public function modalOpciones()

    {

        $id_categoria = intval($_POST['id']);

        $row = Categoria::find($id_categoria);

        $form_opciones = self::renderView("admin/producto-form-extra", array('id_categoria' => $id_categoria));

        #--

        $form = "<div class='panel panel-default'>";

        $form .= "<div class='panel-heading'>Opciones para {$row->titulo}</div>";

        $form .= "<div class='panel-body'>";

        $form .= $form_opciones;

        $form .= "<div class='form-btns'>";

        $form .= "<button class='btn btn-default' data-dismiss='modal'>Cerrar</button>";

        $form .= "</div>";

        $form .= "</div>";

        $form .= "</div>";

        //$form .= "<script>";

        //$form .= "</script>";

        $this->setBlockModal($form);

    }



    public function form($id = null)

    {

        $id_categoria = floatval($id ?: $_POST['id']);

        $tag = trim($_POST['tag']);

        $title = "Nueva " . ucfirst($tag);

        if ( $item = Categoria::find($id_categoria) )

        {

            $title = "Editar \"{$item->titulo}\"";

            $image = $item->imagen;

            //HArray::varDump($image);

        }

        //$this->setPageTitle($title);

        $categorias = Categoria::where(['borrado' => 0, 'id_item_padre' => 0])->where('id_item', "<>", $id_categoria)->get();

        if ( $item->hasSubcategoria[0] )

        {

            $categorias = array();

        }

        #--

        $precioBlock = new PreciosBlock();

        list($valor, $unidad) = explode("|", $item->valor);

        $precioBlock->setUtilidad($valor, $unidad);

        $precioBlock->isParent(false);

        $params = array(

            'item' => $item,

            'titulo' => $title,

            'tag' => $tag,

            'categorias' => $categorias,

            'precioBlock' => $precioBlock->draw()

        );

        if ( $tag == Categoria::tipoMarca )

        {

            $input_file = new InputFileControl();

            $input_file->setDefaultCrop($image->crop_data);

            $input_file->setImageSrc($image->archivo);

            $input_file->setAspectRatio(1.7);

            $params['imageControl'] = $input_file->drawInputFile();

        }

        $this->setParams($params);

        $this->setBlockModal($this->loadView("admin/categorias-form"));

        //$this->setBody("categorias-form");

    }


    public function form_precios_categorias($id = null)

    {

        $id_categoria = floatval($id ?: $_POST['id']);

        $tag = trim($_POST['tag']);

        $title = "Nueva " . ucfirst($tag);

        if ( $item = Categoria::find($id_categoria) )

        {

            $title = "Actualizacion de Precios \"{$item->titulo}\"";

            $image = $item->imagen;

            //HArray::varDump($image);

        }

        //$this->setPageTitle($title);

        $categorias = Categoria::where(['borrado' => 0, 'id_item_padre' => 0])->where('id_item', "<>", $id_categoria)->get();

        if ( $item->hasSubcategoria[0] )

        {

            $categorias = array();

        }

        #--

        $precioBlock = new PreciosBlock();

        list($valor, $unidad) = explode("|", $item->valor);

        $precioBlock->setUtilidad($valor, $unidad);

        $precioBlock->isParent(false);

        $params = array(

            'item' => $item,

            'titulo' => $title,

            'tag' => $tag,

            'categorias' => $categorias,

            'precioBlock' => $precioBlock->draw()

        );

        if ( $tag == Categoria::tipoMarca )

        {

            $input_file = new InputFileControl();

            $input_file->setDefaultCrop($image->crop_data);

            $input_file->setImageSrc($image->archivo);

            $input_file->setAspectRatio(1.7);

            $params['imageControl'] = $input_file->drawInputFile();

        }

        $this->setParams($params);

        $this->setBlockModal($this->loadView("admin/categorias-form-precios"));

        //$this->setBody("categorias-form");

    }



    public function guardar()

    {

        $id_item = intval($_POST['id_item']);

        $id_padre = intval($_POST['id_padre']);

        $nombre = trim($_POST['nombre']);

        $tag = trim($_POST['tag']);

        $utilidad = array_filter($_POST['utilidad']);

        $descripcion = trim($_POST['descripcion']);

        $input_file = $_FILES['input_file'];

        $crop_data = json_decode($json_crop = trim($_POST['cropit_values']), true);

        $activo = isset($_POST['activo']);

        #----

        if ( !$nombre )

        {

            HArray::jsonError("Proporcione un nombre a la {$tag}", "nombre");

        }

        $nombre = mb_strtolower($nombre);

        if ( Categoria::whereRaw("`id_item` <> '{$id_item}' AND `nombre` LIKE '{$nombre}'")->first() )

        {

            HArray::jsonError("Ya existe una categoría con ese nombre. Elija otro.", "nombre");

        }

        if ( count($utilidad) > 0 && count($utilidad) < 2 )

        {

            HArray::jsonError("Ingresar un valor y seleccionar unidad", "utilidad[]");

        }

        #--

        $item = Categoria::findOrNew($id_item);

        $original = (array)$item['original'];

        $imagen = $item->imagen;

        #--

        //$item->id_item_padre = $id_padre;

        $item->nombre = mb_strtolower($nombre);

        $item->tipo = $tag;

        $item->valor = implode("|", $utilidad);

        $item->descripcion = mb_strtolower($descripcion);

        if ( !$item->estatico )

        {

            $item->activo = $activo;

        }

        $item->save();

        #--

        $cambios = ($crop_data['chg'] != $imagen->arr_crop_data['chg']);

        $updload = InputFileControl::uploadImage($input_file, $imagen->archivo, $cambios);

        if ( $cambios )

        {

            Imagen::guardarImagen($imagen->id_imagen, $item->id_item, Categoria::tablaCategoria, $json_crop, $updload['file']);

        }

        $response['cambios'] = ($cambios || array_diff($item['attributes'], $original));

        $response['ok'] = 1;

        HArray::jsonResponse($response);

    }

    // ==========================================
    //  Actualiza los precios de cierta categoria
    // ==========================================
    
    public function actualizar_precios()

    {

        $id_item = $_POST['id_item'];

        $porcentaje_actualizacion = $_POST['aumento_porcentual'];

        $where = "!borrado AND `id_marca` = '" . $id_item . "'";

        $result = Articulo::whereRaw($where)->get();

        foreach ($result as $res)
        {
            $precio_viejo_string = $res->precio_compra;

            $parts = explode('|', $precio_viejo_string);

            $precio_viejo = $parts[0];
            $moneda = $parts[1];
            
            $precio_actualizado = $precio_viejo + ($precio_viejo * ($porcentaje_actualizacion/100));

            $precio_actualizado = floor($precio_actualizado);

            $res->precio_compra = $precio_actualizado . "|" . $moneda;

            $res->save();

        }

        $response['ok'] = 1;

        HArray::jsonResponse($response);

    }



    public function ordenar()

    {

        $values = explode(",", $_POST['ids']);

        foreach ($values as $i => $value)

        {

            $row = Categoria::find($value);

            $row->orden = $i;

            $row->save();

        }

        HArray::jsonSuccess();

    }



    public function setEstado()

    {

        $id = floatval($_POST['id']);

        $attr = trim($_POST['attr']);

        $estado = intval($_POST['estado']);

        #--

        if ( $item = Categoria::find($id) )

        {

            $item->{$attr} = $estado;

            $item->save();

        }

    }



    public function eliminar()

    {

        $key = floatval($_POST['key']);

        if ( $categoria = Categoria::find($key) )

        {

            $categoria->borrar();

        }

    }

}