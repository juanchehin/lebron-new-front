<?php



class ExportOpts

{

    private $pdf_url;

    private $excel_url;

    private $derecha = true;



    /**

     * @param mixed $pdf_url

     */

    public function setPdfUrl($pdf_url)

    {

        $this->pdf_url = $pdf_url;

        return;

    }



    /**

     * @param mixed $excel_url

     */

    public function setExcelUrl($excel_url)

    {

        $this->excel_url = $excel_url;

        return;

    }



    /**

     * @param null $posicion

     */

    public function setNoDerecha()

    {

        $this->derecha = false;

        return;

    }



    public function drawControl()

    {

        ob_start();

        ?>

        <div class="dropdown" style="<?php if ( $this->derecha ) echo 'float:right'; ?>">

            <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-download"></i> Exportar <span class="caret"></span></button>

            <ul class="dropdown-menu <?php if ( $this->derecha ) echo 'dropdown-menu-right'; ?>">

                <?php if ( $this->pdf_url ) : ?>

                    <li><a id="ec-a-pdf" href="<?= $this->pdf_url ?>" target="_blank" title="PDF">Archivo (.pdf)</a></li>

                <?php endif; ?>

                <?php if ( $this->excel_url ) : ?>

                    <li><a href="<?= $this->excel_url ?>" title="Excel">Planilla (.xls)</a></li>

                <?php endif; ?>

            </ul>

        </div>

        <?php



        return ob_get_clean();

    }



    public static function exportar($html, $pdf = false, $pdf_landscape = false, $pdf_adjunto = 0)

    {

        $file_name = "registros_" . date("d-m-Y_H.i.s");

        if ( $pdf )

        {

            $contxt = stream_context_create([

                'ssl' => array(

                    'verify_peer' => false,

                    'verify_peer_name' => false,

                    'allow_self_signed' => true

                )

            ]);

            $options = new \Dompdf\Options();

            $options->setIsRemoteEnabled(true);

            $dompdf = new \Dompdf\Dompdf($options);

            $dompdf->setHttpContext($contxt);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', $pdf_landscape ? "landscape" : "portrait");

            $dompdf->render();

            $dompdf->stream($file_name, array('Attachment' => $pdf_adjunto));

        }

        else

        {

            //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Type: application/vnd.ms-excel; charset=utf8");

            //header("Content-Disposition: attachment;filename={$file_name}.xls");

            header("Content-Disposition:inline;filename={$file_name}.xls");

            header('Cache-Control: max-age=0');

            //$this->setParametros('result', $solicitantes);

            die($html);

        }

        exit;

    }

    // ***** Barcode ||| *******
    public static function exportarBarcode($html, $pdf = false, $pdf_landscape = false, $pdf_adjunto = 0)

    {

        $file_name = "barcode_" . date("d-m-Y_H.i.s");

        if ( $pdf )

        {

            $contxt = stream_context_create([

                'ssl' => array(

                    'verify_peer' => false,

                    'verify_peer_name' => false,

                    'allow_self_signed' => true

                )

            ]);

            $options = new \Dompdf\Options();

            $options->setIsRemoteEnabled(true);

            $dompdf = new \Dompdf\Dompdf($options);

            $dompdf->setHttpContext($contxt);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A5', $pdf_landscape ? "landscape" : "portrait");

            $dompdf->render();

            $dompdf->stream($file_name, array('Attachment' => $pdf_adjunto));

        }

        else

        {

            //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Type: application/vnd.ms-excel; charset=utf8");

            //header("Content-Disposition: attachment;filename={$file_name}.xls");

            header("Content-Disposition:inline;filename={$file_name}.xls");

            header('Cache-Control: max-age=0');

            //$this->setParametros('result', $solicitantes);

            die($html);

        }

        exit;

    }



    public static function yahooImagenOnline($search, $pages = 2)

