<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $simple = Role::firstOrCreate(['name' => 'simple']);
        $colorfull = Role::firstOrCreate(['name' => 'colorfull']);

        Permission::firstOrCreate(['name' => 'mindmaps.create']);
        Permission::firstOrCreate(['name' => 'mindmaps.show']);
        Permission::firstOrCreate(['name' => 'mindmaps.edit']);
        Permission::firstOrCreate(['name' => 'mindmaps.delete']);

        Permission::firstOrCreate(['name' => 'mindmaps.actions.add-node']);
        Permission::firstOrCreate(['name' => 'mindmaps.actions.add-ai-node']);
        Permission::firstOrCreate(['name' => 'mindmaps.actions.remove-node']);
        Permission::firstOrCreate(['name' => 'mindmaps.actions.save']);
        Permission::firstOrCreate(['name' => 'mindmaps.actions.auto-aidea']);
        Permission::firstOrCreate(['name' => 'mindmaps.actions.theme']);

        Permission::firstOrCreate(['name' => 'dashboard.show']);

        Permission::firstOrCreate(['name' => 'profile.show']);
        Permission::firstOrCreate(['name' => 'profile.edit']);

        $superAdmin->givePermissionTo([
            'mindmaps.create',
            'mindmaps.show',
            'mindmaps.edit',
            'mindmaps.delete',
            'mindmaps.actions.add-node',
            'mindmaps.actions.add-ai-node',
            'mindmaps.actions.remove-node',
            'mindmaps.actions.save',
            'mindmaps.actions.auto-aidea',
            'mindmaps.actions.theme',
            'dashboard.show',
            'profile.show',
            'profile.edit',
        ]);

        // remove all permissions
        $simple->syncPermissions([]);
        $simple->givePermissionTo([ 
            'mindmaps.create',
            'mindmaps.show',
            'mindmaps.edit',
            'mindmaps.actions.add-node',
            'mindmaps.actions.add-ai-node',
            'mindmaps.actions.remove-node',
            'mindmaps.actions.save',
        ]);

        $colorfull->syncPermissions([]);
        $colorfull->givePermissionTo([
            'mindmaps.create',
            'mindmaps.show',
            'mindmaps.edit',
            'mindmaps.actions.add-node',
            'mindmaps.actions.add-ai-node',
            'mindmaps.actions.remove-node',
            'mindmaps.actions.save',
        ]);
    }
}
