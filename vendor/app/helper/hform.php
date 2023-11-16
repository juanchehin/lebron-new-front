<?php

class HForm
{
    public static function inputCheck($name, $checked, $function = null, $label = null, $opt = null)
    {
        $options = null;
        $id = uniqid();
        foreach ((array)$opt as $k => $v)
        {
            $options .= "{$k} = '{$v}' ";
        }
        $checked = ((is_bool($checked) && $checked) || $checked > 0) ? "checked='true'" : null;
        #--
        $check_box = "<input type='checkbox' name='{$name}' id='{$id}' {$checked} onclick='{$function}' {$options}/>";
        if ( $label )
        {
            $_label = !is_bool($label) ? $label : $name;
            $_label = ucfirst(preg_replace("#[\-,\_]#", ' ', $_label));
            $check_box .= "<label for='{$id}'>&nbsp;{$_label}</label>";
        }

        return $check_box;
    }

    public static function inputText($name, $value = null, $type = 'text', $label = true, $class = null, $options = array())
    {
        $opt = $input = null;
        foreach ($options as $k => $v)
        {
            $opt .= " {$k}='{$v}' ";
        }

        if ( $label )
        {
            $label = is_bool($label) ? ucfirst(preg_replace("#(\-,\_)#", null, $label)) : $label;
            $input .= "<label for='{$name}'>{$label}</label>";
        }
        $input .= "<input type='{$type}' name='{$name}' id='{$name}' value='{$value}' class='{$class} form-control' {$opt}>";

        return $input;
    }

    public static function inputPassword($name, $label = true, $class = null, $options = array())
    {
        $input = null;
        $input_id = uniqid();
        if ( $class && $label )
        {
            $label .= "<span id='result'></span>";
        }
        $options = array_merge($options, array('autocomplete' => "off"));
        $input .= static::inputText($name, null, 'password', $label, $class, $options);
        $input .= "<script type='text/javascript'>\n";
        $input .= "document.querySelector('input[name=\"{$name}\"]').setAttribute('id','" . $input_id . "');\n";
        $input .= "if( typeof $.toggleShowPassword === 'function' ) {\n";
        $input .= "$('#{$input_id}').after(\"<a href='javascript:void(0)' id='toggle-{$input_id}' class='small'>Mostrar</a>\");";
        $input .= "$.toggleShowPassword({'control':'#toggle-{$input_id}','field':'#{$input_id}'});";
        $input .= "}\n";
        $input .= "</script>";
        return $input;
    }

    public static function autocomplete($name, $source, $hidden_value = null)
    {
        ob_start();
        ?>
        <input type="text" name="<?= $name ?>" id="<?= $name ?>" class="form-control" placeholder="Buscar"/>
        <input type="hidden" name="hdn_<?= $name ?>" id="hdn_<?= $name ?>" value="<?= $hidden_value ?>">
        <script type="text/javascript">
            //@Require jQuery-ui
            var hidden_field = $('#hdn_<?=$name?>');
            var $autocomplete_input = $('input[name="<?=$name?>"]');
            $autocomplete_input.autocomplete({
                "maxShowItems": 15,
                "source": "<?=$source?>",
                "minLength": 3,
                "search": function () {
                    if ( $autocomplete_input.val().length < 3 )
                    {
                        hidden_field.val("");
                    }
                },
                "select": function (event, ui) {
                    json = ui['item'];
                    hidden_field.val(json.id)
                }
            });

            $('.ui-autocomplete').css({"max-height": "300px", "overflow-y": "auto", "overflow-x": "hidden"});
        </script>
        <?php
        return ob_get_clean();
    }
}