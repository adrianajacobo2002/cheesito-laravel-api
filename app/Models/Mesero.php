<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesero extends Model
{
    protected $primaryKey = 'id_mesero';

    protected $fillable = ['nombre', 'codigo'];

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'mesero_id');
    }
}
