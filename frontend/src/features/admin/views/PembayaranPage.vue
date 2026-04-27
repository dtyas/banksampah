<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";

type Pembayaran = {
  id: number;
  transaksi_id: number;
  nasabah_id?: number | null;
  jumlah: number;
  metode: string;
  status: string;
  tanggal: string;
  verified_at?: string | null;
  verifier?: {
    id: number;
    nama: string;
    email: string;
  } | null;
  nasabah?: { id?: number; nama?: string } | null;
  transaksi?: {
    id?: number;
    nasabah_id?: number;
    nasabah?: { id?: number; nama?: string } | null;
  } | null;
};

type Nasabah = {
  id: number;
  nama: string;
};

type TransaksiOption = {
  id: number;
  nasabah?: { nama?: string };
  nasabah_id?: number;
  total_harga?: number;
};

const rows = ref<Pembayaran[]>([]);
const nasabahOptions = ref<Nasabah[]>([]);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const canVerifyPayment = computed(() =>
  canDoOperation(authStore.user, "verifyPayment"),
);
const showForm = ref(false);
const saving = ref(false);
const processingId = ref<number | null>(null);
const searchTerm = ref("");
const filterStart = ref("");
const filterEnd = ref("");
const statusFilter = ref("");

const form = ref({
  nasabah_id: "",
  transaksi_id: "",
  jumlah: "",
  metode: "Cash",
  status: "menunggu",
  tanggal: new Date().toISOString().slice(0, 10),
});

const nasabahSaldo = ref<number | null>(null);
const nasabahSaldoLoading = ref(false);
const nasabahSaldoError = ref("");

// State transaksi terbaru nasabah (auto-picked)
const latestTransaksi = ref<TransaksiOption | null>(null);
const latestTransaksiLoading = ref(false);
const latestTransaksiError = ref("");

async function fetchNasabahSaldo() {
  nasabahSaldo.value = null;
  nasabahSaldoError.value = "";
  const id = form.value.nasabah_id;
  if (!id) return;
  nasabahSaldoLoading.value = true;
  try {
    const res = await api.get(`/nasabah/${id}/saldo`);
    nasabahSaldo.value = res.data?.data?.saldo ?? null;
  } catch {
    nasabahSaldo.value = null;
    nasabahSaldoError.value = "Gagal mengambil saldo nasabah";
  } finally {
    nasabahSaldoLoading.value = false;
  }
}

async function fetchLatestTransaksiNasabah(nasabahId: string) {
  latestTransaksi.value = null;
  latestTransaksiError.value = "";
  form.value.transaksi_id = "";
  if (!nasabahId) return;
  latestTransaksiLoading.value = true;
  try {
    const res = await api.get(`/transaksi?nasabah_id=${nasabahId}`);
    const list: TransaksiOption[] =
      res.data?.data?.data ?? res.data?.data ?? [];
    if (list.length > 0) {
      // Ambil yang id paling besar (terbaru)
      const latest = list.reduce((prev, cur) =>
        cur.id > prev.id ? cur : prev,
      );
      latestTransaksi.value = latest;
      form.value.transaksi_id = String(latest.id);
    } else {
      latestTransaksiError.value = "Nasabah belum memiliki transaksi";
    }
  } catch {
    latestTransaksiError.value = "Gagal mengambil transaksi nasabah";
  } finally {
    latestTransaksiLoading.value = false;
  }
}

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  const start = filterStart.value;
  const end = filterEnd.value;
  const status = statusFilter.value.trim().toLowerCase();

  return rows.value.filter((item) => {
    const nasabahNama =
      item.nasabah?.nama ||
      item.transaksi?.nasabah?.nama ||
      "";
    const haystack = [
      item.transaksi_id,
      item.metode,
      item.status,
      item.tanggal,
      nasabahNama,
    ]
      .filter(Boolean)
      .join(" ")
      .toLowerCase();
    if (keyword && !haystack.includes(keyword)) {
      return false;
    }

    if (status && item.status.toLowerCase() !== status) {
      return false;
    }

    if (!start && !end) {
      return true;
    }

    const parsed = new Date(item.tanggal);
    if (Number.isNaN(parsed.getTime())) {
      return false;
    }
    if (start) {
      const startDate = new Date(start);
      if (parsed < startDate) {
        return false;
      }
    }
    if (end) {
      const endDate = new Date(end);
      endDate.setHours(23, 59, 59, 999);
      if (parsed > endDate) {
        return false;
      }
    }

    return true;
  });
});

