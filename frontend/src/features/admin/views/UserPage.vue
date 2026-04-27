<script setup lang="ts">
import { computed, reactive, ref, watch, onMounted } from "vue";
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

// --- LOGIKA IDENTITAS & IZIN ---
const currentUser = computed(() => authStore.user);
const isSuperAdminLogin = computed(
  () => authStore.user?.role === "super_admin",
);

// Izin tombol Aksi (Super Admin selalu bisa)
const canCreateUser = computed(
  () => isSuperAdminLogin.value || canDoOperation(authStore.user, "create"),
);
const canUpdateUser = computed(
  () => isSuperAdminLogin.value || canDoOperation(authStore.user, "update"),
);
const canDeleteUser = computed(
  () => isSuperAdminLogin.value || canDoOperation(authStore.user, "delete"),
);

// PERBAIKAN: Fungsi untuk membuka gembok Checkbox
// Jika yang login Super Admin, dia BISA edit akses siapapun.
const canEditAksesKustom = computed(() => {
  return isSuperAdminLogin.value;
});

import EyeToggle from "../../../components/ui/EyeToggle.vue";
const showPassword = ref(false);
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

// Nasabah tetap punya automasi, tapi Super Admin bisa override (timpa) manual
const isNasabahRole = computed(() => form.role === "nasabah");

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  if (!keyword) return rows.value;
  return rows.value.filter((item) =>
    [item.nama, item.email, item.role].some((val) =>
      String(val).toLowerCase().includes(keyword),
    ),
  );
});

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

// Watcher untuk template awal (Hanya jalan saat TAMBAH USER BARU)
watch(
  () => form.role,
  (newRole) => {
    if (!editingId.value) {
      // Hanya otomatis jika sedang buat user baru
      if (newRole === "nasabah") {
        form.menu_access = ["Kategori Sampah", "Sampah", "Pencairan Saldo"];
        form.operational_access = ["Ajukan Pencairan Saldo"];
      } else {
        form.menu_access = [...MENU_OPTIONS];
        form.operational_access = [...OPERATIONAL_OPTIONS];
      }
    }
  },
);

async function loadUsers() {
  try {
    const response = await api.get("/users");
    rows.value = response.data?.data?.data ?? response.data?.data ?? [];
  } catch (e) {
    console.error("Gagal load users", e);
  }
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

  // Mengambil data akses yang ada di database
  form.menu_access = Array.isArray(user.menu_access)
    ? [...user.menu_access]
    : [];
  form.operational_access = Array.isArray(user.operational_access)
    ? [...user.operational_access]
    : [];

  window.scrollTo({ top: 0, behavior: "smooth" });
}

function resetForm() {
  editingId.value = null;
  showForm.value = false;
  formErrors.value = {};
  Object.assign(form, {
    nama: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "petugas",
    status: "Aktif",
    menu_access: [],
    operational_access: [],
  });
}

// Fungsi tombol Check All / Reset
function toggleAllMenu(checked: boolean) {
  if (!canEditAksesKustom.value) return;
  form.menu_access = checked ? [...MENU_OPTIONS] : [];
}

function toggleAllOps(checked: boolean) {
  if (!canEditAksesKustom.value) return;
  form.operational_access = checked ? [...OPERATIONAL_OPTIONS] : [];
}

async function save() {
  formErrors.value = {};
  const payload = { ...form };
  if (editingId.value && !form.password) {
    delete (payload as any).password;
    delete (payload as any).password_confirmation;
  }

  try {
    if (editingId.value) {
      await api.put(`/users/${editingId.value}`, payload);
    } else {
      await api.post("/users", payload);
    }
    await loadUsers();
    resetForm();
  } catch (error: any) {
    const errors = error.response?.data?.errors;
    if (errors) {
      formErrors.value = Object.fromEntries(
        Object.entries(errors).map(([k, v]: any) => [k, v[0]]),
      );
    }
  }
}

async function removeUser(id: number) {
  if (confirm("Hapus user ini?")) {
    await api.delete(`/users/${id}`);
    await loadUsers();
  }
}

onMounted(loadUsers);
</script>

