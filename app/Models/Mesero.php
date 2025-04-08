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

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/El_Salvador'))->format('Y-m-d H:i:s');
    }
}
