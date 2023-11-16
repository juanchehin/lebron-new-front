<?php
use Aura\Html\Exception\InvalidUtf8;

class MainTemplate extends Template
{
    const assetsPath = "/assets";
    const appUrl = HTTP_HOST;
    protected $template;
    protected $config;
    private $view_subdir;
    protected static $_ecommerce_file;

    public function __construct()
    {
        parent::__construct();
        $this->_prepare();
    }

    public function setViewSubdir($subdir = null)
    {
        if ( $subdir && !preg_match("#\/$#", $subdir) )
        {
            $subdir .= "/";
        }
        $this->view_subdir = $subdir;
        return;
    }

    protected function removeAsset($asset, $css = true)
    {
        if ( is_array($asset) )
        {
            $asset = implode("|", $asset);
        }
        #--

        foreach (($css ? $this->styles : $this->scripts) as $index => $item)
        {
            if ( preg_match("#({$asset})$#", $item) )
            {
                if ( $css )
                {
                    unset($this->styles[$index]);
                    return;
                }
                unset($this->scripts[$index]);
            }
        }
        return;
    }

    private function _prepare()
    {
        $this->addMetaAttr('author', "Ing CDB");
        $this->setFavicon("static/images/favicon.ico");
        $this->version = uniqid();
        $this->addStyle(self::assetsPath . "/bootstrap/css/bootstrap.min.css");
        $font = "//use.fontawesome.com/releases/v5.0.13/css/all.css";
        if ( DEVELOPMENT )
        {
            $font = self::assetsPath . "/font-awesome/css/font-awesome.min.css";
        }
        $this->addStyle($font);
        $this->addStyle(self::assetsPath . "/plugins/jquery-ui/jquery-ui.min.css");
        $this->addStyle(self::assetsPath . "/plugins/select2/css/select2.min.css");
        // $this->addStyle(self::assetsPath . "/css/global.css?ver={$this->version}");
        $this->addScript(self::assetsPath . "/plugins/jquery.min.js");
        $this->addScript(self::assetsPath . "/plugins/jquery-ui/jquery-ui.min.js");
        $this->addScript(self::assetsPath . "/bootstrap/js/bootstrap.min.js");
        $this->addScript(self::assetsPath . "/plugins/bootbox.min.js");
        $this->addScript(self::assetsPath . "/plugins/select2/js/select2.min.js");
        $this->addScript(self::assetsPath . "/plugins/select2/js/i18n/es.js");
        $this->addScript(self::assetsPath . "/plugins/jquery.mask.min.js");
        $this->addScript(self::assetsPath . "/app.js?ver={$this->version}");
        $this->_setConfig();
    }

    private function _setConfig()
    {
        $this->config = json_decode(file_get_contents("conf/tsconfig.json"), true);
        #-- Archivo de configuración ecommerce
        self::$_ecommerce_file = "conf/live_ecommerce1.json";
        //echo self::$_ecommerce_file;
        $this->config['mp'] = json_decode(file_get_contents(self::$_ecommerce_file), true);
        define('SITE_NAME', $this->config['site_name']);
        define('EMAIL_CONTACTO', $this->config['dev'] ? "claudiosbarrera@gmail.com" : $this->config['contacto']['email']);
        $this->setParams('config', $this->config);
    }

    protected function exportando($columns, $rows, $landscape = false, $output = true, $quitar_ultima = true)
    {
        $pdf = isset($_GET['pdf']);
        if ( $quitar_ultima )
        {
            array_pop($columns);
        }
        /*$data['columns'] = $columns;
        $data['rows'] = $rows;
        $this->setParams($data);*/
        $find[] = "/class/";
        $find[] = "/text-center/";
        $find[] = "/amount/";
        $find[] = "/fecha/";
        $find[] = "/datetime/";
        $find[] = "/entero/";
        $replace[] = "style";
        $replace[] = "text-align:center;";
        $replace[] = "mso-number-format:\"0\.00\";text-align:right;font-weight:500;";
        $replace[] = "mso-number-format:\"dd\/mm\/yyyy\";";
        $replace[] = "mso-number-format:\"dd\/mm\/yyyy hh\:mm\:ss\";";
        $replace[] = "mso-number-format:\"0\";";
        ob_start();
        ?>
        <table width="100%" border="1" cellspacing="0" style="page-break-inside:avoid;font-size:13px;">
            <thead>
            <tr>
                <?php foreach ($columns as $column) : ?>
                    <?php list($label, $clase) = preg_split("#\.\w+#", $column) ?>
                    <th style="text-transform:uppercase;background:#eee;font-weight:600"><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?= preg_replace($find, $replace, $rows) ?>
            </tbody>
        </table>
        <?php
        $content = ob_get_clean();
        ob_end_flush();
        ob_end_clean();
        if ( !$pdf ) $content = mb_convert_encoding($content, "UTF-8");
        if ( !$output )
        {
            return $content;
        }
        //die($content);
        ExportOpts::exportar($content, $pdf, $landscape);
    }

