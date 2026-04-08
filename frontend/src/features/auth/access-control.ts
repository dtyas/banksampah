import type { AuthUser } from '../../api/auth.api';

export const ROUTE_MENU_MAP: Record<string, string> = {
    dashboard: 'Dashboard',
    nasabah: 'Nasabah',
    'kategori-sampah': 'Kategori Sampah',
    sampah: 'Sampah',
    transaksi: 'Transaksi',
    pembayaran: 'Pembayaran',
    'pencairan-saldo': 'Pencairan Saldo',
    user: 'User',
    laporan: 'Laporan',
};

export const OPERATION_MAP = {
    create: 'Tambah Data',
    update: 'Ubah Data',
    delete: 'Hapus Data',
    verifyPayment: 'Verifikasi Pembayaran',
    requestWithdraw: 'Ajukan Pencairan Saldo',
    approveWithdraw: 'Approve Pencairan Saldo',
} as const;

function isSuperAdmin(user: AuthUser | null | undefined): boolean {
    return user?.role === 'super_admin';
}

function hasArrayValue(source: string[] | undefined, expected: string): boolean {
    return Array.isArray(source) && source.includes(expected);
}

export function canAccessMenu(user: AuthUser | null | undefined, menuLabel: string): boolean {
    if (!user) {
        return false;
    }

    if (isSuperAdmin(user)) {
        return true;
    }

    return hasArrayValue(user.menu_access, menuLabel);
}

export function canAccessRoute(user: AuthUser | null | undefined, routeName: string): boolean {
    const menu = ROUTE_MENU_MAP[routeName];

    if (!menu) {
        return true;
    }

    return canAccessMenu(user, menu);
}

export function getFirstAccessibleRoute(user: AuthUser | null | undefined): string {
    if (!user) {
        return 'login';
    }

    if (isSuperAdmin(user)) {
        return 'dashboard';
    }

    for (const [routeName, menu] of Object.entries(ROUTE_MENU_MAP)) {
        if (canAccessMenu(user, menu)) {
            return routeName;
        }
    }

    return 'login';
}

export function canDoOperation(
    user: AuthUser | null | undefined,
    operation: keyof typeof OPERATION_MAP,
): boolean {
    if (!user) {
        return false;
    }

    if (isSuperAdmin(user)) {
        return true;
    }

    return hasArrayValue(user.operational_access, OPERATION_MAP[operation]);
}
