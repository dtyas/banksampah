<script setup lang="ts">
import { computed, onMounted, reactive, ref } from "vue";
import api from "../../../api/http";
import { AxiosError } from "axios";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";

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
const channelOptions = [
  "ID_BCA",
  "ID_BNI",
  "ID_BRI",
  "ID_MANDIRI",
  "ID_OVO",
  "ID_DANA",
  "ID_GOPAY",
];

async function loadNasabah() {
  const response = await api.get("/nasabah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function startEdit(item: Nasabah) {
  editingId.value = item.id;
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
    payout_channel: form.payout_channel,
    account_number: form.account_number,
    account_holder_name: form.account_holder_name,
    email: form.email,
  };
  if (form.password) {
    payload.password = form.password;
    payload.password_confirmation = form.password_confirmation;
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

async function removeNasabah(id: number) {
  if (!canDeleteNasabah.value) {
    return;
  }

  await api.delete(`/nasabah/${id}`);
  await loadNasabah();
}

onMounted(loadNasabah);
</script>

<template>
  <section class="space-y-6">
    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Form Nasabah</h3>
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

        <div>
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

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700"
            >Password</label
          >
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              :placeholder="
                editingId
                  ? 'Password (opsional saat edit)'
                  : 'Password (opsional)'
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

        <div class="md:col-span-2">
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
                  : 'Konfirmasi password (jika diisi)'
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

    <div
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">Nama</th>
            <th class="px-5 py-4">Email</th>
            <th class="px-5 py-4">No HP</th>
            <th class="px-5 py-4">Rekening/E-Wallet</th>
            <th class="px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in rows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ item.nama }}</td>
            <td class="px-5 py-4">{{ item.user?.email || "-" }}</td>
            <td class="px-5 py-4">{{ item.no_hp || "-" }}</td>
            <td class="px-5 py-4">
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
                  @click="removeNasabah(item.id)"
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
          <tr v-if="rows.length === 0" class="border-t border-slate-200">
            <td colspan="5" class="px-5 py-4">
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
    </div>
  </section>
</template>