const hasFilters = computed(
  () =>
    !!searchTerm.value ||
    !!filterStart.value ||
    !!filterEnd.value ||
    !!statusFilter.value,
);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

function statusBadgeClass(status?: string): string {
  const value = (status ?? "").toLowerCase();
  if (value === "berhasil") {
    return "bg-emerald-100 text-emerald-700";
  }
  if (value === "ditolak") {
    return "bg-rose-100 text-rose-700";
  }
  if (value === "diproses" || value === "diverifikasi") {
    return "bg-amber-100 text-amber-700";
  }

  return "bg-slate-100 text-slate-700";
}

function formatDate(value?: string | null): string {
  if (!value) {
    return "-";
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleString("id-ID", {
    dateStyle: "medium",
    timeStyle: "short",
  });
}

function resetFilters() {
  searchTerm.value = "";
  filterStart.value = "";
  filterEnd.value = "";
  statusFilter.value = "";
  setPage(1);
}

async function loadPembayaran() {
  const response = await api.get("/pembayaran");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

async function loadNasabah() {
  const response = await api.get("/nasabah");
  nasabahOptions.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(async () => {
  await Promise.all([loadPembayaran(), loadNasabah()]);
});

watch([searchTerm, filterStart, filterEnd, statusFilter], () => {
  setPage(1);
});

watch(
  () => form.value.nasabah_id,
  async (val) => {
    form.value.transaksi_id = "";
    latestTransaksi.value = null;
    latestTransaksiError.value = "";
    if (val) {
      await Promise.all([
        fetchNasabahSaldo(),
        fetchLatestTransaksiNasabah(val),
      ]);
    } else {
      nasabahSaldo.value = null;
      nasabahSaldoError.value = "";
    }
  },
);

async function submitForm() {
  if (!form.value.transaksi_id) {
    return;
  }

  saving.value = true;
  try {
    await api.post("/pembayaran", {
      transaksi_id: Number(form.value.transaksi_id),
      jumlah: Number(form.value.jumlah || 0),
      metode: form.value.metode,
      status: form.value.status,
      tanggal: form.value.tanggal,
    });
    form.value = {
      nasabah_id: "",
      transaksi_id: "",
      jumlah: "",
      metode: "Cash",
      status: "menunggu",
      tanggal: new Date().toISOString().slice(0, 10),
    };
    nasabahSaldo.value = null;
    showForm.value = false;
    await loadPembayaran();
  } finally {
    saving.value = false;
  }
}

function isCashMethod(metode?: string): boolean {
  return (metode ?? "").toLowerCase() === "cash";
}

function getNasabahNama(item: Pembayaran): string {
  return item.nasabah?.nama || item.transaksi?.nasabah?.nama || "-";
}

async function updateStatus(item: Pembayaran, status: string) {
  if (!canVerifyPayment.value) {
    return;
  }

  processingId.value = item.id;
  try {
    await api.put(`/pembayaran/${item.id}`, {
      transaksi_id: item.transaksi_id,
      jumlah: item.jumlah,
      metode: item.metode,
      status,
      tanggal: item.tanggal,
    });
    await loadPembayaran();
  } finally {
    processingId.value = null;
  }
}

async function approvePayment(item: Pembayaran) {
  if (!canVerifyPayment.value) {
    return;
  }

  // Cash langsung berhasil, non-Cash jadi diverifikasi
  const targetStatus = isCashMethod(item.metode) ? "berhasil" : "diverifikasi";
  await updateStatus(item, targetStatus);
}
</script>

<template>
  <section class="space-y-4">
    <div
      class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
    >
      <div class="text-sm text-slate-500">
        Verifikasi pembayaran dan update status pencairan saldo nasabah.
      </div>
      <div
        class="grid gap-3 md:grid-cols-2 xl:grid-cols-[180px_180px_180px_auto_auto]"
      >
        <label class="text-xs text-slate-600">
          Dari
          <input
            v-model="filterStart"
            type="date"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <label class="text-xs text-slate-600">
          Sampai
          <input
            v-model="filterEnd"
            type="date"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <label class="text-xs text-slate-600">
          Status
          <select
            v-model="statusFilter"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          >
            <option value="">Semua</option>
            <option value="menunggu">Menunggu</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="diproses">Diproses</option>
            <option value="berhasil">Berhasil</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </label>
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama nasabah atau metode"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="canCreate"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup" : "Tambah Pembayaran" }}
          </button>
          <button
            type="button"
            class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700"
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
        <h3 class="text-xl font-semibold text-white">Data Pembayaran</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Status pembayaran dan verifikasi transaksi.
        </p>
      </div>
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Nasabah</th>
            <th class="px-5 py-4">Transaksi</th>
            <th class="px-5 py-4">Jumlah</th>
            <th class="px-5 py-4">Metode</th>
            <th class="px-5 py-4">Status</th>
            <th class="px-5 py-4">Verifier</th>
            <th class="px-5 py-4">Waktu Verifikasi</th>
            <th class="px-5 py-4">Tanggal</th>
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
            <td class="px-5 py-4 font-medium text-slate-700">
              {{ getNasabahNama(item) }}
            </td>
            <td class="px-5 py-4">#{{ item.transaksi_id }}</td>
            <td class="px-5 py-4">
              Rp {{ Number(item.jumlah || 0).toLocaleString("id-ID") }}
            </td>
            <td class="px-5 py-4">{{ item.metode }}</td>
            <td class="px-5 py-4">
              <span
                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                :class="statusBadgeClass(item.status)"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="px-5 py-4">{{ item.verifier?.nama || "-" }}</td>
            <td class="px-5 py-4">{{ formatDate(item.verified_at) }}</td>
            <td class="px-5 py-4">{{ formatDate(item.tanggal) }}</td>
            <td class="px-5 py-4">
              <button
                v-if="item.status === 'menunggu' && canVerifyPayment"
                class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-medium text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                :disabled="processingId === item.id"
                @click="approvePayment(item)"
              >
                {{
                  processingId === item.id
                    ? "Memproses..."
                    : isCashMethod(item.metode)
                      ? "Setujui"
                      : "Verifikasi"
                }}
              </button>
              <button
                v-if="
                  (item.status === 'menunggu' ||
                    item.status === 'diverifikasi') &&
                  canVerifyPayment
                "
                class="ml-2 rounded-lg bg-rose-500 px-3 py-2 text-xs font-medium text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                :disabled="processingId === item.id"
                @click="updateStatus(item, 'ditolak')"
              >
                {{ processingId === item.id ? "Memproses..." : "Tolak" }}
              </button>
              <span v-else class="text-xs text-slate-500">-</span>
            </td>
          </tr>
          <tr
            v-if="filteredRows.length === 0"
            class="border-t border-slate-200"
          >
            <td colspan="10" class="px-5 py-4">
              <div
                class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                role="alert"
              >
                Belum ada data pembayaran.
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
      class="rounded-[24px] border border-emerald-100 bg-emerald-50/40 p-5"
    >
      <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submitForm">
        <!-- Pilih Nasabah -->
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Nasabah
          </label>
          <select
            v-model="form.nasabah_id"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          >
            <option value="" disabled>Pilih nasabah</option>
            <option
              v-for="item in nasabahOptions"
              :key="item.id"
              :value="item.id"
            >
              {{ item.nama }}
            </option>
          </select>
          <!-- Saldo nasabah -->
          <div v-if="form.nasabah_id" class="mt-2 text-xs">
            <span v-if="nasabahSaldoLoading">Mengambil saldo...</span>
            <span v-else-if="nasabahSaldoError" class="text-rose-600">{{
              nasabahSaldoError
            }}</span>
            <span
              v-else-if="nasabahSaldo !== null"
              class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 font-semibold text-emerald-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-emerald-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
              Saldo:
              <b>Rp {{ Number(nasabahSaldo).toLocaleString("id-ID") }}</b>
            </span>
          </div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Jumlah
          </label>
          <input
            v-model="form.jumlah"
            type="number"
            min="0"
            step="0.01"
            placeholder="Contoh: 150000"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Metode
          </label>
          <select
            v-model="form.metode"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          >
            <option value="">Pilih Metode Pembayaran</option>
            <option value="Cash">Cash</option>
          </select>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Status
          </label>
          <select
            v-model="form.status"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          >
            <option value="menunggu">Menunggu</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="diproses">Diproses</option>
            <option value="berhasil">Berhasil</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Tanggal
          </label>
          <input
            v-model="form.tanggal"
            type="date"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>
        <div class="flex items-end">
          <button
            type="submit"
            class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
            :disabled="saving"
          >
            {{ saving ? "Menyimpan..." : "Simpan Pembayaran" }}
          </button>
        </div>
      </form>
    </div>
  </section>
</template>
