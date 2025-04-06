<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $primaryKey = 'id_factura';

    protected $fillable = ['orden_id', 'subtotal', 'propina', 'total'];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}
