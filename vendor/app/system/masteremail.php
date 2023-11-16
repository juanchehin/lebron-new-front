<?php

class MasterEmail extends Loader
{
    const EMAIL_VIEWS = 'email.';
    const BODY_VALUE = 'body';
    protected $email_html = null;
    protected $destinatario;
    protected $copia_oculta;
    protected $remitente_email;
    protected $remitente_nombre;
    protected $asunto;
    protected $adjunto;

    public function setDestino($email, $nombre = null)
    {
        $this->destinatario[$email] = $nombre;
        //$this->nombre_destino = $nombre ? $nombre : preg_replace('#\@.*#',null,$email);
        return;
    }

    public function setCopiaOculta($email, $nombre = null)
    {
        $this->copia_oculta[$email] = $nombre;
        //$this->nombre_destino = $nombre ? $nombre : preg_replace('#\@.*#',null,$email);
        return;
    }

    public function setRemitente($email = null, $nombre = null)
    {
        $this->remitente_email = $email;
        $this->remitente_nombre = $nombre;

        return;
    }

    public function setAsunto($value)
    {
        $this->asunto = $value;

        return $this;
    }

    public function setAdjunto($path, $name = null)
    {
        $this->adjunto[$path] = $name;

        return $this;
    }

    public function setEmailView($vista = null, $params = array())
    {
        if ( $params )
        {
            $this->setValues($params);
        }

        if ( $vista )
        {
            $content = $this->loadView(self::EMAIL_VIEWS . $vista);
        }
        elseif ( $params['body'] )
        {
            $content = $params['body'];
        }
        $this->setValues(self::BODY_VALUE, $content);
        $this->email_html = $this->loadView(self::EMAIL_VIEWS . 'emailtemplate');

        return $this;
    }

    public function enviarEmail($_enviar = true)
    {
        if ( !$this->destinatario )
        {
            die('<h3>No se ha indicado el destinatario</h3>');
        }

        if ( !$this->email_html )
        {
            die('<h3>No se ha especificado el cuerpo del mensaje</h3>');
        }

        if ( DEVELOPMENT )
        {
            $this->destinatario = array();
            $this->setDestino('claudiosbarrera@gmail.com', 'CDB');
        }

        if ( $_enviar )
        {
            global $_CONF;
            $mailer = new PHPMailer();
            $mailer->isHTML(true);
            $mailer->isSMTP(true);
            $mailer->SMTPAuth = true;
            $mailer->SMTPDebug = 0;
            $mailer->SMTPAutoTLS = false;
            $mailer->SMTPSecure = SMTP_SECURE;
            $mailer->Host = SMTP_HOST;
            $mailer->Port = SMTP_PORT;
            $mailer->Username = SMTP_AUTH_USER;
            $mailer->Password = SMTP_AUTH_PASS;
            $mailer->Timeout = 60;
            $mailer->CharSet = "utf-8";
            $mailer->Subject = $this->asunto;
            $mailer->From = $this->remitente_email ? $this->remitente_email : $_CONF['email_contacto'];
            $mailer->FromName = $this->remitente_nombre ? $this->remitente_nombre : $_CONF['empresa'];
            #-- Destinatarios
            foreach ($this->destinatario as $email => $nombre)
            {
                if ( $email )
                {
                    $mailer->addAddress($email, $nombre);
                }
            }
            #-- Copia Oculta
            foreach ($this->copia_oculta as $email => $nombre)
            {
                if ( $email )
                {
                    $mailer->addBCC($email, $nombre);
                }
            }
            $mailer->Body = $this->email_html;
            foreach ($this->adjunto as $path => $file)
            {
                $mailer->AddAttachment($path, $file);
            }
            return $mailer->send();
        }
        else
        {
            echo $this->email_html . "<br />";
            //file_put_contents(SAVED_EMAIL . "/correo_" .microtime() . "_" . UFunciones::cadenaGuion($this->asunto) . ".html",utf8_decode($this->email_html));
        }
        return null;
    }
}