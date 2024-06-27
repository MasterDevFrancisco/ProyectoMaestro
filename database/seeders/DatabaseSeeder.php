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
            ['nombre' => 'Elemento ASEA 1', 'campos' => '{"formula":[],"texto":["<$1{Nombre}1$>","<$2{Apellidos}2$>","<$3{Direccion}3$>"]}', 'eliminado' => '0', 'servicios_id' => '1'],
            ['nombre' => 'Elemento ASEA 2', 'campos' => '{"formula":[],"texto":["<$1{Coordinador}1$>","<$2{Responsable}2$>"]}', 'eliminado' => '0', 'servicios_id' => '1'],
            ['nombre' => 'Elemento SGM 1', 'campos' => '{"formula":[],"texto":["<$1{Normativa}1$>","<$2{Folio}2$>","<$3{Clausula}3$>"]}', 'eliminado' => '0', 'servicios_id' => '3'],
            ['nombre' => 'Elemento SGM 2', 'campos' => '{"formula":[],"texto":["<$1{Recibio}1$>","<$2{Entrego}2$>","<$3{Autorizo}3$>"]}', 'eliminado' => '0', 'servicios_id' => '3'],
        ];        

        $coordinadores = [
            ['name' => 'Coordinador SGM', 'email' => 'coordinador@sgm.com', 'password' => bcrypt('password123'), 'razon_social_id' => '1'],
            ['name' => 'Coordinador ASEA', 'email' => 'coordinador@asea.com', 'password' => bcrypt('password123'), 'razon_social_id' => '2'],
        ];

       
        foreach ($razonesSociales as $razon) {
            RazonSocial::factory()->create($razon);
        }
        foreach ($coordinadores as $coordinador) {
            $user = User::create($coordinador);
            $user->assignRole('coordinador');
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

        // Crear usuarios de prueba con rol 'cliente'
        $razonSGM = RazonSocial::where('nombre_corto', 'SGM SA. de CV.')->first();
        $razonASEA = RazonSocial::where('nombre_corto', 'ASEA SA. de CV.')->first();

        $usuariosPrueba = [
            ['name' => 'Cliente SGM', 'email' => 'cliente.sgm@ejemplo.com', 'password' => bcrypt('clienteSGM123'), 'razon_social_id' => $razonSGM->id],
            ['name' => 'Cliente ASEA', 'email' => 'cliente.asea@ejemplo.com', 'password' => bcrypt('clienteASEA123'), 'razon_social_id' => $razonASEA->id],
        ];

        foreach ($usuariosPrueba as $usuarioPrueba) {
            $user = User::create($usuarioPrueba);
            $user->assignRole('cliente');
        }
    }
}
