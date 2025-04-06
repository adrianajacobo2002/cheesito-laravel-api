<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $primaryKey = 'id_orden';

    protected $fillable = [
        'fecha', 'estado', 'nombre_cliente', 'mesero_id', 'mesa_id'
    ];

    public function mesero()
    {
        return $this->belongsTo(Mesero::class, 'mesero_id');
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'orden_id');
    }
}
