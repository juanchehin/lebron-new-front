(function(d, s, id) {

    var js, fjs = d.getElementsByTagName(s)[0];

    if (d.getElementById(id))

    {

        return;

    }

    js = d.createElement(s);

    js.id = id;

    js.src = "//connect.facebook.net/es_LA/sdk.js";

    js.crossorigin = "anonymous";

    fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));



const sesAuth = "auth";



function checkLoginState()

{

    return JSON.parse(sessionStorage.getItem(sesAuth) || '{}');

    FB.getLoginStatus(function(response) {

        status_callback(response);

    });

}



function facebook_login(callback)

{

    FB.login(function(response) {

        callback.call(this, response);

    }, {

        "scope": "public_profile,email",

        "auth_type": "rerequest"

    });

}



function status_callback(response)

{

    if (response.status === "connected")

    {

        FB.api("/me", { "fields": "last_name,first_name,gender,email,birthday" }, function(data) {

            data["fb_token"] = response.authResponse.accessToken;

            fetch('!FrontUsuario/continuar', {

                "method": "POST",

                "body": new URLSearchParams(data)

            }).then(function(res) {

                //console.log(res.json())

                res.json().then(function($json) {

                    if (($error = $json["error"]))

                    {

                        jdialog($error, true);

                        return;

                    }

                    sessionStorage.setItem(sesAuth, true);

                    if (typeof after_submit === "function")

                    {

                        after_submit($json);

                    }

                });

            });

        });

    }

}



window.fbAsyncInit = function() {

    FB.init({

        "appId": '355127731747742',

        "autoLogAppEvents": false,

        "xfbml": true,

        "version": 'v3.3'

    });



    if (!sessionStorage.getItem(sesAuth))

    {

        checkLoginState();

    }

};


// Parametros que se toman del localstorage
// Son los parametros de marca/categoria
var params = JSON.parse(sessionStorage.getItem("cache") || '{}');



function set_param($key, $value) {

    if (typeof $key === "object") {
        Object.assign(params, $key);
    } else {

        // params[$key] = $value;
        if ($key != 'p') {
            params = {};
        }

        if ($key == 'p') {
            params["p"] = $value;
        }

        if ($key == 'marca') {
            params["marca"] = $value;
        }

        if ($key == 'ctg') {
            params["ctg"] = $value;
        }
        if ($key == 'q') {
            params["q"] = $value;
        }

    }

    url = Object.keys(params).map(function(k) {

        return encodeURIComponent(k)

    }).join("&");

    history.pushState(null, null, location.href.replace(/\?.+/gi, "") + "?" + url);

    sessionStorage.setItem("cache", JSON.stringify(params));

    get_articulo();

}



function paginar(ref)

{

    var page = ref.getAttribute('href').replace(/.+=/g, "");

    ref.setAttribute('href', 'javascript:void(0)');

    set_param("p", page);

    return false;

}



const currentClass = "current";



// =================================================================
// Function que se dispara al hacer clic sobre una categoria/marca
// 'opt' es el <div> completo al cual se le hizo clic
// =================================================================

function set_tag($opt) {

    // Listado de los tags
    const tituloCategoria = document.getElementById('list-title');

    // Obtengo los atributos
    // Si es marca/catagoria y su id
    attr = $opt.id.split("-");

    // Key = marca/ctg
    // value = id de la marca/ctg
    let key = attr[0],
        value = attr[1],
        option_text;

    tituloCategoria.scrollIntoView();

    document.getElementById('dv-filter-group').scrollIntoView();

    // Foreach de todas las categorias
    document.querySelectorAll(`[id^="${key}"]`).forEach(function(hasClass) {

        hasClass.classList.remove(currentClass);

    });

    delete params["p"];

    if ((tagElm = document.getElementById(key + "-"))) {

        tagElm.parentElement.remove();

        delete params[key];
    }

    if (value.trim()) {

        option_text = $opt.textContent;

        tag = `<div>
                <h4 class="hh-tag">${option_text}&nbsp;<span  id="${key}-" onclick='set_tag(this)'>&times;</span></h4>
                </div>`;

        // Agrego un nuevo tag al final del ultimo
        tituloCategoria.innerHTML = tag;
    }

    set_param(key, value);

}



function get_articulo()

{

    const articulosContainer = document.getElementById('articulos-list');

    let $spin = document.createElement("div");

    $spin.style["background"] = "#fff";

    $spin.style["opacity"] = ".5";

    $spin.style["position"] = "absolute";

    $spin.style["z-index"] = "10";

    $spin.style["left"] = "0";

    $spin.style["right"] = "0";

    $spin.style["display"] = "flex";

    $spin.style["justify-content"] = "center";

    $spin.style["align-items"] = "center";

    $spin.style["height"] = articulosContainer.offsetHeight + "px";

    $spin.innerHTML = "<img src='static/images/spin.gif' alt='Espere...' width='220'>";

    articulosContainer.prepend($spin);

    // delete params.marca;

    fetch("!FrontInicio/articulos", {

        "method": "POST",

        "body": new URLSearchParams(params)

    }).then(function(rta) {

        rta.text().then(function($json) {

            articulosContainer.innerHTML = $json;

            if (params["p"])

            {

                document.getElementById('list-title').scrollIntoView();

            }

        })

    });

}