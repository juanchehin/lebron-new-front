<style type="text/css">

    .trow {

        margin-bottom: 2px;

        color: #222;

        font-size: 13px;

    }



    .trow-content {

        width: 100%;

        background: #e0e0e0;

        border: 1px dashed #333;

        border-radius: 3px;

        display: inline-block;

    }



    .trow-content .flex-col {

        padding: 2px 0 0;

        width: auto;

        min-width: 12%;

        display: inline-block;

    }



    .subitem {

        margin-top: 2px;

        margin-left: 22px;

    }



    .subitem .trow-content {

        background: #f8f8f8;

    }



    .ui-sortable-placeholder {

        background-color: white;

        -webkit-box-shadow: 0 0 10px #888;

        -moz-box-shadow: 0 0 10px #888;

        box-shadow: 0 0 10px #888;

        height: 34px;

    }



    .trow-content .drag {

        margin-left: 4px;

        display: inline-block;

        float: left;

        margin-top: 1px;

    }



    .trow .drag:before {

        content: "▓";

        padding-right: 15px;

        cursor: move;

        font-size: 16px;

        color: #0D3349;

    }



    .trow .fa {

        font-size: 17px;

    }



    .trow a {

        margin-right: 8px;

    }

</style>

<div class="row form-group">

    <?php foreach ([$tagCategoria = "categoria", "marca"] as $value): ?>

        <label class="btn btn-primary" for="tag-<?= $value ?>"><input type="radio" name="tag" id="tag-<?= $value ?>"/> <?= ucfirst($value) ?></label>

    <?php endforeach; ?>

    <p></p>

    <input type="text" id="searchbox" class="form-control" placeholder="Buscar" style="width:220px"/>

</div>

<div class="row" id="dtc"></div>

<script type="text/javascript">

    let $params = JSON.parse(sessionStorage.getItem("tagOpt") || '{}');

    if ( !$params["tag"] )

    {

        $params["tag"] = "<?=$tagCategoria?>";

    }

    const ctgContainer = document.getElementById('dtc'), currentClass = "!AdminCategoria";

    document.getElementById('aa-nuevo').onclick = function (evt) {

        evt.preventDefault();

        get_form();

    };



    function get_form($id_ctg = 0)

    {

        get_modal_form({"id": $id_ctg, "tag": $params["tag"]}, currentClass + "/form");

    }

    function get_form_precio_categoria($id_ctg = 0)
    
    {
        get_modal_form_precios_categoria({"id": $id_ctg, "tag": $params["tag"]}, currentClass + "/form");

    }



    function enable_sort()

    {

        $("#sortable-1,.subitem").sortable({

            "tolerance": 'pointer',

            "revert": 'invalid',

            "placeholder": 'trow',

            "handle": '.drag',

            "forceHelperSize": true,

            "start": function (e, ui) {

                ui.placeholder.height(ui.item.height());

            },

            "update": function () {

                let values = [];

                //values[v_key] = [];

                this.childNodes.forEach(function (element) {

                    values.push(element.id);

                });

                fetch(currentClass + "/ordenar", {

                    "method": "POST",

                    "body": new URLSearchParams({"ids": values})

                });

            }

        });

    }



    function get_key($this)

    {

        return $this.closest('.trow').id;

    }



    document.getElementsByName('tag').forEach(function (radio) {

        radio.onclick = function () {

            $params["tag"] = this.id.replace(/\w+-/gi, "");

            list_rows();

        };

    });



    document.getElementById("tag-" + $params["tag"]).click();



    function list_rows()

    {

        ctgContainer.innerHTML = "<h3 class='text-center'><i class='fa fa-spin fa-spinner'></i> Cargando</h3>";

        fetch(`${currentClass}/getRows?list`, {

            "method": "POST",

            "body": new URLSearchParams($params)

        }).then(function ($rta) {

            //before_send();

            $rta.text().then(function ($html) {

                ctgContainer.innerHTML = $html;

                sessionStorage.setItem("tagOpt", JSON.stringify($params));

                enable_sort();

            });

        });

    }



    if ( (inputFind = document.getElementById('searchbox')) )

    {

        inputFind.onkeyup = function () {

            if ( (q = this.value.trim()).length > 2 || !q )

            {

                $params["q"] = q;

                list_rows();

            }

        };

    }



    function set_estado($this)

    {

        let $data = {

            "id": get_key($this),

            "estado": Number($this.checked),

            "attr": $this.name

        };

        /*let checks = $this.parentNode.parentElement.querySelectorAll('[rel="' + $data.id + '"] input[type="checkbox"]');

        if ( !$data.estado )

        {

            checks.forEach(function (check) {

                check.checked = false;

                check.setAttribute("disabled", true);

            });

        }*/

        fetch(currentClass + "/setEstado", {

            "method": "POST",

            "body": new URLSearchParams($data)

        });

    }



    function delete_row($this)

    {

        let key = get_key($this);

        jconfirm(function ($confirm) {

            if ( $confirm )

            {

                fetch(currentClass + "/eliminar", {

                    "method": "POST",

                    "body": new URLSearchParams({

                        "key": key

                    })

                });

                document.getElementById(key).remove();

            }

        });

    }



    list_rows();

</script>