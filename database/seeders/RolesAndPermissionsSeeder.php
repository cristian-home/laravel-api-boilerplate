<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // crear permisos
        Permission::create(['name' => 'ver-hojas-de-vida']);
        Permission::create(['name' => 'crear-hojas-de-vida']);
        Permission::create(['name' => 'editar-hojas-de-vida']);
        Permission::create(['name' => 'eliminar-hojas-de-vida']);

        // crear roles y asignar permisos
        Role::create(['name' => 'super-admin'])->givePermissionTo(
            Permission::all(),
        );

        Role::create(['name' => 'gestion-logistica'])->givePermissionTo([
            'ver-hojas-de-vida',
            'crear-hojas-de-vida',
            'editar-hojas-de-vida',
            'eliminar-hojas-de-vida',
        ]);

        Role::create(['name' => 'formacion'])->givePermissionTo([
            'ver-hojas-de-vida',
            'crear-hojas-de-vida',
            'editar-hojas-de-vida',
            'eliminar-hojas-de-vida',
        ]);
    }
}
