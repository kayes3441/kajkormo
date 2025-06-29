<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'module' => 'dashboard',
                'actions' => ['view'],
            ],
            [
                'module' => 'employees',
                'actions' => ['view', 'edit', 'update_status', 'delete'],
            ],
            [
                'module' => 'employee-role',
                'actions' => ['view', 'edit', 'update_status', 'delete'],
            ],
            [
                'module' => 'basic-configuration',
                'actions' => ['view','update'],
            ],
            [
                'module' => 'carrier-configuration',
                'actions' => ['view','update'],
            ],
            [
                'module' => 'other-configuration',
                'actions' => ['view','update'],
            ],
            [
                'module' => 'payment-configuration',
                'actions' => ['view','update'],
            ],
            [
                'module' => 'countries',
                'actions' => ['view','update_status'],
            ],
        ];

        foreach ($permissions as $permission) {
            AdminPermission::updateOrCreate(
                ['module' => $permission['module']],
                ['actions' => $permission['actions']]
            );
        }
    }
}
