<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';
    
    protected $primaryKey = 'id_inventario';

    protected $fillable = ['platillo_id', 'cantidad_disponible'];

    public function platillo()
    {
        return $this->belongsTo(Platillo::class, 'platillo_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/El_Salvador'))->format('Y-m-d H:i:s');
    }
}
