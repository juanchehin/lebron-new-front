<?php

class Generador
{
    public static function opcionEditar($onclick, $static=false, $enlace='javascript:void(0)')
    {
        if(!$static)
        {
            return "<a href='{$enlace}' title='Editar' onclick='{$onclick}'><i class='fa fa-pencil text-info'></i></a>";
        }
    }

    public static function opcionEliminar($onclick, $static=false, $enlace='javascript:void(0)')
    {
        if(!$static)
        {
            return "<a href='{$enlace}' title='Eliminar' onclick='{$onclick}'><i class='fa fa-trash text-danger'></i></a>";
        }
    }

    public static function opcionAdjuntarImagen($onclick, $enlace='javascript:void(0)')
    {
        return "<a href='{$enlace}' onclick = '{$onclick}' title='Adjuntar imágenes'><i class='fa fa-picture-o'></i></a>";
    }

    public static function checkEstado($name, $onclick, $checked=null, $label=false, $static=false)
    {
        $inputCheck = null;

        $checked = $checked  ? 'checked="true"' : null;

        if(!$static)
        {
            $inputCheck =  "<input type='checkbox' name='{$name}' id='{$name}' class='check-estado' onclick='{$onclick}' {$checked} />";
            if($label)
            {
                $inputCheck .= "<label for='{$name}'>".ucfirst(preg_replace("#[\-,\_]#",' ',$name))."</label>";
            }
        }

        return $inputCheck;
    }

    public static function formInputCaptcha()
    {
        $inputCaptcha  = '<div class="row" id="captcha-container">';
        $inputCaptcha .= '<div class="col-md-6 text-center" id="captcha-container-image">';
        $inputCaptcha .= "<img src='".PUBLIC_HTTP."captcha/captcha.php' id='capImage' alt='captcha-image'/>";
        $inputCaptcha .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="reload_captcha()"><i class="fa fa-refresh"></i></a>';
        $inputCaptcha .= '</div>';
        $inputCaptcha .= '<div class="col-md-6 form-group" id="captcha-container-text">';
        $inputCaptcha .= '<input placeholder="C&oacute;digo de la imagen" name="captcha_code" type="text" autocomplete="off">';
        $inputCaptcha .= '</div>';
        $inputCaptcha .= "<script type='text/javascript'>";
        $inputCaptcha .= 'function reload_captcha() {';
        $inputCaptcha .= '$("#capImage").attr("src","'.PUBLIC_HTTP.'captcha/captcha.php?i=" + Math.random());';
        //$inputCaptcha .= '$.post("!UCaptcha/generarCaptcha",{"return" : false}, function(data){$("#capImage").attr("src",data);});';
        $inputCaptcha .= '$("input[name=\'captcha_code\']").val(null).focus();';
        $inputCaptcha .= '}</script>';
        $inputCaptcha .= '</div>';
        return $inputCaptcha;
    }

    public static function accionesForm($cancelar=true)
    {
        $acciones  = '<div class="acciones col-md-12"><ul>';
        $acciones .= '<li><a href="javascript:void(0)" onclick="guardar_datos()">Guardar</a></li>';
        if($cancelar)
        {
            $acciones .= '<li><a href="javascript:void(0)" class="btn-cerrar">Volver</a></li>';
        }
        $acciones .= '</ul></div>';

        return $acciones;
    }
}