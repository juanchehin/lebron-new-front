<?php



class Cliente extends Persona

{

    public static function boot()

    {

        parent::boot();

        static::addGlobalScope(function ($query) {

            $query->whereIn('rol', [self::rolCliente, self::ROL_USUARIO]);

        });

    }



    public function getStrDireccionAttribute()

    {

        $direcciones = Direccion::where('owner', $this->attributes['id'])->get();

        $mensaje = "<ul>";

        foreach ($direcciones as $direccion)

        {

            $arr = array();

            $addr = $direccion->array_body;

            /*$addr = array(

                'calle' => mb_strtoupper($direccion['street_name']),

                'altura' => $direccion['street_number'],

                'cp' => $direccion['zip_code'],

                'piso' => $direccion['floor'],

                'depto' => $direccion['apartment'],

                'ciudad' => mb_strtoupper($direccion['city']['name']),

                'provincia' => mb_strtoupper($direccion['state']['name'])

            );*/

            $arr[] = mb_strtoupper($addr['calle']) . " " . $addr['numero'];

            $arr[] = mb_strtoupper($direccion['nombre']) . " ({$direccion->valor})";

            $arr[] = mb_strtoupper($addr['provincia']);

            $arr[] = "<br/>Referencia: " . (mb_strtoupper($addr['referencia'] ?: "-"));

            $full_addr = implode(". ", $arr);

            #--

            $mensaje .= "<li>Dirección: {$full_addr}. Tel: {$addr['telefono']}</li>";

        }

        $mensaje .= "</ul>";

        return $mensaje;

    }

}