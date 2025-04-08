<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platillo extends Model
{
    protected $primaryKey = 'id_platillo';

    protected $fillable = ['nombre', 'precio', 'tipo', 'imagen_url'];

    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'platillo_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'platillo_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/El_Salvador'))->format('Y-m-d H:i:s');
    }
}