<template>
  <section class="p-6 space-y-6 bg-slate-50 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">
          Manajemen Akun
        </h1>
        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">
          Kontrol Akses Penuh Super Admin
        </p>
      </div>
      <div class="flex gap-2">
        <input
          v-model="searchTerm"
          type="text"
          placeholder="Cari user..."
          class="px-4 py-2 border border-slate-200 rounded-xl bg-white shadow-sm outline-none"
        />
        <button
          v-if="isSuperAdminLogin"
          @click="showForm = !showForm"
          class="px-6 py-2 bg-emerald-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 cursor-pointer transition"
        >
          {{ showForm ? "Batal" : "Tambah User" }}
        </button>
      </div>
    </div>

    <div
      class="bg-white rounded-[24px] border border-slate-200 overflow-hidden shadow-sm"
    >
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th
              class="px-6 py-4 font-black text-slate-600 text-[10px] uppercase"
            >
              User
            </th>
            <th
              class="px-6 py-4 font-black text-slate-600 text-[10px] uppercase"
            >
              Role
            </th>
            <th
              class="px-6 py-4 font-black text-slate-600 text-[10px] uppercase"
            >
              Status
            </th>
            <th
              class="px-6 py-4 font-black text-slate-600 text-[10px] uppercase text-center"
            >
              Aksi
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="user in pagedRows"
            :key="user.id"
            class="hover:bg-slate-50/50 transition"
          >
            <td class="px-6 py-4">
              <div class="font-bold text-slate-900">{{ user.nama }}</div>
              <div class="text-[11px] text-slate-400 font-medium">
                {{ user.email }}
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                :class="
                  user.role === 'super_admin'
                    ? 'bg-purple-100 text-purple-700'
                    : 'bg-slate-100 text-slate-600'
                "
                class="px-2 py-1 rounded font-black text-[9px] uppercase tracking-tighter"
              >
                {{ user.role }}
              </span>
            </td>
            <td
              class="px-6 py-4 text-[10px] font-black uppercase"
              :class="
                user.status === 'Aktif' ? 'text-emerald-600' : 'text-rose-500'
              "
            >
              {{ user.status }}
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-center gap-2">
                <button
                  v-if="canUpdateUser"
                  @click="startEdit(user)"
                  class="px-4 py-1.5 bg-amber-400 text-white font-black text-[10px] uppercase rounded-lg hover:bg-amber-500 cursor-pointer transition"
                >
                  Edit
                </button>
                <button
                  v-if="canDeleteUser && user.id !== currentUser?.id"
                  @click="removeUser(user.id)"
                  class="px-4 py-1.5 bg-rose-500 text-white font-black text-[10px] uppercase rounded-lg hover:bg-rose-600 cursor-pointer transition"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-if="showForm"
      class="bg-white rounded-[32px] p-8 border border-slate-200 shadow-2xl animate-in fade-in slide-in-from-bottom-4 duration-500"
    >
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black text-slate-900 tracking-tighter">
          {{ editingId ? "Update Data" : "User Baru" }}
        </h2>
        <div
          class="px-4 py-1 bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-black uppercase rounded-full"
        >
          Mode Super Admin
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-[11px] font-black text-slate-500 uppercase ml-1"
                >Nama</span
              >
              <input
                v-model="form.nama"
                type="text"
                class="mt-1 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500 transition"
              />
            </label>
            <label class="block">
              <span class="text-[11px] font-black text-slate-500 uppercase ml-1"
                >Email</span
              >
              <input
                v-model="form.email"
                type="email"
                class="mt-1 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500 transition"
              />
            </label>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <label class="block">
              <span class="text-[11px] font-black text-slate-500 uppercase ml-1"
                >Role</span
              >
              <select
                v-model="form.role"
                class="mt-1 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold cursor-pointer outline-none"
              >
                <option value="super_admin">Super Admin</option>
                <option value="petugas">Petugas</option>
                <option value="nasabah">Nasabah</option>
              </select>
            </label>
            <label class="block">
              <span class="text-[11px] font-black text-slate-500 uppercase ml-1"
                >Status</span
              >
              <select
                v-model="form.status"
                class="mt-1 w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold cursor-pointer outline-none"
              >
                <option value="Aktif">Aktif</option>
                <option value="Inactive">Non-Aktif</option>
              </select>
            </label>
          </div>

          <div
            class="p-5 bg-white rounded-[24px] text-slate-900 space-y-4 border border-slate-200"
          >
            <h4
              class="text-[10px] font-black uppercase tracking-widest text-slate-500"
            >
              Keamanan Akun
            </h4>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-slate-400 text-slate-900"
                :placeholder="
                  editingId ? 'Kosongkan jika tidak diganti' : 'Password baru'
                "
              />
              <div class="absolute right-3 top-1/2 -translate-y-1/2">
                <EyeToggle
                  :show="showPassword"
                  @toggle="showPassword = !showPassword"
                />
              </div>
            </div>
            <div
              v-if="formErrors.password"
              class="text-xs text-rose-400 font-bold"
            >
              {{ formErrors.password }}
            </div>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Konfirmasi password"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 placeholder:text-slate-400 text-slate-900"
              />
              <div class="absolute right-3 top-1/2 -translate-y-1/2">
                <EyeToggle
                  :show="showPassword"
                  @toggle="showPassword = !showPassword"
                />
              </div>
            </div>
            <div
              v-if="formErrors.password_confirmation"
              class="text-xs text-rose-400 font-bold"
            >
              {{ formErrors.password_confirmation }}
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="p-6 bg-slate-50 border border-slate-200 rounded-[28px]">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-[11px] font-black text-slate-700 uppercase">
                Akses Menu
              </h4>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="toggleAllMenu(true)"
                  class="text-[9px] font-black px-2 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 cursor-pointer transition"
                >
                  Check All
                </button>
                <button
                  type="button"
                  @click="toggleAllMenu(false)"
                  class="text-[9px] font-black px-2 py-1 bg-slate-400 text-white rounded hover:bg-slate-500 cursor-pointer transition"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <label
                v-for="menu in MENU_OPTIONS"
                :key="menu"
                class="flex items-center gap-2 p-3 bg-white border border-slate-100 rounded-xl cursor-pointer hover:border-emerald-400 transition group"
              >
                <input
                  type="checkbox"
                  v-model="form.menu_access"
                  :value="menu"
                  :disabled="!canEditAksesKustom"
                  class="w-4 h-4 accent-emerald-600 cursor-pointer"
                />
                <span
                  class="text-xs font-bold text-slate-600 group-hover:text-emerald-700"
                  >{{ menu }}</span
                >
              </label>
            </div>
          </div>

          <div class="p-6 bg-slate-50 border border-slate-200 rounded-[28px]">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-[11px] font-black text-slate-700 uppercase">
                Operasional
              </h4>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="toggleAllOps(true)"
                  class="text-[9px] font-black px-2 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 cursor-pointer transition"
                >
                  Check All
                </button>
                <button
                  type="button"
                  @click="toggleAllOps(false)"
                  class="text-[9px] font-black px-2 py-1 bg-slate-400 text-white rounded hover:bg-slate-500 cursor-pointer transition"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="grid grid-cols-1 gap-2">
              <label
                v-for="ops in OPERATIONAL_OPTIONS"
                :key="ops"
                class="flex items-center gap-2 p-3 bg-white border border-slate-100 rounded-xl cursor-pointer hover:border-emerald-400 transition group"
              >
                <input
                  type="checkbox"
                  v-model="form.operational_access"
                  :value="ops"
                  :disabled="!canEditAksesKustom"
                  class="w-4 h-4 accent-emerald-600 cursor-pointer"
                />
                <span
                  class="text-xs font-bold text-slate-600 group-hover:text-emerald-700"
                  >{{ ops }}</span
                >
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-10 flex justify-end gap-3 pt-6 border-t border-slate-100">
        <button
          @click="resetForm"
          class="px-8 py-3 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 cursor-pointer transition"
        >
          Batal
        </button>
        <button
          @click="save"
          class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 cursor-pointer transition shadow-xl shadow-emerald-100"
        >
          {{ editingId ? "Update Data User" : "Daftarkan User" }}
        </button>
      </div>
    </div>
  </section>
</template>
