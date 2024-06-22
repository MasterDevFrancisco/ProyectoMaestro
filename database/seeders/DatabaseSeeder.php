<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Elementos;
use App\Models\Formatos;
use App\Models\RazonSocial;
use App\Models\Servicios;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* Roles */
        Role::create(['name' => 'cliente']);
        $this->call(RolesAndPermissionsSeeder::class);

        $razonesSociales = [
            ['razon_social' => 'SGM', 'nombre_corto' => 'SGM SA. de CV.', 'eliminado' => '0'],
            ['razon_social' => 'ASEA', 'nombre_corto' => 'ASEA SA. de CV.', 'eliminado' => '0'],
        ];

        $serviciosData = [
            ['nombre' => 'ASEA-00-037 - Aviso de Cancelacion de la Poliza de Seguro del Sector Hidrocarburos', 'razon_social_id' => '2', 'eliminado' => '0'],
            ['nombre' => 'ASEA-00-034 - Notifica la Modificaion de tu Piliza de Seguro', 'razon_social_id' => '2', 'eliminado' => '0'],
            ['nombre' => '1. CONFORMACIÓN DEL SISTEMA DE GESTIÓN DE MEDICIONES PARA EXPENDIO   ', 'razon_social_id' => '1', 'eliminado' => '0'],
            ['nombre' => '2. CONFORMACIÓN DEL SISTEMA DE GESTIÓN DE MEDICIONES PARA TRANSPORTE', 'razon_social_id' => '1', 'eliminado' => '0'],
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

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'superadmin@cmx.com',
            'password' => bcrypt('toor')
        ]);
        $admin->assignRole('admin');

        // Crear usuarios coordinadores
        $coordinadores = [
            ['name' => 'Coordinador SGM', 'email' => 'coordinador@sgm.com', 'password' => bcrypt('password123'), 'razon_social_id' => '1'],
            ['name' => 'Coordinador ASEA', 'email' => 'coordinador@asea.com', 'password' => bcrypt('password123'), 'razon_social_id' => '2'],
        ];

        foreach ($coordinadores as $coordinador) {
            $user = User::create($coordinador);
            $user->assignRole('coordinador');
        }
    }
}
