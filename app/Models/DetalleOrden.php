<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    protected $table = 'detalle_orden';

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

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/El_Salvador'))->format('Y-m-d H:i:s');
    }
    
}
