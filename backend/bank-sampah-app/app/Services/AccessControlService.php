<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessControlService
{
    public const MENU_OPTIONS = [
        'Dashboard',
        'Nasabah',
        'Kategori Sampah',
        'Sampah',
        'Transaksi',
        'Pembayaran',
        'Pencairan Saldo',
        'User',
        'Laporan',
    ];

    public const OPERATIONAL_OPTIONS = [
        'Tambah Data',
        'Ubah Data',
        'Hapus Data',
        'Verifikasi Pembayaran',
        'Ajukan Pencairan Saldo',
        'Approve Pencairan Saldo',
    ];

    public function menuPermissionName(string $menuLabel): string
    {
        return 'menu.' . $this->slug($menuLabel);
    }

    public function operationalPermissionName(string $accessLabel): string
    {
        return 'operational.' . $this->slug($accessLabel);
    }

    /**
     * @return array<int, string>
     */
    public function menuPermissionNames(array $menuAccess): array
    {
        return array_values(array_unique(array_map(fn(string $label): string => $this->menuPermissionName($label), $menuAccess)));
    }

    /**
     * @return array<int, string>
     */
    public function operationalPermissionNames(array $operationalAccess): array
    {
        return array_values(array_unique(array_map(fn(string $label): string => $this->operationalPermissionName($label), $operationalAccess)));
    }

    /**
     * @return array<int, string>
     */
    public function normalizeMenuAccess(?array $menuAccess): array
    {
        return $this->filterAllowed($menuAccess, self::MENU_OPTIONS);
    }

    /**
     * @return array<int, string>
     */
    public function normalizeOperationalAccess(?array $operationalAccess): array
    {
        return $this->filterAllowed($operationalAccess, self::OPERATIONAL_OPTIONS);
    }

    public function syncUserAccess(User $user, ?array $menuAccess = null, ?array $operationalAccess = null): void
    {
        $menuAccess = $menuAccess ?? ($user->menu_access ?? []);
        $operationalAccess = $operationalAccess ?? ($user->operational_access ?? []);

        $normalizedMenus = $this->normalizeMenuAccess($menuAccess);
        $normalizedOperational = $this->normalizeOperationalAccess($operationalAccess);

        $permissionNames = [
            ...$this->menuPermissionNames($normalizedMenus),
            ...$this->operationalPermissionNames($normalizedOperational),
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'sanctum',
            ]);
        }

        $role = Role::query()->firstOrCreate([
            'name' => $user->role,
            'guard_name' => 'sanctum',
        ]);

        $user->syncRoles([$role]);
        $user->syncPermissions($permissionNames);
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug) ?? '';

        return trim($slug, '_');
    }

    /**
     * @param array<int, string>|null $values
     * @param array<int, string> $allowed
     * @return array<int, string>
     */
    private function filterAllowed(?array $values, array $allowed): array
    {
        if (!is_array($values)) {
            return [];
        }

        $allowedMap = array_flip($allowed);
        $filtered = [];

        foreach ($values as $value) {
            if (!is_string($value)) {
                continue;
            }

            $trimmed = trim($value);
            if ($trimmed === '' || !array_key_exists($trimmed, $allowedMap)) {
                continue;
            }

            $filtered[] = $trimmed;
        }

        return array_values(array_unique($filtered));
    }
}
