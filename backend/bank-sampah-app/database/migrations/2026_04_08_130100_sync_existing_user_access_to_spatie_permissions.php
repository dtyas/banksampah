<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumns('users', ['menu_access', 'operational_access'])) {
            return;
        }

        $rolesTable = config('permission.table_names.roles', 'roles');
        $permissionsTable = config('permission.table_names.permissions', 'permissions');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
        $modelHasPermissionsTable = config('permission.table_names.model_has_permissions', 'model_has_permissions');

        $roleNames = ['super_admin', 'petugas', 'nasabah'];
        foreach ($roleNames as $roleName) {
            DB::table($rolesTable)->updateOrInsert(
                ['name' => $roleName, 'guard_name' => 'sanctum'],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $users = DB::table('users')->select('id', 'role', 'menu_access', 'operational_access')->get();

        foreach ($users as $user) {
            $roleId = DB::table($rolesTable)
                ->where('name', $user->role)
                ->where('guard_name', 'sanctum')
                ->value('id');

            if ($roleId) {
                DB::table($modelHasRolesTable)->updateOrInsert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ], []);
            }

            $menuAccess = json_decode($user->menu_access ?? '[]', true);
            $operationalAccess = json_decode($user->operational_access ?? '[]', true);
            $permissionNames = [];

            if (is_array($menuAccess)) {
                foreach ($menuAccess as $menu) {
                    if (!is_string($menu)) {
                        continue;
                    }

                    $permissionNames[] = 'menu.' . $this->slug($menu);
                }
            }

            if (is_array($operationalAccess)) {
                foreach ($operationalAccess as $access) {
                    if (!is_string($access)) {
                        continue;
                    }

                    $permissionNames[] = 'operational.' . $this->slug($access);
                }
            }

            foreach (array_unique($permissionNames) as $permissionName) {
                DB::table($permissionsTable)->updateOrInsert(
                    ['name' => $permissionName, 'guard_name' => 'sanctum'],
                    ['updated_at' => now(), 'created_at' => now()],
                );

                $permissionId = DB::table($permissionsTable)
                    ->where('name', $permissionName)
                    ->where('guard_name', 'sanctum')
                    ->value('id');

                if (! $permissionId) {
                    continue;
                }

                DB::table($modelHasPermissionsTable)->updateOrInsert([
                    'permission_id' => $permissionId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ], []);
            }
        }
    }

    public function down(): void
    {
        // Permission backfill is data-only and intentionally not reverted.
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug) ?? '';

        return trim($slug, '_');
    }
};
