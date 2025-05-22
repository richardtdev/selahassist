<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Plan management
            'view plans',
            'create plans',
            'edit plans',
            'delete plans',
            
            // Sermon management
            'create sermons',
            'edit sermons',
            'delete sermons',
            'view all sermons',
            
            // Template management
            'create templates',
            'edit templates',
            'delete templates',
            'view all templates',
            
            // System management
            'access admin dashboard',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $pastorRole = Role::create(['name' => 'pastor']);
        $pastorRole->givePermissionTo([
            'create sermons',
            'edit sermons',
            'delete sermons',
            'create templates',
            'edit templates',
            'delete templates',
        ]);

        // Assign roles to existing users
        $admin = User::where('email', 'admin@sermonassist.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }

        $pastor = User::where('email', 'pastor@sermonassist.com')->first();
        if ($pastor) {
            $pastor->assignRole('pastor');
        }
    }
}
