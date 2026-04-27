<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from "vue";
import api from "../../../api/http";
import { AxiosError } from "axios";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";
import { isFeatureEnabled } from "../../../config/features";

type Nasabah = {
  id: number;
  nama: string;
  alamat: string | null;
  no_hp: string | null;
  payout_channel?: string | null;
  account_number?: string | null;
  account_holder_name?: string | null;
  user?: { email?: string };
};

const rows = ref<Nasabah[]>([]);
const editingId = ref<number | null>(null);
const showForm = ref(false);
const searchTerm = ref("");
const authStore = useAuthStore();
const canCreateNasabah = computed(() =>
  canDoOperation(authStore.user, "create"),
);
const canUpdateNasabah = computed(() =>
  canDoOperation(authStore.user, "update"),
);
const canDeleteNasabah = computed(() =>
  canDoOperation(authStore.user, "delete"),
);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const formErrors = ref<Record<string, string>>({});
const form = reactive({
  nama: "",
  alamat: "",
  no_hp: "",
  payout_channel: "",
  account_number: "",
  account_holder_name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const isEditMode = computed(() => editingId.value !== null);
const isPayoutCash = computed(() => form.payout_channel === "CASH");
const isXenditDisbursementEnabled = computed(() =>
  isFeatureEnabled("enableXenditDisbursement"),
);

const channelOptions = computed(() => {
  const base = ["CASH"];
  if (isXenditDisbursementEnabled.value) {
    base.push(
      "ID_BCA",
      "ID_BNI",
      "ID_BRI",
      "ID_MANDIRI",
      "ID_OVO",
      "ID_DANA",
      "ID_GOPAY",
    );
  }
  return base;
});

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  if (!keyword) {
    return rows.value;
  }

  return rows.value.filter((item) => {
    const values = [
      item.nama,
      item.alamat ?? "",
      item.no_hp ?? "",
      item.payout_channel ?? "",
      item.account_number ?? "",
      item.account_holder_name ?? "",
      item.user?.email ?? "",
    ]
      .join(" ")
      .toLowerCase();

    return values.includes(keyword);
  });
});

const hasFilters = computed(() => !!searchTerm.value);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