    {

        $urls = array();

//		$pages = ZPHP::get_config('image.search_pages');

        //header('Content-type: text/html');



        for ($i = 1; $i <= $pages; $i++)

        {

            foreach ((array)$search as $string)

            {



                $search_encoded = urlencode($string);



//				$url = "https://ar.images.search.yahoo.com/search/images;_ylt=A2KLj9EfK5RXGDkAjR2s9Qt.;_ylc=X1MDMjExNDcxMzAwNARfcgMyBGJjawMyZnVldHQ5YnA4YW5rJTI2YiUzRDMlMjZzJTNEYmEEZnIDBGdwcmlkA0MwWlg0ajU1UndDcGxqcmF5WkpHRUEEbXRlc3RpZANudWxsBG5fc3VnZwMxMARvcmlnaW4DYXIuaW1hZ2VzLnNlYXJjaC55YWhvby5jb20EcG9zAzAEcHFzdHIDBHBxc3RybAMEcXN0cmwDMTgEcXVlcnkDdmlzYSBwcm9tb2Npb24gcG5nBHRfc3RtcAMxNDY5MzI4MTY3BHZ0ZXN0aWQDbnVsbA--?gprid=C0ZX4j55RwCpljrayZJGEA&pvid=ThXwwjcyLjMn.d3qV5Qq9A5pMTkwLgAAAACe_Mjz&fr2=sb-top-ar.images.search.yahoo.com&p={$search_encoded}&ei=UTF-8&iscqry=&fr=sfp";

                $url = "https://images.search.yahoo.com/search/images;_ylt=AwrBTzCQv1ZXVrgA59er9Qt.;_ylu=X3oDMTB0N2Noc21lBGNvbG8DYmYxBHBvcwMxBHZ0aWQDBHNlYwNwaXZz?p={$search_encoded}&fr=yfp-t-726&imgsz=large&fr2=piv-web&vm=p&b=" . ($i * 60);

                //			$url_request = new URLRequest($url);

                //			$contents = $url_request->request();

                $contents = file_get_contents($url);



                //UFunciones::varDump($contents, false);

                //			continue;



//				preg_match_all("#(?i)data\\-src\\=\\'(?P<url>.+?)\\'#", $contents, $matches);

//				preg_match_all("#(?i)href\\=\\'.*?\\&imgurl\\=(?P<url>.+?)\\&#", $contents, $matches);



                preg_match_all("#(?i)\\<a.+?href\\=\\'.*?\\&imgurl\\=(?P<url>.+?)\\&.+?'.*?\\<img.+?data\\-src\\=\\'(?P<thumb_url>.+?)\\'#", $contents, $matches);

                //preg_match_all("#\<img\s+alt=\".*\"\s+(height=\"\d+\"\s+)?src\=\"(?P<thumb_url>.*?)\"#", $contents, $matches);



                $match_urls = (array)$matches['url'];

                foreach ($match_urls as $index => $url)

                {

                    //$url = StringHelper::put_prefix(urldecode($url), 'http://', true);

                    $thumb = str_replace('w=300&h=300', "", $matches['thumb_url'][$index]);



                    $urls[] = array(

                        'title' => "http://" . urldecode($url),

                        'image_src' => $thumb,

                        'big_image' => $thumb

                    );

                }

            }



        }

//		die();

//		$urls = array_unique($urls);

        return $urls;

    }



    public static function ruleta()

    {

        ob_start();

        ?>

        <input type="button" value="Hacer girar" style="float:left;" id='spin' />

        <canvas id="canvas" width="500" height="500"></canvas>

        <script>

            var options = ["Granizado", "Vuelve a intentarlo", "Shot", "Gorra", "Camiseta", "Vuelve a intentarlo"];



            var startAngle = 0;

            var arc = Math.PI / (options.length / 2);

            var spinTimeout = null;



            var spinArcStart = 10;

            var spinTime = 0;

            var spinTimeTotal = 0;



            var ctx;



            document.getElementById("spin").addEventListener("click", spin);



            function byte2Hex(n) {

                var nybHexString = "0123456789ABCDEF";

                return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);

            }



            function RGB2Color(r,g,b) {

                return '#' + byte2Hex(r) + byte2Hex(g) + byte2Hex(b);

            }



