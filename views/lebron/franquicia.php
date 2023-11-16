<?= $modalBlock; ?>

<div class="onepage">

    <div class="col-md-2"></div>

    <div class="col-md-8">

        <div id="page-content">

            <h2 class="text-center" id="page-title"></h2>

            <div id="tab-1" title="Misión & Visión">

                <div class='form-group'>

                    <h2><span class='recuadro'>Visi&oacute;n</span></h2>

                    <img src='static/images/vision-2.png' style="float: right;width:170px;margin-top: -25px"/>

                    Somos pioneros en ofrecer productos innovadores acordes a las necesidades de nuestros clientes,

                    acompa&ntilde;ados de un servicio eficiente logramos establecernos como una de las principales empresas del pa&iacute;s en prestar un servicio de distribuci&oacute;n &aacute;gil y flexible capaz de garantizar un correcto sistema de distribuci&oacute;n tanto a clientes e inversores.

                    <p class="clearfix">&nbsp;</p>

                </div>

                <div class='form-group'>

                    <h2><span class="recuadro">Misi&oacute;n</span></h2>

                    <img src='static/images/mision-1.png' style="float: left;width:190px;margin-bottom:15px"/>

                    Nos esforzamos por maximizar el retorno a nuestros accionistas y brindar la posibilidad de difundir establecimientos de compra-venta de suplementos e insumos deportivos.

                    LeBron Suplementos pretende potenciar la atención a sus clientes, accionistas y proveedores comercializando productos de primera l&iacute;nea y el ofrecimiento de servicios de excelencia.

                </div>

            </div>

            <div id="tab-2" title="Historia">

                <?php

                $historia = array(

                    '2014' => "Nuestra empresa nace con un capital de $1800",

                    '2016' => "Apertura de nuestra 1° sucursal en San Isidro de Lules.",

                    '2017' => "Abrimos nuestra 2° sucursal en la capital de la provincia.",

                    '2018' => "Llegamos con nuestros productos a provincias como Salta, Jujuy, Catamarca y Santiago del Estero y más allá del NOA hacia todo el país.",

                    '2019' => "Inauguramos nuestra 3° sucursal en la zona del micro centro de San Miguel de Tucumán.",

                    '2020' => "Inauguramos nuestra casa central con dimensiones de cinco veces el tamaño de nuestra 1° sucursal.",

                    '2021' => "Apostamos a iniciar este sistema de franquicias."

                );

                $jsonCount = $i = count($historia);

                ?>

                <div class="" style="padding:20px 15px;display: flex;flex-wrap:wrap;font-size:14px;line-height:20px; background:#f9f9f9;justify-content: center">

                    <?php foreach ($historia as $anio => $texto) : ?>

                        <div id="${anio}" class="col-md-3" style=";margin-bottom:18px;text-align: center">

                            <img src="static/images/franquicia/hito.png" width="185" alt=""/>

                            <i style="position: absolute; left:36%;color:#fff;font-size:26px;top:8%"><?= $anio ?></i>

                            <div style="height:35px;border-left: 1px dashed #000;margin:0 48% 8px;width:2px"></div>

                            <p style="border-top: 1px solid #0b0b0b;background:#fff;padding:5px"><?= $texto ?></p>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <div id="tab-3" title="Bases y Condiciones">

                <ul class="bases">

                    <li>Uso de la marca y Know How</li>

                    <li>Derecho a las estrategias y procesos para el franquiciado</li>

                    <li>Evaluación de los puntos de venta: Incluye exclusividad de la franquicia en el territorio y de comercialización y distribución al franquiciado</li>

                    <li>Evaluación inicial de la nueva unidad de negocio: requisitos y personal</li>

                    <li>Asistencia pre-apertura y en los primeros días posteriores a la misma</li>

                    <li>Env&iacute;o de Stock y asistencia en el manejo del mismo</li>

                    <li>Asistencia en el manejo del local comercial</li>

                    <li>Asesoría y supervisión de la instalación en general y los requisitos preestablecidos por la marca</li>

                    <li>Disponibilidad de canales para la comunicación entre la unidad de negocio (franquicia) y la unidad central (franquiciante)</li>

                    <li>Indicaciones y sugerencias que aseguren la estandarización y coherencia de la franquicia con los parámetros estéticos de la marca</li>

                    <li>Capacitación inicial y futuras actividades de entrenamiento del personal perteneciente a la franquicia</li>

                    <li>Capacitación inicial y actualización de productos e insumos nuevos</li>

                </ul>

            </div>

            <div id="tab-4" title="Contacto">

                <div class="text-center">

                    <br/>

                </div>

                <?= $contactForm ?>

            </div>

        </div>

    </div>

</div>

