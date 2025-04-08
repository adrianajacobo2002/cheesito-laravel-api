<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $primaryKey = 'id_mesa';

    protected $fillable = ['num_mesa', 'estado', 'capacidad'];

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'mesa_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/El_Salvador'))->format('Y-m-d H:i:s');
    }
}
