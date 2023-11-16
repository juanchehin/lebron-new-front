<?php

class MasterTemplate extends Loader
{
    protected $page_title = "Nueva página";
    protected $favicon;
    protected $atributo_meta;
    protected $atributo_meta_ogp;
    protected $template;
    protected $item_actual;
    protected $header_config;

    public function __construct()
    {
        parent::__construct();
        $this->add_styles('assets/bootstrap/css/bootstrap.min.css');
        $this->add_styles('assets/fonts/font-awesome/css/font-awesome.min.css');
        $this->add_styles('assets/plugins/jquery-ui/jquery-ui-theme.min.css');
        $this->add_styles('assets/css/default-style.css');
        #--
        $this->add_scripts('assets/js/jquery-2.1.4.min.js');
        $this->add_scripts('assets/plugins/jquery-ui/jquery-ui.min.js');
        $this->add_scripts('assets/bootstrap/js/bootstrap.min.js');
        $this->add_scripts('assets/js/bootbox.js');
        $this->add_scripts('assets/js/app.js');
        defined('CLASSNAME') OR define('CLASSNAME', get_called_class());
    }

    protected function setItemSeleccionado($value)
    {
        $this->item_actual = $value;
        return $this;
    }

    protected function getItemSeleccionado()
    {
        return $this->item_actual;
    }

    protected function setTemplate($template)
    {
        $this->template = $template;
        return  $this;
    }

    protected function setAtributoMeta($key, $value)
    {
        $this->atributo_meta[$key] = $value;
        return $this;
    }

    protected function setAtributoMetaOgp($key, $value)
    {
        $this->atributo_meta_ogp[$key] = $value;
        return $this;
    }

    protected function setMetaOgImage($value)
    {
        $this->setAtributoMeta('og:image', $value);
        return;
    }

    protected function setMetaOgTitle($value=null)
    {
        $this->setAtributoMetaOgp('og:title', $value ? $value : PAGE_TITLE);
        return;
    }

    protected function setMetaOgDescription($value)
    {
        $this->setAtributoMetaOgp('og:description', $value);
        return;
    }

    protected function setMetaOgUrl($value=null)
    {
        $og_url = BASE_URL . $_SERVER['REQUEST_URI'];
        $this->setAtributoMetaOgp('og:url', $value ? $value : $og_url);
        return;
    }

    protected function setDescription($content)
    {
        $this->setAtributoMeta('description',$content);
        return $this;
    }

    protected function setAuthor($content)
    {
        $this->setAtributoMeta('author', $content);
        return $this;
    }

    protected function setKeywords($content)
    {
        $this->setAtributoMeta('keywords', $content);
        return $this;
    }

    protected function setPageTitle($title)
    {
        $this->page_title = $title;

        return $this;
    }

    protected function getPageTitle()
    {
        return $this->page_title;
    }

    protected function setFavicon($icon)
    {
        $this->favicon = $icon;
        return $this;
    }

    protected function setContent($content=null)
    {
        $this->setValues('content',strtolower($content));
        $this->renderTemplate();
        return;
    }

    private function renderTemplate()
    {
		global $tmpFolder;
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang='es'>\n";
        $html .= "<head>\n";
        $html .= "<meta charset='utf-8'>\n";
        $html .= "<base href='/{$tmpFolder}' data-clase='".CLASSNAME."'>\n";
        $html .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
        foreach ($this->atributo_meta as $name => $content)
        {
            $html .= "<meta name='{$name}' content='{$content}'>\n";
        }

        foreach ($this->atributo_meta_ogp as $name => $content)
        {
            $html .= "<meta property='{$name}' content='{$content}'>\n";
        }

        if($this->favicon)
        {
            $html .= "<link rel='shortcut icon' href='{$this->favicon}'>\n";
        }
        $html .= "<title>{$this->escape($this->page_title)}</title>\n";
        $html .= "<!-- S T Y L E S H E E T S -->\n";
        foreach ($this->styles as $style)
        {
            $html .= "<link href='{$style}' type='text/css' rel='stylesheet'>\n";
        }

        $html .= "<!-- J A V A S C R I P T S -->\n";
        foreach ($this->scripts as $script)
        {
            $html .= "<script type='text/javascript' src='{$script}'></script>\n";
        }
        $html .= $this->header_config;
        $html .= "\n</head>\n";
        $html .= $this->loadView($this->template);
        $html .= "\n</html>";

        echo $html;
    }
}