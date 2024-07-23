<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Elementos;
use App\Models\RazonSocial;
use App\Models\Servicios;
use App\Models\Tablas;
use App\Models\Campos;
use App\Models\Data;
use App\Models\Formatos;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        Role::create(['name' => 'cliente']);
        $this->call(RolesAndPermissionsSeeder::class);

        // Datos
        $razonesSociales = [
            ['razon_social' => 'SGM', 'nombre_corto' => 'SGM SA. de CV.', 'eliminado' => '0'],
            ['razon_social' => 'ASEA', 'nombre_corto' => 'ASEA SA. de CV.', 'eliminado' => '0'],
        ];

        $serviciosData = [
            ['nombre' => 'ASEA-00-037 - Aviso de Cancelacion de la Poliza de Seguro del Sector Hidrocarburos', 'razon_social_id' => '2', 'eliminado' => '0'],
            ['nombre' => 'ASEA-00-034 - Notifica la Modificacion de tu Poliza de Seguro', 'razon_social_id' => '2', 'eliminado' => '0'],
            ['nombre' => '1. CONFORMACIÓN DEL SISTEMA DE GESTIÓN DE MEDICIONES PARA EXPENDIO', 'razon_social_id' => '1', 'eliminado' => '0'],
            ['nombre' => '2. CONFORMACIÓN DEL SISTEMA DE GESTIÓN DE MEDICIONES PARA TRANSPORTE', 'razon_social_id' => '1', 'eliminado' => '0'],
        ];

        $elementosData = [
            ['nombre' => 'Elemento ASEA 1', 'eliminado' => '0', 'servicios_id' => '1'],
            ['nombre' => 'Elemento ASEA 2', 'eliminado' => '0', 'servicios_id' => '1'],
            ['nombre' => 'Elemento SGM 1', 'eliminado' => '0', 'servicios_id' => '3'],
            ['nombre' => 'Elemento SGM 2', 'eliminado' => '0', 'servicios_id' => '3'],
        ];

        $tablasData = [
            ['nombre' => 'Tabla 1', 'formatos_id' => '1'],
            ['nombre' => 'Tabla 2', 'formatos_id' => '2'],
        ];

        $camposData = [
            ['tablas_id' => '1', 'nombre_columna' => 'Campo 1', 'status' => 'activo', 'linkname' => 'campo_1'],
            ['tablas_id' => '1', 'nombre_columna' => 'Campo 2', 'status' => 'activo', 'linkname' => 'campo_2'],
            ['tablas_id' => '2', 'nombre_columna' => 'Campo 3', 'status' => 'inactivo', 'linkname' => 'campo_3'],
            ['tablas_id' => '2', 'nombre_columna' => 'Campo 4', 'status' => 'activo', 'linkname' => 'campo_4'],
        ];

        $dataEntries = [
            ['rowID' => '1', 'valor' => 'Valor 1', 'campos_id' => '1', 'users_id' => '1'],
            ['rowID' => '2', 'valor' => 'Valor 2', 'campos_id' => '2', 'users_id' => '1'],
            ['rowID' => '3', 'valor' => 'Valor 3', 'campos_id' => '3', 'users_id' => '2'],
            ['rowID' => '4', 'valor' => 'Valor 4', 'campos_id' => '4', 'users_id' => '2'],
        ];

        $coordinadores = [
            ['name' => 'Coordinador SGM', 'email' => 'coordinador@sgm.com', 'password' => bcrypt('password123'), 'razon_social_id' => '1'],
            ['name' => 'Coordinador ASEA', 'email' => 'coordinador@asea.com', 'password' => bcrypt('password123'), 'razon_social_id' => '2'],
        ];

        // Datos de Formatos
        $formatosData = [
            ['nombre' => 'Formato 1', 'ruta_pdf' => '', 'eliminado' => '0', 'elementos_id' => '1'],
            ['nombre' => 'Formato 2', 'ruta_pdf' => '', 'eliminado' => '0', 'elementos_id' => '2'],
            ['nombre' => 'Formato 3', 'ruta_pdf' => '', 'eliminado' => '0', 'elementos_id' => '3'],
            ['nombre' => 'Formato 4', 'ruta_pdf' => '', 'eliminado' => '0', 'elementos_id' => '4'],
        ];

        // Crear datos
        RazonSocial::insert($razonesSociales);

        foreach ($coordinadores as $coordinador) {
            $user = User::create($coordinador);
            $user->assignRole('coordinador');
        }

        Servicios::insert($serviciosData);
        Elementos::insert($elementosData);
        Formatos::insert($formatosData);
        Tablas::insert($tablasData);
        Campos::insert($camposData);
        Data::insert($dataEntries);
       

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
