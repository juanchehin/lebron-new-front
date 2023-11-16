let attr_opt = {};

let labelPrecio;

let labelCantidad;



dat = {

    "id": 1105017901,

    "status": "closed",

    "external_reference": null,

    "preference_id": "403963614-b6e52c31-6e4e-401e-a87a-036293a9d759",

    "payments": [{"id": 4736441170, "transaction_amount": 2886, "total_paid_amount": 2886, "shipping_cost": 0, "currency_id": "ARS", "status": "approved", "status_detail": "accredited", "operation_type": "regular_payment", "date_approved": "2019-05-04T16:34:54.000-04:00", "date_created": "2019-05-04T16:34:52.000-04:00", "last_modified": "2019-05-04T16:34:54.000-04:00", "amount_refunded": 0}],

    "shipments": [{

        "id": 27953879270,

        "shipment_type": "shipping",

        "shipping_mode": "me2",

        "picking_type": null,

        "status": "pending",

        "substatus": null,

        "items": [{"description": "C4 RIPPED. CELLUCOR. 30 Un", "dimensions": "8.0x8.0x13.0,195.0", "dimensions_source": null, "id": "569", "quantity": 1}],

        "date_created": "2019-05-04T16:32:48.000-04:00",

        "last_modified": "2019-05-04T16:34:43.000-04:00",

        "date_first_printed": null,

        "service_id": null,

        "sender_id": 403963614,

        "receiver_id": 99154401,

        "receiver_address": {

            "id": 1035765013,

            "address_line": "Deheza 63",

            "city": {"id": null, "name": "Cordoba"},

            "state": {"id": "AR-X", "name": "C\u00f3rdoba"},

            "country": {"id": "AR", "name": "Argentina"},

            "latitude": "-31.414151",

            "longitude": "-64.161866",

            "comment": "Piso7 dpto D torre 25 de Mayo",

            "contact": "Gonzalo Made Escudero",

            "phone": "3815554433",

            "zip_code": "5000",

            "street_name": "Deheza",

            "street_number": "63",

            "floor": null,

            "apartment": null

        },

        "shipping_option": {"id": 450730870, "cost": 0, "currency_id": "ARS", "shipping_method_id": 73328, "estimated_delivery": {"date": "2019-05-09T00:00:00.000-03:00", "time_from": null, "time_to": null}, "name": "Normal a domicilio", "list_cost": 284.99, "speed": {"handling": 24, "shipping": 48}}

    }],

    "collector": {"id": 403963614, "email": "jornalsoft@gmail.com", "nickname": "KSOZE10"},

    "marketplace": "MP-MKT-5867779358146915",

    "notification_url": "https:\/\/lebron-suplementos.com\/!FrontPayment\/notificacion",

    "date_created": "2019-05-04T20:32:48.216+00:00",

    "last_updated": "2019-05-04T20:34:54.649+00:00",

    "sponsor_id": null,

    "shipping_cost": 0,

    "total_amount": 2886,

    "site_id": "MLA",

    "paid_amount": 2886,

    "refunded_amount": 0,

    "payer": {"id": 99154401, "email": null},

    "items": [{"id": "569", "category_id": "29", "currency_id": "ARS", "description": "", "picture_url": "", "title": "C4 RIPPED. CELLUCOR. 30 Un", "quantity": 1, "unit_price": 1732}, {

        "id": "218",

        "category_id": "29",

        "currency_id": "ARS",

        "description": "",

        "picture_url": "",

        "title": "BIOPROT. HOCH SPORT. 1 Kg. | \n Dulce de Leche",

        "quantity": 1,

        "unit_price": 1154

    }],

    "cancelled": false,

    "additional_info": null,

    "application_id": null

};

attr_opt["cantidad"] = 1;

attr_opt["promoItems"] = {};



function get_parent($elm, $prnt = true)

{

    let parent = $elm.form;

    labelPrecio = document.getElementById('precio-' + parent.id);

    labelCantidad = document.getElementById('sp-cantidad-' + parent.id);

    return $prnt ? parent : parent.id;

}



function change_opt($dd)