            function getColor(item, maxitem) {

                var phase = 0;

                var center = 128;

                var width = 127;

                var frequency = Math.PI*2/maxitem;

                

                red   = Math.sin(frequency*item+2+phase) * width + center;

                green = Math.sin(frequency*item+0+phase) * width + center;

                blue  = Math.sin(frequency*item+4+phase) * width + center;

                

                return RGB2Color(red,green,blue);

            }



            function drawRouletteWheel() {

                var canvas = document.getElementById("canvas");

                if (canvas.getContext) {

                    var outsideRadius = 200;

                    var textRadius = 160;

                    var insideRadius = 125;



                    ctx = canvas.getContext("2d");

                    ctx.clearRect(0,0,500,500);



                    ctx.strokeStyle = "black";

                    ctx.lineWidth = 2;



                    ctx.font = 'bold 12px Helvetica, Arial';



                    for(var i = 0; i < options.length; i++) {

                    var angle = startAngle + i * arc;

                    //ctx.fillStyle = colors[i];

                    ctx.fillStyle = getColor(i, options.length);



                    ctx.beginPath();

                    ctx.arc(250, 250, outsideRadius, angle, angle + arc, false);

                    ctx.arc(250, 250, insideRadius, angle + arc, angle, true);

                    ctx.stroke();

                    ctx.fill();



                    ctx.save();

                    ctx.shadowOffsetX = -1;

                    ctx.shadowOffsetY = -1;

                    ctx.shadowBlur    = 0;

                    ctx.shadowColor   = "rgb(220,220,220)";

                    ctx.fillStyle = "black";

                    ctx.translate(250 + Math.cos(angle + arc / 2) * textRadius, 

                                    250 + Math.sin(angle + arc / 2) * textRadius);

                    ctx.rotate(angle + arc / 2 + Math.PI / 2);

                    var text = options[i];

                    ctx.fillText(text, -ctx.measureText(text).width / 2, 0);

                    ctx.restore();

                    } 



                    //Arrow

                    ctx.fillStyle = "black";

                    ctx.beginPath();

                    ctx.moveTo(250 - 4, 250 - (outsideRadius + 5));

                    ctx.lineTo(250 + 4, 250 - (outsideRadius + 5));

                    ctx.lineTo(250 + 4, 250 - (outsideRadius - 5));

                    ctx.lineTo(250 + 9, 250 - (outsideRadius - 5));

                    ctx.lineTo(250 + 0, 250 - (outsideRadius - 13));

                    ctx.lineTo(250 - 9, 250 - (outsideRadius - 5));

                    ctx.lineTo(250 - 4, 250 - (outsideRadius - 5));

                    ctx.lineTo(250 - 4, 250 - (outsideRadius + 5));

                    ctx.fill();

                }

            }



            function spin() {

                spinAngleStart = Math.random() * 10 + 10;

                spinTime = 0;

                spinTimeTotal = Math.random() * 3 + 4 * 1000;

                rotateWheel();

            }



            function rotateWheel() {

                spinTime += 30;

                if(spinTime >= spinTimeTotal) {

                    stopRotateWheel();

                    return;

                }

                var spinAngle = spinAngleStart - easeOut(spinTime, 0, spinAngleStart, spinTimeTotal);

                startAngle += (spinAngle * Math.PI / 180);

                drawRouletteWheel();

                spinTimeout = setTimeout('rotateWheel()', 30);

            }



            function stopRotateWheel() {

                clearTimeout(spinTimeout);

                var degrees = startAngle * 180 / Math.PI + 90;

                var arcd = arc * 180 / Math.PI;

                var index = Math.floor((360 - degrees % 360) / arcd);

                ctx.save();

                ctx.font = 'bold 30px Helvetica, Arial';

                var text = options[index]

                ctx.fillText(text, 250 - ctx.measureText(text).width / 2, 250 + 10);

                ctx.restore();

            }



            function easeOut(t, b, c, d) {

                var ts = (t/=d)*t;

                var tc = ts*t;

                return b+c*(tc + -3*ts + 3*t);

            }



            drawRouletteWheel();

        </script>

        <?php

        return ob_get_clean();

    }

}