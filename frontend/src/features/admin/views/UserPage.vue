<script setup lang="ts">
import { computed, nextTick, onMounted, reactive, ref, watch } from "vue";
import api from "../../../api/http";
import { AxiosError } from "axios";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";

type UserRow = {
  id: number;
  nama: string;
  email: string;
  role: string;
  status: string;
  menu_access?: string[];
  operational_access?: string[];
};

const MENU_OPTIONS = [
  "Dashboard",
  "Nasabah",
  "Kategori Sampah",
  "Sampah",
  "Transaksi",
  "Pembayaran",
  "Pencairan Saldo",
  "User",
  "Laporan",
];

const OPERATIONAL_OPTIONS = [
  "Tambah Data",
  "Ubah Data",
  "Hapus Data",
  "Verifikasi Pembayaran",
  "Ajukan Pencairan Saldo",
  "Approve Pencairan Saldo",
];

const rows = ref<UserRow[]>([]);
const editingId = ref<number | null>(null);
const showForm = ref(false);
const searchTerm = ref("");
const authStore = useAuthStore();
const isSuperAdmin = computed(() => authStore.user?.role === "super_admin");
const canCreateUser = computed(
  () => isSuperAdmin.value && canDoOperation(authStore.user, "create"),
);
const canUpdateUser = computed(
  () => isSuperAdmin.value && canDoOperation(authStore.user, "update"),
);
const canDeleteUser = computed(
  () => isSuperAdmin.value && canDoOperation(authStore.user, "delete"),
);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const formErrors = ref<Record<string, string>>({});
const form = reactive({
  nama: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "petugas",
  status: "Aktif",
  menu_access: [] as string[],
  operational_access: [] as string[],
});

const isNasabahRole = computed(() => form.role === "nasabah");
const NASABAH_MENU = ["Kategori Sampah", "Sampah", "Pencairan Saldo"];
const NASABAH_OPS = ["Ajukan Pencairan Saldo"];

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  if (!keyword) {
    return rows.value;
  }

  return rows.value.filter((item) => {
    const values = [item.nama, item.email, item.role, item.status]
      .join(" ")
      .toLowerCase();
    return values.includes(keyword);
  });
});

const hasFilters = computed(() => !!searchTerm.value);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

watch(
  () => form.role,
  (role) => {
    if (role === "nasabah") {
      form.menu_access = [...NASABAH_MENU];
      form.operational_access = [...NASABAH_OPS];
    } else if (role === "super_admin" || role === "petugas") {
      // Untuk super_admin dan petugas, berikan semua akses secara default
      form.menu_access = [...MENU_OPTIONS];
      form.operational_access = [...OPERATIONAL_OPTIONS];
    }
  },
);

