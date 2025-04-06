<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $primaryKey = 'id_inventario';

    protected $fillable = ['platillo_id', 'cantidad_disponible'];

    public function platillo()
    {
        return $this->belongsTo(Platillo::class, 'platillo_id');
    }
}
