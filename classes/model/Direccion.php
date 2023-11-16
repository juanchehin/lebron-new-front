<?php


class Direccion extends Componente
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query->where('tipo', "direccion");
        });
    }

}