{

    let option = $dd.options[$dd.selectedIndex];

    let parent = get_parent($dd);

    //json_opt = JSON.parse(option.value);

    if ( option.value )

    {

        if ( Object.values(parent.dataset).length > 1 )

        {

            itemId = $dd.getAttribute("rel");

            parent.setAttribute(`data-${itemId}`, option.value);

        }

        else

        {

            //parent.setAttribute("rel", option.innerText.replace(/[^\d+]/gi,""));

            parent.setAttribute("rel", option.dataset["qty"]);

            attr_opt["cup"] = option.value;

        }

    }

    attr_opt["sabor"] = option.innerText.replace(/\(.+$/gi, "");

    if ( (precio = parseFloat(option.getAttribute("rel"))) > 0 )

    {

        attr_opt[parent.id] = precio;

        labelPrecio.innerHTML = precio.toFixed(2);

    }

    labelCantidad.innerText = "1";

}



function up_down($btn)

{

    let parent = get_parent($btn);

    let cantidad = parseInt(labelCantidad.innerText);

    if ( !attr_opt[parent.id] )

    {

        attr_opt[parent.id] = parseFloat(labelPrecio.innerText);

    }

    //--

    if ( $btn.getAttribute("rel") )

    {

        if ( cantidad > 1 )

        {

            cantidad--;

        }

    }

    else if ( cantidad < parseInt(parent.getAttribute("rel")) )

    {

        cantidad++;

    }

    attr_opt["cantidad"] = cantidad;

    labelPrecio.innerHTML = (attr_opt[parent.id] * cantidad).toFixed(2);

    labelCantidad.innerHTML = cantidad;

}



function comprar($me)

{

    $parent = get_parent($me);

    promoItems = Object.values($parent.dataset);

    stockDisponible = parseInt($parent.getAttribute("rel"));

    let id = $me.getAttribute("rel");

    document.getElementsByName(`cbo-${id}`).forEach(function (elm) {

        if ( !parseInt(elm.options[elm.selectedIndex].dataset["qty"]) )

        {

            stockDisponible = 0;

        }

    });

    /*if ( (sabor = attr_opt["sabor"]) )

    {

        nombre = nombre.replace(/(\|).+$/g, "$1 " + sabor);

    }

    attr_opt["nombre"] = nombre;*/

    if ( stockDisponible < 1 )

    {

        jdialog("<span class='text-danger'>Sin stock disponible</span>");

        return;

    }

    attr_opt["subtotal"] = parseFloat(labelPrecio.innerText);

    attr_opt["directa"] = $me.name;

    if ( !attr_opt["cup"] )

    {

        attr_opt["cup"] = $parent.id;

        if ( promoItems.length > 1 )

        {

            attr_opt["cup"] += `_${promoItems.join("_")}`;

        }



    }

    //console.log(attr_opt);

    //return;

    $me.insertAdjacentHTML("afterbegin", "<i class='fa fa-spin fa-spinner'></i>");

    $me.setAttribute("disabled", true);

    fetch("!FrontCart/setItemCart", {

        "method": "POST",

        "body": new URLSearchParams(attr_opt)

    }).then(function (res) {

        res.json().then(function ($js) {

            delete attr_opt["cup"];

            if ( attr_opt.directa )

            {

                location.href = "./checkout";

                return;

            }

            $me.removeAttribute("disabled");

            $me.firstElementChild.remove();

            if ( !(msj = $js["error"]) )

            {

                msj = "Artículo agregado a la compra";

                get_count_cart();

            }

            document.getElementById('btn-dwn-' + $parent.id).click();

            jdialog(msj);

        });

    });

}


function comprarMayoristas($me)

{

    $parent = get_parent($me);

    promoItems = Object.values($parent.dataset);

    stockDisponible = parseInt($parent.getAttribute("rel"));

    let id = $me.getAttribute("rel");

    document.getElementsByName(`cbo-${id}`).forEach(function (elm) {

        if ( !parseInt(elm.options[elm.selectedIndex].dataset["qty"]) )

        {

            stockDisponible = 0;

        }

    });

    /*if ( (sabor = attr_opt["sabor"]) )

    {

        nombre = nombre.replace(/(\|).+$/g, "$1 " + sabor);

    }

    attr_opt["nombre"] = nombre;*/

    if ( stockDisponible < 1 )

    {

        jdialog("<span class='text-danger'>Sin stock disponible</span>");

        return;

    }

    attr_opt["subtotal"] = parseFloat(labelPrecio.innerText);

    attr_opt["directa"] = $me.name;

    if ( !attr_opt["cup"] )

    {

        attr_opt["cup"] = $parent.id;

        if ( promoItems.length > 1 )

        {

            attr_opt["cup"] += `_${promoItems.join("_")}`;

        }



    }

    //console.log(attr_opt);

    //return;

    $me.insertAdjacentHTML("afterbegin", "<i class='fa fa-spin fa-spinner'></i>");

    $me.setAttribute("disabled", true);

    fetch("!FrontCartMayoristas/setItemCart", {

        "method": "POST",

        "body": new URLSearchParams(attr_opt)

    }).then(function (res) {

        res.json().then(function ($js) {

            delete attr_opt["cup"];

            if ( attr_opt.directa )

            {

                location.href = "./mayoristas/checkout";

                return;

            }

            $me.removeAttribute("disabled");

            $me.firstElementChild.remove();

            if ( !(msj = $js["error"]) )

            {

                msj = "Artículo agregado a la compra";

                get_count_cart();

            }

            document.getElementById('btn-dwn-' + $parent.id).click();

            jdialog(msj);

        });

    });

}