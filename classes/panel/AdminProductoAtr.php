<?php



class AdminProductoAtr extends AdminProducto

{

    /* atributos producto online 07/03/2019 */

    public function onlineData($id_articulo)

    {

        if ( !$this->controlPermiso(Permiso::permisoPublicar, false) || (!$articulo = Articulo::find($id_articulo)) )

        {

            Router::redirect(self::sysUrl . "/productos");

        }

        $this->addStyle("static/plugin/jodit/jodit.min.css");

        $this->addScript("static/plugin/jodit/jodit.min.js");

        $this->setPageTitle("Artículo #{$id_articulo} - {$articulo->producto}");

        $inputFile = new InputFileControl();

        $inputFile->setAspectRatio(1.1);

        //$inputFile->setDefaultCrop();

        $this->setParams('inputFile', $inputFile->drawInputFile());

        $data['articulos'] = Articulo::whereRaw("!borrado AND !id_parent AND id_producto != '{$id_articulo}'")->get();

        $data['row'] = $articulo;

        $data['itemUrl'] = "<a href='" . self::appUrl . "/articulo/" . ($articulo->id_parent ?: $id_articulo) . "' target='_blank'>Ver</a>";

        $data['categorias'] = Categoria::where(['borrado' => 0, 'tipo' => Categoria::tipoCategoria])->orderBy("id_item_padre")->get();

        $this->setParams($data);

        $this->setBody("producto-atributos");

    }



    public function guardar()

    {

        $id_parent = floatval($_POST['id_producto_rel']);

        $id_producto = floatval($_POST['id_producto']);

        $id_categoria = floatval($_POST['id_categoria']);

        $esOferta = boolval($_POST['descuento']) ? -1 : 1;

        $precio = floatval($_POST['precio']);

        $texto = trim($_POST['texto']);

        $fecha_vencimiento = trim($_POST['fecha_vencimiento']);

        $iva = trim($_POST['iva']);

        $desc_prov = trim($_POST['desc_prov']);

        $costo = $_POST['costo'];

        $input_file = $_FILES['input_file'];

        $dimension = $_POST['web_option'];

        $itemsPromo = json_decode($_POST['itemPromo'], true);

        //HArray::varDump($itemsPromo);
	
        if ( $item = Articulo::find($id_producto) )

        {

            #--

            if ( isset($_GET['img']) )

            {

                if ( !$input_file['name'] )

                {

                    HArray::jsonError("Seleccionar una imagen");

                }

                $id_imagen = floatval($_POST['id_imagen']);

                $json_crop = trim($_POST['cropit_values']);

                #--

                $imagen = Imagen::findOrNew($id_imagen);

                $crop_data = json_decode($json_crop, true);

                $cambios = ($crop_data['chg'] != $imagen->arr_crop_data['chg']);

                $updload = InputFileControl::uploadImage($input_file, $imagen->archivo, $cambios);

                if ( $error = $updload['error'] )

                {

                    HArray::jsonError($error);

                }

                #--

                if ( $cambios )

                {

                    Imagen::guardarImagen($imagen->id_imagen, $id_producto, Articulo::TBL_ARTICULO, $json_crop, $updload['file']);

                }

            }

            else

            {

                $original = $item['original'];

                if ( (!$item->id_parent && !$id_parent) && !$precio )

                {

                    //HArray::jsonError("Ingresar el precio", "precio");

                }

                if ( $esOferta < 0 && !$precio )

                {

                    HArray::jsonError("Ingresar el porcentaje de descuento", "precio");

                }

                #--

                if ( !$id_categoria )

                {

                    //HArray::jsonError("Seleccionar categoría", "id_categoria");

                }

                #--

                $dimension_count = count(array_values(array_filter($dimension)));

                if ( $dimension_count > 0 && $dimension_count < 4 )

                {

                    HArray::jsonError("Ingresar todas las dimensiones del artículo", "dimension[alto]");

                }

                $item->precio_online = $precio * $esOferta;

                $item->id_parent = $id_parent;

                $item->dimension = $dimension;

                $item->json_items_promo = $itemsPromo;

                $item->id_categoria = $id_categoria;

                $item->texto = $texto;

                $primerCaracterIVA = substr($iva, 0, 1);
                
                $item->iva = $primerCaracterIVA;

                $item->descuento_prov = $desc_prov;

                $item->costo = $costo;
		
                if($fecha_vencimiento)
                {
                    $item->fecha_vencimiento = $fecha_vencimiento;
                }


                $item->save();

                #--

                if ( array_diff($item['attributes'], $original) )

                {

                    $json['notice'] = "Datos actualizados";

                }

            }

        }

        $json['ok'] = true;

        HArray::jsonResponse($json);

    }



    public function imagenesArticulo()

    {

        $id_articulo = floatval($_POST['cup']);

        $imagenes = Imagen::imagenesEntidad(Imagen::TBL_ARTICULO, $id_articulo);

        $tbl = "<div class='row' id='articulo-imagen'>";

        foreach ($imagenes as $img)

        {

            $tbl .= "<div class='col-md-4'>";

            $tbl .= "<div class='image-actions'>";

            $tbl .= "<a href='javascript:void(0)' onclick='img_borrar(\"{$img->id_imagen}\")'><i class='fa fa-trash-alt text-danger'></i></a>";

            $tbl .= "</div>";

            $tbl .= "<img src='{$img->image_crop_src}' width='100%' alt='{$img->id_imagen}'>";

            $tbl .= "</div>";

        }

        $tbl .= "</div>";

        if ( !$imagenes[0] )

        {

            $tbl .= "<div class='alert alert-info'>No hay imágenes</div>";

        }

        die($tbl);

    }



    public function borrarImagen()

    {

        $id_imagen = floatval($_GET['id']);

        if ( $imagen = Imagen::find($id_imagen) )

        {

            $imagen->delete();

            HArray::jsonSuccess();

        }

        HArray::jsonError("No se pudo eliminar la imagen");

    }

}