async function loadNasabah() {
  const response = await api.get("/nasabah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function startEdit(item: Nasabah) {
  editingId.value = item.id;
  showForm.value = true;
  formErrors.value = {};
  form.nama = item.nama ?? "";
  form.alamat = item.alamat ?? "";
  form.no_hp = item.no_hp ?? "";
  form.payout_channel = item.payout_channel ?? "";
  form.account_number = item.account_number ?? "";
  form.account_holder_name = item.account_holder_name ?? "";
  form.email = item.user?.email ?? "";
  form.password = "";
  form.password_confirmation = "";
}

function resetForm() {
  editingId.value = null;
  formErrors.value = {};
  showPassword.value = false;
  showPasswordConfirmation.value = false;
  form.nama = "";
  form.alamat = "";
  form.no_hp = "";
  form.payout_channel = "";
  form.account_number = "";
  form.account_holder_name = "";
  form.email = "";
  form.password = "";
  form.password_confirmation = "";
  showForm.value = false;
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

async function save() {
  formErrors.value = {};

  if (editingId.value && !canUpdateNasabah.value) {
    formErrors.value.general =
      "Anda tidak memiliki akses untuk mengubah nasabah.";

    return;
  }

  if (!editingId.value && !canCreateNasabah.value) {
    formErrors.value.general =
      "Anda tidak memiliki akses untuk menambah nasabah.";

    return;
  }

  const payload: Record<string, string> = {
    nama: form.nama,
    alamat: form.alamat,
    no_hp: form.no_hp,
  };

  // Only include payout details in edit mode
  if (editingId.value) {
    payload.payout_channel = form.payout_channel;
    payload.account_number = form.account_number;
    payload.account_holder_name = form.account_holder_name;
    payload.email = form.email;
    if (form.password) {
      payload.password = form.password;
      payload.password_confirmation = form.password_confirmation;
    }
  }

  try {
    if (editingId.value) {
      await api.put(`/nasabah/${editingId.value}`, payload);
    } else {
      await api.post("/nasabah", payload);
    }
  } catch (error) {
    applyValidationErrors(error);

    return;
  }

  await loadNasabah();
  resetForm();
}

async function removeNasabah(id: number, nama: string) {
  if (!canDeleteNasabah.value) {
    return;
  }

  const confirmed = window.confirm(`Hapus nasabah "${nama}"?`);
  if (!confirmed) {
    return;
  }

  await api.delete(`/nasabah/${id}`);
  await loadNasabah();
}

function resetFilters() {
  searchTerm.value = "";
  setPage(1);
}

onMounted(loadNasabah);

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
        Kelola data nasabah beserta rekening payout dan akun login.
      </div>
      <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_auto_auto]">
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama, email, atau rekening"
            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="canCreateNasabah || canUpdateNasabah"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup Form" : "Tambah Nasabah" }}
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
      <div class="bg-emerald-700 px-5 py-4">
        <h3 class="text-xl font-semibold text-white">Data Nasabah</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Daftar nasabah terbaru yang tersimpan di sistem.
        </p>
      </div>
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Nama</th>
            <th class="px-5 py-4">Email</th>
            <th class="px-5 py-4">No HP</th>
            <th
              v-if="isFeatureEnabled('enableXenditDisbursement')"
              class="px-5 py-4"
            >
              Rekening/E-Wallet
            </th>
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
            <td class="px-5 py-4">{{ item.user?.email || "-" }}</td>
            <td class="px-5 py-4">{{ item.no_hp || "-" }}</td>
            <td
              v-if="isFeatureEnabled('enableXenditDisbursement')"
              class="px-5 py-4"
            >
              {{ item.account_number || "-" }}
            </td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <button
                  v-if="canUpdateNasabah"
                  class="rounded-lg bg-amber-400 px-3 py-2 text-xs"
                  @click="startEdit(item)"
                >
                  Edit
                </button>
                <button
                  v-if="canDeleteNasabah"
                  class="rounded-lg bg-rose-500 px-3 py-2 text-xs text-white"
                  @click="removeNasabah(item.id, item.nama)"
                >
                  Hapus
                </button>
                <span
                  v-if="!canUpdateNasabah && !canDeleteNasabah"
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
            <td
              :colspan="isFeatureEnabled('enableXenditDisbursement') ? 6 : 5"
              class="px-5 py-4"
            >
              <div
                class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                role="alert"
              >
                Belum ada data nasabah.
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
      v-if="showForm"
      class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
    >
      <h3 class="text-lg font-semibold text-slate-900">
        {{ editingId ? "Edit Nasabah" : "Form Nasabah" }}
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

        <div v-if="isEditMode && isXenditDisbursementEnabled">
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Email User Nasabah</label
          >
          <input
            v-model="form.email"
            placeholder="Email user nasabah"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          />
          <p v-if="formErrors.email" class="mt-1 text-xs text-rose-600">
            {{ formErrors.email }}
          </p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >No HP</label
          >
          <input
            v-model="form.no_hp"
            placeholder="No HP"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          />
          <p v-if="formErrors.no_hp" class="mt-1 text-xs text-rose-600">
            {{ formErrors.no_hp }}
          </p>
        </div>

        <div v-if="isEditMode && isXenditDisbursementEnabled">
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Channel Payout</label
          >
          <select
            v-model="form.payout_channel"
            class="w-full rounded-xl border border-slate-300 px-4 py-3"
          >
            <option value="">Pilih channel</option>
            <option
              v-for="option in channelOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
          <p
            v-if="formErrors.payout_channel"
            class="mt-1 text-xs text-rose-600"
          >
            {{ formErrors.payout_channel }}
          </p>
        </div>

        <template
          v-if="isEditMode && isXenditDisbursementEnabled && !isPayoutCash"
        >
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700"
              >Nomor Rekening/E-Wallet</label
            >
            <input
              v-model="form.account_number"
              placeholder="Nomor rekening atau no HP e-wallet"
              class="w-full rounded-xl border border-slate-300 px-4 py-3"
            />
            <p
              v-if="formErrors.account_number"
              class="mt-1 text-xs text-rose-600"
            >
              {{ formErrors.account_number }}
            </p>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700"
              >Nama Pemilik Rekening</label
            >
            <input
              v-model="form.account_holder_name"
              placeholder="Nama pemilik rekening"
              class="w-full rounded-xl border border-slate-300 px-4 py-3"
            />
            <p
              v-if="formErrors.account_holder_name"
              class="mt-1 text-xs text-rose-600"
            >
              {{ formErrors.account_holder_name }}
            </p>
          </div>
        </template>

        <template v-if="isEditMode && isXenditDisbursementEnabled">
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700"
              >Password</label
            >
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password (kosongkan jika tidak diubah)"
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

          <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-slate-700"
              >Konfirmasi Password</label
            >
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                placeholder="Konfirmasi password (jika diubah)"
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
        </template>
      </div>
      <div class="mt-4">
        <label class="mb-2 block text-sm font-medium text-slate-700"
          >Alamat</label
        >
        <textarea
          v-model="form.alamat"
          rows="3"
          placeholder="Alamat"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <p v-if="formErrors.alamat" class="mt-1 text-xs text-rose-600">
          {{ formErrors.alamat }}
        </p>
      </div>
      <div class="mt-4 flex gap-3">
        <button
          class="rounded-xl bg-emerald-600 px-5 py-3 text-white disabled:cursor-not-allowed disabled:bg-slate-300"
          :disabled="editingId ? !canUpdateNasabah : !canCreateNasabah"
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
