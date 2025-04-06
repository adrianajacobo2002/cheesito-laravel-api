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
}
