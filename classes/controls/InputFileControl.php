<?php

class InputFileControl
{
    const maxSize = 1024;
    const maxWidth = 1000;
    const imageDir = "media/image";
    private $image_src;
    private $multiple = null;
    private $data_type = 1;
    private $default_crop;
    private static $aspectRatio = 1.2;
    public static $_extensiones = array('gif', 'jpg', 'jpeg', 'bmp');

    public function setImageSrc($src)
    {
        if ( file_get_contents($src) !== false )
        {
            $this->image_src = $src;
        }
        return;
    }

    /*
     * @param $values json
     */
    public function setDefaultCrop($values)
    {
        $this->default_crop = preg_replace("#(^\{|\}$)#", "", $values);
        return;
    }

    public function setEsInputFile()
    {
        $this->data_type = 2;
        return;
    }

    public function esMultiple()
    {
        $this->multiple = "multiple";
        return;
    }

    public function setAspectRatio($aspect)
    {
        self::$aspectRatio = floatval($aspect);
        return;
    }

    public function drawInputFile()
    {
        ob_start();
        $id = uniqid();
        $btn_label = "Seleccionar " . ($this->data_type > 1 ? "Archivo" : "Imagen");
        ?>
        <style type="text/css">
            .preview-image {
                min-height: 190px;
                overflow-y: hidden;
                margin-top: 4px;
                background: #f8f8f8;
                display: flex;
            }

            .preview-image img {
                max-width: 100%;
                display: block;
                height: 100%;
                margin: auto;
            }

            .cropit-preview {
                background-color: #f8f8f8;
                background-size: cover;
                border: 1px solid #ccc;
                border-radius: 3px;
                margin-top: 7px;
            }

            .cropit-preview-image-container {
                cursor: move;
            }

            .cropit-options {
                display: flex;
            }

            .cropit-options button, .cropit-options input,
            {
                display: inline-block;
                width: 100%;
            }

            .image-editor input, .export {
                display: block;
            }

            .image-editor button {
                border: none;
                margin-top: 10px;
                background: transparent;
                color: #0e76a8;
            }
        </style>
        <button type="button" class="btn btn-warning" id="btn-<?= $id ?>"><?= $btn_label ?></button>
        <?php if ( false ) : ?>
        <div class="preview-image"><?= $this->image_src ?></div>
    <?php endif; ?>
        <div class="image-editor">
            <input type="file" <?= $this->multiple ?> class="cropit-image-input" onchange="IFC.select_file(this)" name="input_file" id="if-<?= $id ?>" rel="<?= $this->data_type ?>" style="display: none">
            <div class="cropit-preview" id="cropit-preview"></div>
            <div class="cropit-options">
                <?php if ( false ) : ?>
                    <button class="rotate-ccw" type="button"><i class="fa fa-undo-alt"></i></button>
                <?php endif; ?>
                <input placeholder="" id="cropit-image-zoom" type="range" class="cropit-image-zoom-input" style="margin-top:10px">
                <?php if ( false ) : ?>
                    <button class="rotate-cw" type="button"><i class="fa fa-redo-alt"></i></button>
                <?php endif; ?>
            </div>
            <?php //<input type="hidden" name="cropit_image" id="cropit-image-result"/>
            ?>
            <input type="hidden" name="cropit_values" id="cropit-values"/>
        </div>
        <div id="input-file-notice"></div>
        <script src="static/cropit/dist/jquery.cropit.js"></script>
        <script type="text/javascript">
            var IFC = {}, default_crop = {<?=$this->default_crop?>};
            document.getElementById('btn-<?=$id?>').onclick = function () {
                document.getElementById('if-<?=$id?>').click();
            };

            IFC.select_file = function (ifile) {
                const maxSize = 1024;
                let inputType = ifile.getAttribute("rel"); //1:imagen, 2:archivo (documento)
                if ( inputType && !ifile.getAttribute("multiple") )
                {
                    let file = ifile.files[0];
                    let fileName = file.name;
                    let fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1).toLocaleLowerCase();
                    let fileSize = Math.round(file.size / 1024);
                    let msj = null;
                    let extension = ['jpeg', 'jpg', 'png', 'gif'];
                    if ( inputType > 1 )
                    {
                        extension = ['doc', 'docx', 'pdf', 'odf'];
                    }

                    if ( $.inArray(fileExtension, extension) < 0 )
                    {
                        msj = 'El archivo debe ser <i>' + extension.join(" / ") + '</i>';
                    }
                    else if ( fileSize > maxSize )
                    {
                        msj = 'El tama&ntilde;o del archivo es mayor a ' + (maxSize / 1024) + ' MB (' + maxSize + 'KB)';
                    }

                    if ( msj )
                    {
                        document.getElementById('input-file-notice').innerHTML = msj;
                        //$('.cropit-preview').empty();
                        ifile.value = null;
                        return;
                    }
                    //div.show().html("<span style='color:blue'> Imagen: " + fileName + ", Tama&ntilde;o: " + fileSize + " KB.</span>");
                    if ( inputType < 2 && false )
                    {
                        //IFC.vista_previa(ifile);
                        return true;
                    }

                    return true;
                }
            };