async function loadUsers() {
  const response = await api.get("/users");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function startEdit(user: UserRow) {
  editingId.value = user.id;
  showForm.value = true;
  formErrors.value = {};
  form.nama = user.nama;
  form.email = user.email;
  form.password = "";
  form.password_confirmation = "";
  form.role = user.role;
  form.status = user.status || "Aktif";

  // Untuk super_admin dan petugas, jika akses kosong, berikan semua akses
  if (user.role === "super_admin" || user.role === "petugas") {
    form.menu_access =
      user.menu_access && user.menu_access.length > 0
        ? [...user.menu_access]
        : [...MENU_OPTIONS];
    form.operational_access =
      user.operational_access && user.operational_access.length > 0
        ? [...user.operational_access]
        : [...OPERATIONAL_OPTIONS];
  } else {
    form.menu_access = [...(user.menu_access || [])];
    form.operational_access = [...(user.operational_access || [])];
  }
}

function resetForm() {
  editingId.value = null;
  formErrors.value = {};
  showPassword.value = false;
  showPasswordConfirmation.value = false;
  form.nama = "";
  form.email = "";
  form.password = "";
  form.password_confirmation = "";
  form.role = "petugas";
  form.status = "Aktif";
  form.menu_access = [];
  form.operational_access = [];
  showForm.value = false;
}

function resetFilters() {
  searchTerm.value = "";
  setPage(1);
}

function applyValidationErrors(error: unknown) {
  const axiosError = error as AxiosError<{ errors?: Record<string, string[]> }>;
  const errors = axiosError.response?.data?.errors;

  if (!errors) {
    return;
  }

  const mapped: Record<string, string> = {};
  Object.entries(errors).forEach(([field, messages]) => {
    mapped[field] = messages[0] ?? "Input tidak valid";
  });

  formErrors.value = mapped;
}

function toggleAllMenuAccess(checked: boolean) {
  if (isNasabahRole.value) {
    return;
  }
  form.menu_access = checked ? [...MENU_OPTIONS] : [];
}

function toggleAllOperationalAccess(checked: boolean) {
  if (isNasabahRole.value) {
    return;
  }
  form.operational_access = checked ? [...OPERATIONAL_OPTIONS] : [];
}

async function save() {
  formErrors.value = {};

  if (editingId.value && !canUpdateUser.value) {
    formErrors.value.general = "Anda tidak memiliki akses untuk mengubah user.";

    return;
  }

  if (!editingId.value && !canCreateUser.value) {
    formErrors.value.general = "Anda tidak memiliki akses untuk menambah user.";

    return;
  }

  const payload: Record<string, unknown> = {
    nama: form.nama,
    email: form.email,
    role: form.role,
    status: form.status,
    menu_access: form.menu_access,
    operational_access: form.operational_access,
  };

  if (form.password) {
    payload.password = form.password;
    payload.password_confirmation = form.password_confirmation;
  }

  try {
    if (editingId.value) {
      await api.put(`/users/${editingId.value}`, payload);
    } else {
      if (!form.password) {
        formErrors.value.password = "Password wajib diisi";

        return;
      }
      await api.post("/users", payload);
    }
  } catch (error) {
    applyValidationErrors(error);

    return;
  }

  await loadUsers();
  resetForm();
}

async function removeUser(id: number) {
  if (!canDeleteUser.value) {
    return;
  }

  await api.delete(`/users/${id}`);
  await loadUsers();
}

onMounted(loadUsers);

watch(searchTerm, () => {
  setPage(1);
});
</script>

<template>
  <section class="space-y-6">
    <div
      class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
    >
      <div class="text-sm text-slate-500">
        Kelola user, role, akses menu, dan akses operasional.
      </div>
      <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_auto_auto]">
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama, email, role"
            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="isSuperAdmin && (canCreateUser || canUpdateUser)"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup Form" : "Tambah User" }}
          </button>
          <button
            type="button"
            class="rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
            :disabled="!hasFilters"
            @click="resetFilters"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <div
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <div
        v-if="!isSuperAdmin"
        class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm text-amber-700"
      >
        ⚠️ Hanya <strong>Super Admin</strong> yang dapat mengelola user.
      </div>
      <div class="bg-emerald-700 px-5 py-4">
        <h3 class="text-xl font-semibold text-white">Data User</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Ringkasan akun admin, petugas, dan nasabah.
        </p>
      </div>
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Nama</th>
            <th class="px-5 py-4">Email</th>
            <th class="px-5 py-4">Role</th>
            <th class="px-5 py-4">Status</th>
            <th class="px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in pagedRows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ (currentPage - 1) * 10 + index + 1 }}</td>
            <td class="px-5 py-4">{{ item.nama }}</td>
            <td class="px-5 py-4">{{ item.email }}</td>
            <td class="px-5 py-4">{{ item.role }}</td>
            <td class="px-5 py-4">{{ item.status }}</td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <button
                  v-if="isSuperAdmin && canUpdateUser"
                  class="rounded-lg bg-amber-400 px-3 py-2 text-xs"
                  @click="startEdit(item)"
                >
                  Edit
                </button>
                <button
                  v-if="isSuperAdmin && canDeleteUser"
                  class="rounded-lg bg-rose-500 px-3 py-2 text-xs text-white"
                  @click="removeUser(item.id)"
                >
                  Hapus
                </button>
                <span v-if="!isSuperAdmin" class="text-xs text-slate-400">
                  Hanya Super Admin
                </span>
                <span
                  v-else-if="!canUpdateUser && !canDeleteUser"
                  class="text-xs text-slate-400"
                >
                  Tidak ada akses aksi
                </span>
              </div>
            </td>
          </tr>
          <tr
            v-if="filteredRows.length === 0"
            class="border-t border-slate-200"
          >
            <td colspan="5" class="px-5 py-4">
              <div
                class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                role="alert"
              >
                Belum ada data user.
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div
        class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4"
      >
        <p class="text-xs text-slate-500">
          Menampilkan {{ pagedRows.length }} dari {{ filteredRows.length }} data
          <span v-if="hasFilters" class="text-slate-400">
            (total {{ rows.length }})
          </span>
        </p>
        <div class="flex items-center gap-2 text-xs">
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="currentPage === 1"
            @click="setPage(currentPage - 1)"
          >
            Sebelumnya
          </button>
          <span class="text-slate-500">
            Halaman {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="currentPage === totalPages"
            @click="setPage(currentPage + 1)"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="showForm && isSuperAdmin"
      class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
    >
      <h3 class="text-lg font-semibold text-slate-900">
        {{ editingId ? "Edit User" : "Form User" }}
      </h3>
      <p v-if="formErrors.general" class="mt-2 text-sm text-rose-600">
        {{ formErrors.general }}
      </p>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Nama</label
          >
          <input
            v-model="form.nama"
            placeholder="Nama"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          />
          <p v-if="formErrors.nama" class="mt-1 text-xs text-rose-600">
            {{ formErrors.nama }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Email</label
          >
          <input
            v-model="form.email"
            placeholder="Email"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          />
          <p v-if="formErrors.email" class="mt-1 text-xs text-rose-600">
            {{ formErrors.email }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Password</label
          >
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              :placeholder="
                editingId ? 'Password (opsional saat edit)' : 'Password'
              "
              class="w-full rounded-xl border border-slate-300 px-4 py-3"
            />
            <button
              type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs text-slate-600"
              @click="showPassword = !showPassword"
            >
              {{ showPassword ? "Hide" : "Show" }}
            </button>
          </div>
          <p v-if="formErrors.password" class="mt-1 text-xs text-rose-600">
            {{ formErrors.password }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Konfirmasi Password</label
          >
          <div class="relative">
            <input
              v-model="form.password_confirmation"
              :type="showPasswordConfirmation ? 'text' : 'password'"
              :placeholder="
                editingId
                  ? 'Konfirmasi password (jika diubah)'
                  : 'Konfirmasi password'
              "
              class="w-full rounded-xl border border-slate-300 px-4 py-3"
            />
            <button
              type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs text-slate-600"
              @click="showPasswordConfirmation = !showPasswordConfirmation"
            >
              {{ showPasswordConfirmation ? "Hide" : "Show" }}
            </button>
          </div>
          <p
            v-if="formErrors.password_confirmation"
            class="mt-1 text-xs text-rose-600"
          >
            {{ formErrors.password_confirmation }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Role</label
          >
          <select
            v-model="form.role"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          >
            <option value="super_admin">Super Admin</option>
            <option value="petugas">Petugas</option>
            <option value="nasabah">Nasabah</option>
          </select>
          <p v-if="formErrors.role" class="mt-1 text-xs text-rose-600">
            {{ formErrors.role }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Status</label
          >
          <select
            v-model="form.status"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          >
            <option value="Aktif">Aktif</option>
            <option value="Inactive">Inactive</option>
          </select>
          <p v-if="formErrors.status" class="mt-1 text-xs text-rose-600">
            {{ formErrors.status }}
          </p>
        </div>
      </div>

      <div class="mt-5 rounded-2xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <h4 class="font-semibold text-slate-900">Menu Access</h4>
          <div class="flex gap-2 text-xs">
            <button
              class="rounded-lg bg-emerald-100 px-2 py-1 text-emerald-700"
              @click="toggleAllMenuAccess(true)"
              :disabled="isNasabahRole"
            >
              Check all
            </button>
            <button
              class="rounded-lg bg-slate-100 px-2 py-1 text-slate-700"
              @click="toggleAllMenuAccess(false)"
              :disabled="isNasabahRole"
            >
              Uncheck all
            </button>
          </div>
        </div>
        <div class="mt-3 grid gap-2 md:grid-cols-3">
          <label
            v-for="menu in MENU_OPTIONS"
            :key="menu"
            class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm"
          >
            <input
              v-model="form.menu_access"
              type="checkbox"
              :value="menu"
              :class="[
                'h-4 w-4 rounded border-slate-300',
                isSuperAdmin ? 'cursor-pointer' : 'cursor-not-allowed',
              ]"
              :disabled="!isSuperAdmin"
            />
            <span>{{ menu }}</span>
          </label>
        </div>
        <p v-if="isNasabahRole" class="mt-2 text-xs text-slate-500">
          Akses nasabah otomatis disesuaikan.
        </p>
        <p v-if="formErrors.menu_access" class="mt-2 text-xs text-rose-600">
          {{ formErrors.menu_access }}
        </p>
      </div>

      <div class="mt-4 rounded-2xl border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <h4 class="font-semibold text-slate-900">Operational Access</h4>
          <div class="flex gap-2 text-xs">
            <button
              class="rounded-lg bg-emerald-100 px-2 py-1 text-emerald-700"
              @click="toggleAllOperationalAccess(true)"
              :disabled="isNasabahRole"
            >
              Check all
            </button>
            <button
              class="rounded-lg bg-slate-100 px-2 py-1 text-slate-700"
              @click="toggleAllOperationalAccess(false)"
              :disabled="isNasabahRole"
            >
              Uncheck all
            </button>
          </div>
        </div>
        <div class="mt-3 grid gap-2 md:grid-cols-2">
          <label
            v-for="access in OPERATIONAL_OPTIONS"
            :key="access"
            class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm"
          >
            <input
              v-model="form.operational_access"
              type="checkbox"
              :value="access"
              :class="[
                'h-4 w-4 rounded border-slate-300',
                isSuperAdmin ? 'cursor-pointer' : 'cursor-not-allowed',
              ]"
              :disabled="!isSuperAdmin"
            />
            <span>{{ access }}</span>
          </label>
        </div>
        <p v-if="isNasabahRole" class="mt-2 text-xs text-slate-500">
          Operational access nasabah otomatis disesuaikan.
        </p>
        <p
          v-if="formErrors.operational_access"
          class="mt-2 text-xs text-rose-600"
        >
          {{ formErrors.operational_access }}
        </p>
      </div>
      <div class="mt-4 flex gap-3">
        <button
          class="rounded-xl bg-emerald-600 px-5 py-3 text-white disabled:cursor-not-allowed disabled:bg-slate-300"
          :disabled="editingId ? !canUpdateUser : !canCreateUser"
          @click="save"
        >
          {{ editingId ? "Update" : "Tambah" }}
        </button>
        <button
          class="rounded-xl bg-slate-200 px-5 py-3 text-slate-700"
          @click="resetForm"
        >
          Reset
        </button>
      </div>
    </div>
  </section>
</template>
