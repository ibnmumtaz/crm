<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'lead.view', 'lead.create', 'lead.update', 'lead.delete',
            'lead.restore', 'lead.reassign', 'lead.reopen', 'lead.export', 'lead.import',
            'communication.view', 'communication.create', 'communication.update', 'communication.delete',
            'task.view', 'task.create', 'task.update', 'task.delete',
            'target.view', 'target.create', 'target.update', 'target.delete',
            'analytics.view', 'analytics.export',
            'user.manage', 'role.manage', 'pipeline.manage',
            'source.manage', 'template.manage', 'audit.view',
            'system.manage', 'superadmin.manage', 'impersonate', 'pii.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissions);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'system.manage', 'superadmin.manage', 'impersonate',
        ])->get());

        $manager = Role::firstOrCreate(['name' => 'sales-manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'lead.view', 'lead.update', 'lead.reassign', 'lead.export',
            'communication.view', 'communication.create',
            'task.view', 'task.create', 'task.update',
            'target.view', 'target.create', 'target.update',
            'analytics.view', 'analytics.export',
        ]);

        $rep = Role::firstOrCreate(['name' => 'sales-rep', 'guard_name' => 'web']);
        $rep->syncPermissions([
            'lead.view', 'lead.create', 'lead.update',
            'communication.view', 'communication.create',
            'task.view', 'task.create', 'task.update',
            'target.view', 'analytics.view',
        ]);

        $analyst = Role::firstOrCreate(['name' => 'analyst', 'guard_name' => 'web']);
        $analyst->syncPermissions([
            'lead.view', 'communication.view', 'task.view',
            'target.view', 'analytics.view', 'analytics.export',
        ]);
    }
}
