<?php

class Proveedor extends Persona
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query->where('rol', self::rolProveedor);
        });
    }
}