            /*function docReady(fn)
            {
                if ( document.readyState === "complete" || document.readyState === "interactive" )
                {
                    setTimeout(fn, 1);
                    return;
                }
                document.addEventListener("DOMContentLoaded", fn);
            }*/

            setTimeout(function () {
                var imageEditor = $('.image-editor');
                var _width = imageEditor.outerWidth() - 15;
                $('.cropit-preview').css({'width': '100%', 'height': _width / parseFloat(<?=self::$aspectRatio?>)});
                imageEditor.css('width', _width).cropit({
                    "allowDragNDrop": false,
                    "imageState": {
                        "src": "<?=$this->image_src?>",
                        "zoom": default_crop.img_zoom,
                        "offset": default_crop.img_coords
                    },
                    "onFileChange": function () {
                        return false;
                    },
                    "onImageLoading": function () {

                    },
                    "onImageLoaded": function () {
                        //IFC.get_image_cropit(true);
                    },
                    "onOffsetChange": function () {
                        /*let imageData = imageEditor.cropit('export', {'type': 'image/jpeg', 'quality': 0.33, 'originalSize': true});
                        $('#cropit-image-result').val(imageData);*/
                        IFC.get_image_cropit();
                    }
                });

                IFC.get_image_cropit = function () {
                    let imageData = imageEditor.cropit('export',
                        {
                            'type': 'image/jpeg',
                            'quality': 0.5,
                            'originalSize': true
                        });
                    //$('#cropit-image-result').val(imageData);
                    let arr_css = {};
                    arr_css.img_coords = imageEditor.cropit("offset");
                    arr_css.img_zoom = imageEditor.cropit("zoom");
                    arr_css.preview_size = imageEditor.cropit("previewSize");
                    arr_css.chg = arr_css.img_coords.x + arr_css.img_coords.y + arr_css.img_zoom;
                    $('#cropit-values').val(JSON.stringify(arr_css));
                    return imageData;
                };
            });
            $('.rotate-cw').click(function () {
                imageEditor.cropit('rotateCW');
            });
            $('.rotate-ccw').click(function () {
                imageEditor.cropit('rotateCCW');
            });

            $('#export').click(function () {
                window.open(IFC.get_image_cropit())
            });
            //IFC.get_image_cropit(true);
        </script>

