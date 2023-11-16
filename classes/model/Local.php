<?php



class Local

{

    const deposito = 1;

    const negocioLules = 2;

    const depositoMitre = 4;

    const depositoSecco = 5;

    const mitreNegocio = 6;

    const bsas42 = 7;

    const marceloCordobaGym = 8;

    const warriorGym = 9;

    const ventaPagina = 10;

    const optimusGym = 11;

    const local25 = 12;

    const tamaraGym = 13;

    const fabian = 14;

    public static $_LOCALES = array(

        //self::deposito => "Depósito Centro",

        self::negocioLules => "Lules",

        //3 => "Yerba Buena,

        //self::depositoMitre => "Depósito Mitre",

        self::mitreNegocio => "Negocio Mitre",

        //self::depositoSecco => "Deposito Secco OK",

        self::bsas42 => "Buenos Aires 42",

        self::local25 => "Local 25 de Mayo",

        // self::marceloCordobaGym => "Marcelo Córdoba GyM",

        self::optimusGym => "Optimus GYM",

        self::tamaraGym => "Tamara GyM",

        self::warriorGym => "Warrior GyM",

        // self::fabian => "Fabián"


    );



    public static $_puntosVenta = array(

        self::mitreNegocio, 

        self::negocioLules, 

        self::bsas42, 

        self::local25, 

        self::marceloCordobaGym, 

        self::optimusGym, 

        self::tamaraGym, 

        self::warriorGym,

        self::fabian

    );



    public static function nombreLocal($id_local)

    {

        static::$_LOCALES[self::ventaPagina] = "E-commerce";

        return static::$_LOCALES[$id_local];

    }



    static public function esDeposito($id_local)

    {

        return !in_array($id_local, static::$_puntosVenta);

    }

}