    protected function setBody($view = null, $is_html = false)
    {
        $this->setParams('_view_content', $is_html ? $view : $this->loadView($this->view_subdir . $view));
        parent::setBody($this->template);
    }

    protected function replaceLinks($links)
    {
        return preg_replace("#\<a\s+#", "<a onclick='dt_paginate(this)' ", $links);
    }

    protected static function isXhrRequest()
    {
        $es_xhr = preg_match("#\!#", $_SERVER['REQUEST_URI']);
        return (Router::isAjaxRequest() || $es_xhr);
    }

    protected function setBlockModal($modal_body = null, $modal_title = null, $modal_close = false)
    {
        if ( $modal_body && self::isXhrRequest() )
        {
            HArray::jsonResponse(['body' => $modal_body, 'title' => $modal_title]);
        }
        #--
        $modal = new HModalBlock();
        if ( $modal_close )
        {
            $modal->setModalClose();
        }
        $modal->setModalBody($modal_body);
        $modal->setModalTitle($modal_title);
        $this->setParams('block_modal', $modal->drawModal());
    }


    protected function modalBlock($modal_body = null)
    {
        if ( $modal_body && self::isXhrRequest() )
        {
            HArray::jsonResponse(['body' => $modal_body, 'ok' => 1]);
        }
        ob_start();
        ?>
        <style>
            .modals {
                display: none;
                position: fixed;
                z-index: 2;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgb(0, 0, 0);
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modals-content {
                background-color: #fefefe;
                margin: 10% auto;
                padding: 25px;
                border: 1px solid #888;
                width: 45%;
            }

            .dv-modal-close {
                text-align: right;
                margin-bottom: 4px;
            }

            .dv-modal-close a {
                color: #333;
                font-size: 25px;
                font-weight: bold;
                background: #f09c37;
                border-radius: 3px;
                padding: 0 6px;
            }

            .dv-modal-close a:hover,
            .dv-modal-close a:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }

            @media screen and (max-width: 1100px) {
                .modals-content {
                    width: 90%;
                }
            }
        </style>
        <div id="modal-<?= $modalId = uniqid() ?>" class="modals">
            <div class="modals-content">
                <div class="dv-modal-close">
                    <a class="modal-close" href="javascript:void(0)">&times;</a>
                </div>
                <div id="modal-body-<?= $modalId ?>"></div>
            </div>
        </div>
        <script>
            var $_modal = document.getElementById("modal-<?=$modalId?>");
            Element.prototype.setHtmlContent = function ($content) {
                this.innerHTML = $content;
                var arr = this.getElementsByTagName('script');
                for (var n = 0; n < arr.length; n++)
                {
                    eval(arr[n].innerHTML);
                }
            };

            function showModal(xhrUrl, params = {})
            {
                backGround = document.createElement("span");
                backGround.id = "bg-<?=$modalId?>";
                //backGround.style = "position:absolute;background:#ccc;opacity:.75;left:0;right:0;width:100%;height:100%;z-index:1";
                backGround.innerHTML = "Procesando...";

                //$_modal.insertAdjacentElement("afterbegin", backGround);
                content = (params["body"] || "");
                if ( xhrUrl )
                {
                    fetch(xhrUrl, {
                        "method": "POST",
                        "body": new URLSearchParams(params)
                    }).then(function (response) {
                        response.json().then(function (jsonRes) {
                            content = jsonRes["body"];
                        });
                    });
                }
                document.getElementById('modal-body-<?=$modalId?>').setHtmlContent(content);
                backGround.innerHTML = "";
                document.body.style["overflow-y"] = "hidden";
                $_modal.style.display = "block";
                //$_modal.style.display = "block";
            }

            Array.from(document.getElementsByClassName("modal-close")).forEach(function (closeBtn) {
                closeBtn.onclick = function (evt) {
                    evt.preventDefault();
                    //document.getElementById('bg-< ?=$modalId?>').remove();
                    document.body.style["overflow-y"] = "auto";
                    $_modal.style.display = "none";
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }
}