<style>

    @import url('https://fonts.googleapis.com/css2?family=Lato&display=swap');



    .onepage {

        font-family: 'Raleway', sans-serif;

        font-weight: 400;

        font-size: 18px;

        line-height: 1.8em;

    }



    #dv-monto-franquicia .panel-heading {

        background: #0B8CCE;

        font-size: 22px;

        font-weight: 600;

        color: #0b0b0b;

        text-align: center;

    }



    #page-content {

        padding: 70px 15px 35px;

    }



    h1 {

        font-size: 65px;

        font-weight: bold;

        margin: 0 0 25px 0;

        text-shadow: 2px 3px 3px #ccc;

    }



    h2 {

        margin: 35px 0 20px;

        font-size: 50px;

    }



    .recuadro {

        background: #ff0000;

        padding: 0 35px;

        color: #fff;

        border: 1px solid #0b0b0b;

        /*border-radius: 0 25px 25px 0;*/

        border-radius: 25px;

        box-shadow: 2px 3px 3px #ccc;

    }



    #aa-header {

        min-height: auto;

        background-image: url("static/images/franquicia/header-1.jpg");

        background-position: top left;

        background-repeat: no-repeat;

        background-size: cover;

        color: #fff;

        font-size: 19px;

        opacity: 0.8;

    }



    #inner-div {

        padding: 15px;

    }



    #elementor-container {

        display: flex;

        margin-right: auto;

        margin-left: auto;

        position: relative;

        align-items: center;

        min-height: 90vh;

        text-align: center;

        justify-content: center;

        flex-wrap: wrap;

    }



    #banner-caption {

        display: block;

        margin-bottom: 150px;

        color: #ffffff;

        font-weight: 600;

        background: #0b0b0b;

        opacity:.7;

        padding: 12px 10px;

    }



    .circles {

        padding: 80px 90px;

        color: #fff;

        font-weight: bold;

        position: relative;

        background-color: #00000044;

        border: 1px solid #fff;

        border-radius: 50%;

        line-height: 1.3;

        cursor: pointer;

        text-align: center;

        text-transform: uppercase;

        transition: background ease-in-out 200ms;

    }



    .circles:hover, .circles.active {

        background: #ff0000;

        color: #fff;

    }



    .circles .circles-text {

        position: absolute;

        font-size: 16px;

        top: 50%;

        left: 50%;

        -webkit-transform: translate(-50%, -50%);

        -ms-transform: translate(-50%, -50%);

        transform: translate(-50%, -50%);

    }



    ul.bases li {

        margin: 0;

        padding: 12px 70px;

        list-style: none;

        background-image: url("static/images/arrow-1.jpg");

        background-repeat: no-repeat;

        background-position: left center;

        background-size: 60px;

    }



    .hito {

        background-image: url("static/images/hito.png");

        background-size: contain;

        background-repeat: no-repeat;

        height: 62px;

        line-height: 50px;

        width: 185px;

        font-size: 25px;

        color: #fff;

        text-align: center;

    }



    .time-line {

        background: #eee;

        height: 53px;

        width: 100%;

        text-align: center;

    }



    @media screen and (max-width: 600px) {

        #page-title {

            font-size: 38px;

        }

    }

</style>

<script>

    $options = {1: "Misión y Visión", 2: "Historia", 3: "Bases y Condiciones", 4: "Contactanos"};

    htmlHeader = `<div id="inner-div">`;

    htmlHeader += "<a href='./'><img src='<?=$logo_src?>' style='padding:2px 8px;background:#0b0b0b;width:200px;border-radius:6px' alt='LeBron Suplementos'/></a>";

    htmlHeader += `</div>`;

    htmlHeader += "<div id='elementor-container'>";

    htmlHeader += "<div class='col-md-8'>";

    htmlHeader += "<div class='form-group' id='banner-caption''>";

    htmlHeader += "<h1>FRANQUICIA</h1>";

    htmlHeader += "LeBron Suplementos es una empresa l&iacute;der en el NOA, se distingue de la competencia por la calidad e innovaci&oacute;n de los productos y servicios dentro del rubro deportivo y fitness. LeBron Suplementos conforma un excelente lugar de trabajo, donde se tiene en cuenta el bienestar y la permanente capacitación del personal para maximizar la atención a su público.\n";

    htmlHeader += "<p class='clearfix'></p>";

    htmlHeader += "</div>";

    htmlHeader += "<div class='row' style='justify-content:center;display: flex;flex-wrap: wrap;'>";

    for (option in $options)

    {

        htmlHeader += "<div class='col-md-3' style='min-height: 200px'>";

        htmlHeader += `<a href='javascript:void(0)' id='${option}' class='circles'><span class='circles-text'>${$options[option]}</span></a>`;

        htmlHeader += "</div>";

    }

    htmlHeader += "</div>";

    htmlHeader += "</div>";

    htmlHeader += "<div class='clearfix'></div>";

    htmlHeader += "</div>";

    document.getElementById('aa-header').innerHTML = htmlHeader;

    Array.from(document.getElementsByClassName('circles')).forEach(function (aa) {

        aa.onclick = function () {

            if ( (active = document.querySelector(".active")) )

            {

                active.classList.remove("active");

            }

            document.querySelectorAll('[id^="tab-"]').forEach(function (tab) {

                tab.classList.add("hidden");

            });

            this.classList.add("active");

            tabTitle = (this.id > 1) ? `<span class="recuadro">${this.innerText}</span>` : "";

            document.getElementById('page-title').innerHTML = tabTitle;

            document.getElementById(`tab-${this.id}`).classList.remove("hidden");

            //(pageContent = document.getElementById('page-content')).setHtmlContent(contenido);

            document.getElementById('page-content').scrollIntoView({behavior: "smooth"});

        };

    });



    if ( (tabContacto = document.getElementById('4')) )

    {

        tabContacto.click();

    }

    ["body-header", "cartbox"].forEach(function (elm) {

        if ( (div = document.getElementById(elm)) )

        {

            div.remove();

        }

    });

</script>