        <?php
        return ob_get_clean();
    }

    /*
     * @param $file
     *
     */
    public static function uploadImage($imagen, $current_image = null, $crop = null)
    {
        $info = pathinfo($imagen['name']);
        $result = array();
        $current_image_crop = preg_replace("#(.+)(\.\w+)$#", "$1_crop$2", $current_image);
        if ( $imagen['name'] )
        {
            if ( $imagen['error'] || !$imagen['size'] || !in_array(strtolower($info['extension']), self::$_extensiones) )
            {
                $result['error'] = "El archivo que intenta subir no es una imagen o contiene errores. Verifique el archivo.";
            }

            if ( round($imagen['size'] / 1024) > self::maxSize )
            {
                $result['error'] = "El tamaño en MB de la imagen ingresada supera lo permitido (1MB). Elija otra";
            }

            $_file = self::imageDir . "/img_" . time() . ".{$info['extension']}";//static::redimensionar($imagen['tmp_name']);
            if ( move_uploaded_file($imagen['tmp_name'], $_file) )
            {
                @unlink($current_image);
                //@unlink($current_image_crop);
            }
            $result['file'] = $_file;
        }
        #--
        if ( $crop )
        {
            $crop['x1'] = $crop['img_coords']['x'];
            $crop['y1'] = $crop['img_coords']['y'];
            $crop['x2'] = $crop['preview_size']['width'];
            $crop['y2'] = $crop['preview_size']['height'];
            @unlink($current_image_crop);
            $result['crop'] = true;
            //static::createCrop($_file, $crop);
        }
        return $result;
    }

    public static function createCrop($imagen, $crop, $drop_original = false)
    {
        $info = getimagesize($imagen);
        $ext = preg_replace("#\w+\/#", null, $info['mime']);
        $zoom = (1 / $crop['img_zoom']);
        $x1 = abs(floor($crop['x1'] * $zoom));
        $y1 = abs(floor($crop['y1'] * $zoom));
        $x2 = floor($crop['x2'] * $zoom) + $x1;
        $y2 = floor($crop['y2'] * $zoom) + $y1;
        #--
        switch ( strtolower($ext) )
        {
            case 'png':
                $img_r = imagecreatefrompng($imagen);
                break;
            case "pjpeg":
            case "jpeg":
            case "jpg":
                $img_r = imagecreatefromjpeg($imagen);
                break;
            case 'gif':
                $img_r = imagecreatefromgif($imagen);
                break;
        }
        $w = $x2 - $x1;
        $h = $y2 - $y1;
        $dest = imagecreatetruecolor($w, $h);
        //$final = imagecreatetruecolor($crop_ancho = ($info[0] / 2), $crop_alto = ($info[1] / 2));
        imagecopy($dest, $img_r, 0, 0, $x1, $y1, $w, $h);
        //imagecopyresampled($final, $dest, 0, 0, 0, 0, $crop_ancho, $crop_alto, $w, $h);
        $nueva_imagen = preg_replace("#(.+)(\.\w+)$#", "$1_crop$2", $imagen);
        switch ( $ext )
        {
            case "png":
                imagepng($dest, $nueva_imagen, 85);
                break;
            case "pjpeg":
            case "jpeg":
            case "jpg":
                imagejpeg($dest, $nueva_imagen, 85);
                break;
            case "gif":
                imagegif($dest, $nueva_imagen);
                break;
        }
        #--
        //HArray::varDump($zoom);
        //HArray::varDump("{$x1} - {$y1} - {$x2} - {$y2}");
        imagedestroy($dest);
        imagedestroy($img_r);
        if ( $drop_original )
        {
            unlink($imagen);
        }
        return $nueva_imagen;
        //static::redimensionar($nueva_imagen, $nueva_imagen);
    }

    public static function redimensionar($imagen, $destino = null)
    {
        $max_ancho = self::maxWidth;
        $max_alto = $max_ancho / self::$aspectRatio;

        $image_size = getimagesize($imagen);
        $ancho = $image_size[0];
        $alto = $image_size[1];
        $crop_x = 0;
        $crop_y = 0;
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;

        if ( ($ancho <= $max_ancho) && ($alto <= $max_alto) )
        {
            $ancho_final = $ancho;
            $alto_final = $alto;
        }
        elseif ( ($x_ratio * $alto) < $max_alto )
        {
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        }
        else
        {
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }

        #--
        $extension = strtolower(preg_replace("#\w+\/#", "", $image_size['mime']));
        switch ( $extension )
        {
            case 'png':
                $img_r = imagecreatefrompng($imagen);
                break;
            case "pjpeg":
            case "jpeg":
            case "jpg":
                $img_r = imagecreatefromjpeg($imagen);
                break;
            case 'gif':
                $img_r = imagecreatefromgif($imagen);
                break;
        }
        $dst_r = imagecreatetruecolor($ancho_final, $alto_final);

        imagecopyresampled($dst_r, $img_r, 0, 0, $crop_x, $crop_y, $ancho_final, $alto_final, $ancho, $alto);

        $nueva_imagen = $destino ?: self::imageDir . "/img_" . time() . ".{$extension}";

        switch ( $extension )
        {
            case 'png':
                imagepng($dst_r, $nueva_imagen, 85);
                break;
            case "pjpeg":
            case "jpeg":
            case "jpg":
                imagejpeg($dst_r, $nueva_imagen, 85);
                break;
            case 'gif':
                imagegif($dst_r, $nueva_imagen);
                break;
        }

        imagedestroy($dst_r);

        return $nueva_imagen;

    }
}