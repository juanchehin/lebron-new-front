<?php



//use PHPMailer\PHPMailer\PHPMailer;



class Emailer

{

    const EMAIL_VIEWS = 'email/';

    const BODY_VALUE = 'body';

    const EMAIL_TEMPLATE = self::EMAIL_VIEWS . 'email-template';

    protected $email_html = null;

    protected $destinatario;

    protected $copia_oculta;

    protected $remitente_email;

    protected $remitente_nombre;

    protected $asunto;

    protected $adjunto;



    #--

    public function setDestino($email, $nombre = null)

    {

        $this->destinatario[$email] = $nombre;

        return;

    }



    public function setCopiaOculta($email, $nombre = null)

    {

        $this->copia_oculta[$email] = $nombre;

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

        return;

    }



    public function setAdjunto($path, $name = null)

    {

        $this->adjunto[$path] = $name;

        return $this;

    }



    public function setEmailView($vista = null)

    {

        ob_start();

        ?>

        <table width="700" border="0" align="center" cellspacing="0" bgcolor="#FFF">

            <tbody>

            <tr style="background:#fff">

                <td style="padding:8px 25px;text-align:right;font-size:25px;border-bottom:2px solid #eee;" valign="midle">

                    <a href="<?= HTTP_HOST ?>" style="color:#fff;text-decoration:none;padding: 10px 5px">

                        <?php echo "<img src='" . HTTP_HOST . "/static/logo-lebron.jpg' width='180' alt='" . SITE_NAME . "'/>" ?>

                    </a>

                </td>

            </tr>

            <tr>

                <td>

                    <div style="padding:28px 25px;min-height:70vh;font-size:0.97em;">

                        <?= $vista ?>

                    </div>

                </td>

            </tr>

            <tr>

                <td style="padding:0 15px;background:#f8f8f8;line-height: 0" valign="top">

                    <div style="padding:15px 0;width:100%;">

                        <div style="width:25%;display: inline-block">

                            <?php $style = 'display:inline-block;vertical-align:middle;'; ?>

                        </div>

                        <div style="width:70%;display:inline-block;text-align:right;font-size:13px">

                            <?php $style .= 'padding-left:25px;text-align:left'; ?>

                        </div>

                        <div style="font-size:11px;padding-bottom:12px"><?= SITE_NAME ?>&nbsp;&reg;&nbsp;2019</div>

                    </div>

                </td>

            </tr>

            </tbody>

        </table>

        <?php

        $content = ob_get_clean();

        $this->_emailTemplate($content);

    }



    public function setEmailHtml($html)

    {

        $content = "<div style='min-width: 700px;padding:15px'>";

        $content .= $html;

        $content .= "</div>";

        $this->_emailTemplate($content);

        return;

    }



    private function _emailTemplate($body)

    {

        $template = "<!DOCTYPE html>";

        $template .= "<html>";

        $template .= "<head>";

        $template .= "<base href='/'>";

        $template .= "<meta charset='UTF-8'>";

        $template .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";

        $template .= "<title></title>";

        $template .= "</head>";

        $template .= "<body style='margin:0 auto;background:#fff;font-family: Helvetica, Arial, sans-serif'>";

        $template .= $body;

        $template .= "</body>";

        $template .= "</html>";

        $this->email_html = $template;

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



        if ( $_enviar )

        {

            $mailer = new PHPMailer();

            $mailer->isHTML();

            $mailer->isSMTP();

            $mailer->SMTPDebug = 0;

            $mailer->Host = 'mail.lebron-suplementos.com';

            $mailer->Port = 587;

            // $is_auth = !empty(SMTP_AUTH_USER);

            $mailer->SMTPAuth = true;

            $mailer->SMTPSecure = 'tls';

            $mailer->SMTPAutoTLS = false;

            $mailer->Username = 'info@lebron-suplementos.com';

            $mailer->Password = SMTP_AUTH_PASS;

            // if ( true )

            // {

            //     $mailer->Username = 'info@lebron-suplementos.com';

            //     $mailer->Password = 'mKi-uCb4UWds';

            //     $mailer->smtpConnect(

            //         array(

            //             "ssl" => array(

            //                 "verify_peer" => false,

            //                 "verify_peer_name" => false,

            //                 "allow_self_signed" => true

            //             )

            //         )

            //     );

            // }

            $mailer->Timeout = 60;

            $mailer->CharSet = "utf-8";

            $mailer->Subject = $this->asunto;

            // $mailer->From = $this->remitente_email;

            $mailer->addAddress("sgmtucuman@gmail.com");

            $mailer->FromName = $this->remitente_nombre;

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

            foreach ($this->adjunto as $path => $name)

            {

                $mailer->AddAttachment($path, $name);

            }

            //HArray::varDump($mailer);

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