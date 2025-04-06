<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    protected $primaryKey = 'id_detalle_orden';

    protected $fillable = ['orden_id', 'platillo_id', 'cantidad', 'subtotal', 'estado'];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function platillo()
    {
        return $this->belongsTo(Platillo::class, 'platillo_id');
    }
}
