<?php

namespace Database\Seeders;

use App\Services\AccessControlService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Seed the application's permissions table.
     */
    public function run(): void
    {
        $accessControl = new AccessControlService();

        $menuPermissions = array_map(
            [$accessControl, 'menuPermissionName'],
            AccessControlService::MENU_OPTIONS
        );
        $operationalPermissions = array_map(
            [$accessControl, 'operationalPermissionName'],
            AccessControlService::OPERATIONAL_OPTIONS
        );

        $permissionNames = array_values(array_unique([
            ...$menuPermissions,
            ...$operationalPermissions,
        ]));

        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}
