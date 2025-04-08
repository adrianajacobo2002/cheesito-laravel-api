<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Mesero;
use Illuminate\Support\Str;

class MeseroSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Juan Martínez',
            'Pedro Gómez',
            'Ana Rodríguez',
            'Luis Hernández',
            'María López',
        ];

        foreach ($nombres as $nombre) {
            $mesero = Mesero::create([
                'nombre' => $nombre,
                'codigo' => '', 
            ]);

            $año = now()->year;
            $iniciales = strtoupper(collect(explode(' ', $nombre))->map(fn($part) => Str::substr($part, 0, 1))->implode(''));
            $codigo = sprintf('%s-%s-%03d', $año, $iniciales, $mesero->id_mesero);

            $mesero->update(['codigo' => $codigo]);
        }
    }
}
