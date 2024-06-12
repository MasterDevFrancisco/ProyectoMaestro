<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Elementos;
use App\Models\Formatos;
use App\Models\RazonSocial;
use App\Models\Servicios;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $razonesSociales = [
            ['razon_social' => 'Razon Social 1', 'nombre_corto' => 'Razon Social 1 SA. de CV.', 'eliminado' => '0'],
            ['razon_social' => 'Razon Social 2', 'nombre_corto' => 'Razon Social 2 SA. de CV.', 'eliminado' => '0'],
        ];

        $serviciosData = [
            ['nombre' => 'Servicio 1', 'razon_social_id' => '1', 'eliminado' => '0'],
            ['nombre' => 'Servicio 2', 'razon_social_id' => '2', 'eliminado' => '0'],
        ];

        $elementosData = [
            ['nombre' => 'Elemento 1', 'campos' => '{"numerico":["$numero 1$","$numero 2$"],"texto":["$texto 1$","$texto 2$"],"fecha":["$fecha 1$","$fecha 2$"]}	', 'eliminado' => '0', 'servicios_id' => '1'],
            ['nombre' => 'Elemento 2', 'campos' => '{"numerico":["$numero 1$","$numero 2$"],"texto":["$texto 1$","$texto 2$"],"fecha":["$fecha 1$","$fecha 2$"]}	', 'eliminado' => '0', 'servicios_id' => '1'],
        ];

        foreach ($razonesSociales as $razon) {
            RazonSocial::factory()->create($razon);
        }

        foreach ($serviciosData as $servicio) {
            Servicios::factory()->create($servicio);
        }

        foreach ($elementosData as $elemento) {
            Elementos::factory()->create($elemento);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); 
    }
}
