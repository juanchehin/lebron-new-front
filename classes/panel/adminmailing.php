<?php

class AdminMailing extends AdminMain
{
    public function index()
    {
        $this->setPageTitle("Mailing");
        $this->getRows();
        $this->setBody("mailing-index");
    }

    public function getRows()
    {
        $search = $_REQUEST['s'];
        $correos = Contenido::where('tipo_contenido', Contenido::TIPO_EMAIL);
        if ( $search )
        {
            $correos = $correos->where('titulo', 'LIKE', "%{$search}%");
        }

        $result = $correos->paginate(20);
        $rows = null;
        if ( !$result[0] )
        {
            $rows .= "<tr class='text-center'><td colspan='4'>No se encontraron registros</td></tr>";
        }
        else
        {
            foreach ($result as $row)
            {
                $action = "<a href='" . self::PANEL_URI . "/mailing/ver/{$row->id_contenido}'><i class='fa fa-envelope'></i></a>";
                //$action = "<a href='".self::PANEL_URI."/mailing/ver/{$row->id_contenido}'><i class='fa fa-envelope'></i></a>";
                $rows .= "<tr>";
                $rows .= "<td>{$row->id_contenido}</td>";
                $rows .= "<td>{$row->titulo}</td>";
                $rows .= "<td>{$row->fecha}</td>";
                $rows .= "<td class='table-options'>{$action}</td>";
                $rows .= "</tr>";
            }
        }

        $rows .= "<tr class='dt_pagination'>";
        $rows .= "<td colspan='4'>";
        $rows .= preg_replace("#\<a\s+#", '<a onclick="paginate(this)" ', $result->links());
        $rows .= "</td>";
        $rows .= "</tr>";

        $this->setParams('table_rows', $rows);
    }

    public function formPage($id_contenido = null)
    {
        $mail = Contenido::find($id_contenido);
        $destinatario = null;
        if ( $mail )
        {
            $destinatario = implode(";", json_decode($mail->introduccion, true));
        }
        $this->addScript("plugins/ckeditor_47/ckeditor.js");
        $this->setPageTitle($mail ? "Email Nro {$mail->id_contenido}" : "Nuevo correo");
        $this->setParams('mail', $mail);
        $this->setParams('destinatario', $destinatario);
        $this->setBody("mailing-form");
    }

    public function enviar()
    {
        $id_mail = addslashes($_REQUEST['id_mail']);
        $asunto = trim($_REQUEST['asunto']);
        $destinatarios = explode(";", $_REQUEST['destinatario']);
        $mensaje = trim($_REQUEST['mensaje']);
        $enviar = isset($_REQUEST['check_enviar']);
        $destinatarios = $_destinatarios = array_filter($destinatarios, 'trim');

        #--
        if ( !$asunto )
        {
            HArray::jsonError("Especificar el <b>asunto</b>.");
        }

        if ( empty($destinatarios) )
        {
            HArray::jsonError("Agregue el o los <b>destinatarios</b>.");
        }

        if ( !strip_tags($mensaje) )
        {
            HArray::jsonError("Ingrese el <b>mensaje</b>");
        }
        #--
        $aviso = null;
        $enviados = 0;
        if ( $enviar )
        {
            $_destinatarios = array();
            foreach ($destinatarios as $destinatario)
            {
                $destinatario = trim($destinatario);
                if ( !filter_var($destinatario, FILTER_VALIDATE_EMAIL) )
                {
                    $aviso .= "<b>{$destinatario}</b> no es un correo electr&oacute;nico v&aacute;lido.<br/>";
                    continue;
                }

                /*$usuario = Usuario::where('email', $destinatario)->first();
                if ( $usuario )
                {
                    $mensaje .= "<p>Hola: {$usuario->nombre_persona},</p>";
                }*/
                $correo = new Emailer();
                $correo->setAsunto(ucfirst(strtolower($asunto)));
                $correo->setDestino($destinatario);
                $correo->setRemitente(EMAIL_CONTACTO, SITE_NAME);
                $correo->setEmailView(null, array('body' => $mensaje));
                if ( $correo->enviarEmail() )
                {
                    $enviados++;
                    $_destinatarios[] = $destinatario;
                }
            }
        }
        #-- Guardar
        $mail = Contenido::findOrNew($id_mail);
        $mail->titulo = $asunto;
        $mail->introduccion = json_encode($_destinatarios);
        $mail->texto = $mensaje;
        $mail->save();
        $json['location'] = CP_ADMIN . "/mailing";
        $json['notice'] = "Ning&uacute;n mensaje pudo ser enviado.";
        if ( $enviados )
        {
            $notice = "Se enviaron {$enviados} mensaje" . ($enviados > 1 ? 's' : '') . " correctamente.";
            if ( $aviso )
            {
                $notice .= "<br />Inconvenientes:<br />";
                $notice .= $aviso;
            }
            $json['notice'] = $notice;
        }

        HArray::jsonResponse($json);
    }
}