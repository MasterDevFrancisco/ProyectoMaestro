<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos si aún no existen
        $permissions = [
            'view documentation',
            'view elementos',
            'view usuarios',
            'view formatos',
            // Agrega otros permisos que necesites
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
        $clienteRole->givePermissionTo('view documentation');

        // Crear el rol de admin y asignar todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Crear el rol de coordinador y asignar permisos específicos
        $coordinadorRole = Role::firstOrCreate(['name' => 'coordinador']);
        $coordinadorRole->givePermissionTo(['view elementos', 'view usuarios', 'view formatos']);
    }
}
