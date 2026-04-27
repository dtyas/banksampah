<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";
import { isFeatureEnabled } from "../../../config/features";

type PencairanRow = {
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

const rows = ref<PencairanRow[]>([]);
const refreshing = ref(false);
const processingId = ref<number | null>(null);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const canApproveWithdraw = computed(() =>
  canDoOperation(authStore.user, "approveWithdraw"),
);
const isNasabah = computed(() => authStore.user?.role === "nasabah");
const ledger = ref<{
  nasabah?: {
    payout_channel?: string | null;
    account_number?: string | null;
    account_holder_name?: string | null;
  };
  saldo?: {
    total_setoran: number;
    total_pencairan_berhasil: number;
    total_pencairan_pending: number;
    saldo_tersedia: number;
  };
  transaksi_terakhir?: Array<{
    id: number;
    tanggal: string;
    total_harga: number;
    total_berat: number;
  }>;
  pencairan_terakhir?: PencairanRow[];
} | null>(null);
const ledgerLoading = ref(false);
const ledgerError = ref("");
const withdrawError = ref("");
const accountSaving = ref(false);
const accountError = ref("");
const showForm = ref(false);
const showAccountForm = ref(false);
const saving = ref(false);
const transaksiOptions = ref<
  Array<{ id: number; nasabah_id?: number; nasabah?: { nama?: string } }>
>([]);

// State untuk nasabah (admin view)
const nasabahOptions = ref<Nasabah[]>([]);
const nasabahSaldo = ref<number | null>(null);
const nasabahSaldoLoading = ref(false);
const nasabahSaldoError = ref("");

const form = ref({
  nasabah_id: "",
  transaksi_id: "",
  jumlah: "",
  metode: "Pencairan Saldo",
  status: "diverifikasi",
  tanggal: new Date().toISOString().slice(0, 10),
});
const accountForm = ref({
  payout_channel: "",
  account_number: "",
  account_holder_name: "",
});

const xenditChannelOptions = computed(() => {
  if (isFeatureEnabled("enableXenditDisbursement")) {
    return [
      "ID_BCA",
      "ID_BNI",
      "ID_BRI",
      "ID_MANDIRI",
      "ID_OVO",
      "ID_DANA",
      "ID_GOPAY",
    ];
  }
  return [];
});

const methodOptions = computed(() => {
  const base = ["Cash"];
  if (isFeatureEnabled("enableXenditDisbursement")) {
    base.push(...xenditChannelOptions.value);
  }
  return base;
});
let refreshTimer: number | null = null;

const nasabahRows = computed(() => ledger.value?.pencairan_terakhir ?? []);
const latestPencairan = computed(() => nasabahRows.value[0] ?? null);
const { currentPage, totalPages, pagedRows, setPage } = usePagination(rows);
const {
  currentPage: nasabahPage,
  totalPages: nasabahPages,
  pagedRows: nasabahPagedRows,
  setPage: setNasabahPage,
} = usePagination(nasabahRows);

function isDisbursementMethod(metode?: string): boolean {
  const value = (metode ?? "").toLowerCase();
  const baseKeywords = ["cash", "pencairan"];
  const xenditKeywords = [
    "wallet",
    "transfer",
    "bank",
    "disbursement",
    "xendit",
  ];

  const allKeywords = isFeatureEnabled("enableXenditDisbursement")
    ? [...baseKeywords, ...xenditKeywords]
    : baseKeywords;

  return allKeywords.some((keyword) => value.includes(keyword));
}

function isCashMethod(metode?: string): boolean {
  return (metode ?? "").toLowerCase().includes("cash");
}

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

async function loadData() {
  refreshing.value = true;
  try {
    const response = await api.get("/pembayaran");
    const data = response.data?.data?.data ?? response.data?.data ?? [];
    rows.value = (data as PencairanRow[]).filter((item) =>
      isDisbursementMethod(item.metode),
    );
  } finally {
    refreshing.value = false;
  }
}

async function loadLedger() {
  if (!isNasabah.value) {
    return;
  }

  ledgerLoading.value = true;
  ledgerError.value = "";
  try {
    const response = await api.get("/nasabah/me/ledger");
    ledger.value = response.data?.data ?? null;
    form.value.metode = ledger.value?.nasabah?.payout_channel || "Cash";
    accountForm.value = {
      payout_channel: ledger.value?.nasabah?.payout_channel || "",
      account_number: ledger.value?.nasabah?.account_number || "",
      account_holder_name: ledger.value?.nasabah?.account_holder_name || "",
    };
  } catch (error) {
    ledgerError.value = "Ledger nasabah tidak tersedia";
  } finally {
    ledgerLoading.value = false;
  }
}

async function loadTransaksiNasabah() {
  if (!isNasabah.value) {
    return;
  }

  const response = await api.get("/nasabah/me/transaksi");
  const data = response.data?.data ?? [];
  transaksiOptions.value = (data as Array<{ id: number }>).map((item) => ({
    id: item.id,
    nasabah_id: undefined,
    nasabah: { nama: authStore.user?.nama || "Nasabah" },
  }));

  // Auto-select the latest transaction for nasabah
  if (transaksiOptions.value.length > 0) {
    const latest = transaksiOptions.value.reduce((prev, cur) => (cur.id > prev.id ? cur : prev));
    form.value.transaksi_id = String(latest.id);
  }
}

async function loadNasabah() {
  if (isNasabah.value) return;
  const response = await api.get("/nasabah");
  nasabahOptions.value = response.data?.data?.data ?? response.data?.data ?? [];
}

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

async function loadTransaksi() {
  if (isNasabah.value) {
    return;
  }
  const response = await api.get("/transaksi");
  transaksiOptions.value =
    response.data?.data?.data ?? response.data?.data ?? [];
}

// Transaksi difilter berdasarkan nasabah yang dipilih (admin view)
const filteredTransaksiOptions = computed(() => {
  if (isNasabah.value || !form.value.nasabah_id) return transaksiOptions.value;
  return transaksiOptions.value.filter(
    (t) => String((t as any).nasabah_id) === String(form.value.nasabah_id),
  );
});

// State transaksi terbaru nasabah untuk admin (auto-picked)
const latestTransaksi = ref<{ id: number; nasabah?: { nama?: string }; total_harga?: number } | null>(null);
const latestTransaksiLoading = ref(false);
const latestTransaksiError = ref("");

async function fetchLatestTransaksiNasabah(nasabahId: string) {
  latestTransaksi.value = null;
  latestTransaksiError.value = "";
  form.value.transaksi_id = "";
  if (!nasabahId) return;
  latestTransaksiLoading.value = true;
  try {
    const res = await api.get(`/transaksi?nasabah_id=${nasabahId}`);
    const list: Array<{ id: number; nasabah?: { nama?: string }; total_harga?: number }> =
      res.data?.data?.data ?? res.data?.data ?? [];
    if (list.length > 0) {
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

async function submitForm() {
  withdrawError.value = "";
  if (!form.value.transaksi_id) {
    return;
  }

  saving.value = true;
  try {
    if (isNasabah.value) {
      await api.post("/pencairan-saldo/request", {
        transaksi_id: Number(form.value.transaksi_id),
        jumlah: Number(form.value.jumlah || 0),
        metode: form.value.metode.trim(),
      });
    } else {
      await api.post("/pembayaran", {
        transaksi_id: Number(form.value.transaksi_id),
        jumlah: Number(form.value.jumlah || 0),
        metode: form.value.metode.trim(),
        status: form.value.status,
        tanggal: form.value.tanggal,
      });
    }
    form.value = {
      nasabah_id: "",
      transaksi_id: "",
      jumlah: "",
      metode: "Pencairan Saldo",
      status: "diverifikasi",
      tanggal: new Date().toISOString().slice(0, 10),
    };
    nasabahSaldo.value = null;
    showForm.value = false;
    if (isNasabah.value) {
      await loadLedger();
    } else {
      await loadData();
    }
  } catch (error) {
    withdrawError.value = "Pengajuan gagal, cek saldo dan data rekening.";
  } finally {
    saving.value = false;
  }
}

async function saveAccount() {
  accountError.value = "";
  accountSaving.value = true;
  try {
    await api.put("/nasabah/me/payout", {
      payout_channel: accountForm.value.payout_channel.trim(),
      account_number: accountForm.value.account_number.trim(),
      account_holder_name: accountForm.value.account_holder_name.trim(),
    });
    await loadLedger();
  } catch (error) {
    accountError.value = "Gagal menyimpan rekening/ewallet.";
  } finally {
    accountSaving.value = false;
  }
}

async function ajukanDisbursement(item: PencairanRow) {
  if (!canApproveWithdraw.value) {
    return;
  }

  if (isCashMethod(item.metode)) {
    await updateStatus(item, "berhasil");
    return;
  }

  processingId.value = item.id;
  try {
    // Backend update pembayaran currently requires full payload, not partial patch.
    await api.put(`/pembayaran/${item.id}`, {
      transaksi_id: item.transaksi_id,
      jumlah: item.jumlah,
      metode: item.metode,
      status: "diproses",
      tanggal: item.tanggal,
    });

    await loadData();
  } finally {
    processingId.value = null;
  }
}

async function updateStatus(item: PencairanRow, status: string) {
  if (!canApproveWithdraw.value) {
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

    await loadData();
  } finally {
    processingId.value = null;
  }
}

onMounted(async () => {
  if (isNasabah.value) {
    showForm.value = true;
    await Promise.all([loadLedger(), loadTransaksiNasabah()]);
  } else {
    await Promise.all([loadData(), loadNasabah()]);
  }

  // Polling keeps disbursement status in sync with async webhook updates.
  if (!isNasabah.value) {
    refreshTimer = window.setInterval(loadData, 12000);
  }
});

watch(
  () => form.value.nasabah_id,
  async (val) => {
    if (isNasabah.value) return;
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

onUnmounted(() => {
  if (refreshTimer !== null) {
    window.clearInterval(refreshTimer);
  }
});
</script>

<template>
  <section class="space-y-6">
    <template v-if="isNasabah">
      <div class="grid gap-4 md:grid-cols-2">
        <article
          class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
        >
          <p class="text-sm font-medium text-slate-500">Saldo Tersedia</p>
          <h3 class="mt-4 text-3xl font-bold text-slate-900">
            <span v-if="ledgerLoading">...</span>
            <span v-else
              >Rp {{ Number(ledger?.saldo || 0).toLocaleString("id-ID") }}</span
            >
          </h3>
          <p class="mt-2 text-xs text-emerald-600">
            Saldo dapat diajukan untuk pencairan kapan saja.
          </p>
        </article>
        <article
          class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
        >
          <p class="text-sm font-medium text-slate-500">Status Terakhir</p>
          <h3 class="mt-4 text-2xl font-bold text-slate-900">
            {{ latestPencairan?.status || "Belum ada" }}
          </h3>
          <p class="mt-2 text-xs text-amber-600">
            {{
              latestPencairan
                ? `Pengajuan terakhir ${formatDate(latestPencairan.tanggal)}`
                : "Ajukan pencairan pertama untuk mulai proses."
            }}
          </p>
        </article>
      </div>
      <p v-if="ledgerError" class="text-sm text-rose-500">{{ ledgerError }}</p>

      <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
        <section
          class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
        >
          <div class="border-b border-slate-100 px-6 py-5">
            <h3 class="text-lg font-semibold text-slate-900">
              Ajukan Pencairan Saldo
            </h3>
            <p class="mt-1 text-sm text-slate-500">
              Isi form berikut untuk mengajukan pencairan ke cash atau e-wallet.
            </p>
          </div>
          <form class="space-y-5 px-6 py-5" @submit.prevent="submitForm">
            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Nominal Pencairan
              </label>
              <input
                v-model="form.jumlah"
                type="number"
                min="0"
                step="0.01"
                placeholder="Contoh: 200000"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
                required
              />
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Metode Pencairan
              </label>
              <select
                v-model="form.metode"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
                required
              >
                <option value="">Pilih Metode Pencairan</option>
                <option
                  v-for="option in methodOptions"
                  :key="option"
                  :value="option"
                >
                  {{ option }}
                </option>
              </select>
              <p class="mt-2 text-xs text-slate-500">
                {{
                  isFeatureEnabled("enableXenditDisbursement") &&
                  !form.metode.toLowerCase().includes("cash")
                    ? "Dana dikirim via Xendit ke rekening/e-wallet terdaftar."
                    : "Cash diambil langsung ke petugas bank sampah."
                }}
              </p>
            </div>

            <div
              v-if="
                isFeatureEnabled('enableXenditDisbursement') &&
                !form.metode.toLowerCase().includes('cash')
              "
              class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-semibold text-slate-900">
                    Rekening/E-Wallet
                  </p>
                  <p class="text-xs text-slate-500">
                    Lengkapi data rekening untuk transfer otomatis.
                  </p>
                </div>
                <button
                  type="button"
                  class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"
                  @click="showAccountForm = !showAccountForm"
                >
                  {{ showAccountForm ? "Tutup" : "Atur Rekening" }}
                </button>
              </div>
              <div
                v-if="showAccountForm"
                class="mt-4 grid gap-4 md:grid-cols-3"
              >
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Channel
                  </label>
                  <select
                    v-model="accountForm.payout_channel"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm"
                  >
                    <option value="">Pilih channel</option>
                    <option
                      v-for="option in xenditChannelOptions"
                      :key="option"
                      :value="option"
                    >
                      {{ option }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Nomor Rekening/E-Wallet
                  </label>
                  <input
                    v-model="accountForm.account_number"
                    placeholder="Nomor rekening atau no HP e-wallet"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm"
                  />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700">
                    Nama Pemilik
                  </label>
                  <input
                    v-model="accountForm.account_holder_name"
                    placeholder="Nama pemilik rekening"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm"
                  />
                </div>
              </div>
              <div v-if="showAccountForm" class="mt-4 flex items-center gap-3">
                <button
                  type="button"
                  class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-70"
                  :disabled="accountSaving"
                  @click="saveAccount"
                >
                  {{ accountSaving ? "Menyimpan..." : "Simpan Rekening" }}
                </button>
                <span v-if="accountError" class="text-sm text-rose-600">
                  {{ accountError }}
                </span>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <button
                type="submit"
                class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
                :disabled="saving"
              >
                {{ saving ? "Menyimpan..." : "Ajukan Pencairan" }}
              </button>
              <span v-if="withdrawError" class="text-sm text-rose-600">
                {{ withdrawError }}
              </span>
            </div>
          </form>
        </section>

        <section
          class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
        >
          <div class="border-b border-slate-100 pb-4">
            <h3 class="text-lg font-semibold text-slate-900">
              Panduan Nasabah
            </h3>
            <p class="mt-1 text-sm text-slate-500">
              Langkah singkat sebelum mengajukan pencairan.
            </p>
          </div>
          <div class="mt-5 space-y-4">
            <div class="rounded-2xl bg-sky-50 px-4 py-4">
              <p class="text-sm font-semibold text-sky-700">1. Cek Saldo</p>
              <p class="mt-1 text-sm text-slate-600">
                Pastikan saldo tersedia cukup dan nominal yang diajukan sesuai.
              </p>
            </div>
            <div class="rounded-2xl bg-emerald-50 px-4 py-4">
              <p class="text-sm font-semibold text-emerald-700">
                2. Pilih Metode
              </p>
              <p class="mt-1 text-sm text-slate-600">
                Pilih cash atau e-wallet, lalu lengkapi data pencairan.
              </p>
            </div>
            <div class="rounded-2xl bg-amber-50 px-4 py-4">
              <p class="text-sm font-semibold text-amber-700">
                3. Tunggu Verifikasi
              </p>
              <p class="mt-1 text-sm text-slate-600">
                Admin akan meninjau permintaan sebelum status berubah berhasil.
              </p>
            </div>
          </div>
        </section>
      </div>

      <div
        class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
      >
        <div class="bg-emerald-700 px-5 py-4">
          <h3 class="text-xl font-semibold text-white">Riwayat Pencairan</h3>
          <p class="mt-1 text-sm text-emerald-100">
            Pengajuan pencairan saldo terakhir nasabah.
          </p>
        </div>
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-5 py-4">No</th>
              <th class="px-5 py-4">Tanggal</th>
              <th class="px-5 py-4">Jumlah</th>
              <th class="px-5 py-4">Status</th>
              <th class="px-5 py-4">Metode</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in nasabahPagedRows"
              :key="item.id"
              class="border-t border-slate-200"
            >
              <td class="px-5 py-4">
                {{ (nasabahPage - 1) * 10 + index + 1 }}
              </td>
              <td class="px-5 py-4">{{ formatDate(item.tanggal) }}</td>
              <td class="px-5 py-4">
                Rp {{ Number(item.jumlah || 0).toLocaleString("id-ID") }}
              </td>
              <td class="px-5 py-4">
                <span
                  class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                  :class="statusBadgeClass(item.status)"
                >
                  {{ item.status }}
                </span>
              </td>
              <td class="px-5 py-4">{{ item.metode }}</td>
            </tr>
            <tr
              v-if="nasabahRows.length === 0"
              class="border-t border-slate-200"
            >
              <td colspan="4" class="px-5 py-4">
                <div
                  class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                  role="alert"
                >
                  Belum ada pengajuan pencairan.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div
          class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4"
        >
          <p class="text-xs text-slate-500">
            Menampilkan {{ nasabahPagedRows.length }} dari
            {{ nasabahRows.length }}
            data
          </p>
          <div class="flex items-center gap-2 text-xs">
            <button
              class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
              :disabled="nasabahPage === 1"
              @click="setNasabahPage(nasabahPage - 1)"
            >
              Sebelumnya
            </button>
            <span class="text-slate-500">
              Halaman {{ nasabahPage }} / {{ nasabahPages }}
            </span>
            <button
              class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
              :disabled="nasabahPage === nasabahPages"
              @click="setNasabahPage(nasabahPage + 1)"
            >
              Berikutnya
            </button>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="flex items-center justify-between">
        <div class="text-sm text-slate-600">
          Status disbursement diperbarui otomatis setiap 12 detik. Metode cash
          diproses manual oleh petugas.
        </div>
        <div class="flex items-center gap-2">
          <button
            v-if="canCreate"
            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup" : "Tambah Pencairan" }}
          </button>
          <button
            class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"
            :disabled="refreshing"
            @click="loadData"
          >
            {{ refreshing ? "Memuat..." : "Refresh" }}
          </button>
        </div>
      </div>

      <div
        class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
      >
        <div class="bg-emerald-700 px-5 py-4">
          <h3 class="text-xl font-semibold text-white">Data Pencairan Saldo</h3>
          <p class="mt-1 text-sm text-emerald-100">
            Riwayat pencairan saldo nasabah dan statusnya.
          </p>
        </div>
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-5 py-4">No</th>
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
              <td class="px-5 py-4">
                {{ (currentPage - 1) * 10 + index + 1 }}
              </td>
              <td class="px-5 py-4">{{ item.transaksi_id }}</td>
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
                  v-if="item.status === 'menunggu' && canApproveWithdraw"
                  class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-medium text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                  :disabled="processingId === item.id"
                  @click="updateStatus(item, 'diverifikasi')"
                >
                  {{ processingId === item.id ? "Memproses..." : "Verifikasi" }}
                </button>
                <button
                  v-if="item.status === 'diverifikasi' && canApproveWithdraw"
                  class="ml-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                  :disabled="processingId === item.id"
                  @click="ajukanDisbursement(item)"
                >
                  {{
                    processingId === item.id
                      ? "Memproses..."
                      : isCashMethod(item.metode)
                        ? "Bayar Cash"
                        : "Ajukan Disbursement"
                  }}
                </button>
                <button
                  v-if="
                    (item.status === 'menunggu' ||
                      item.status === 'diverifikasi') &&
                    canApproveWithdraw
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
            <tr v-if="rows.length === 0" class="border-t border-slate-200">
              <td colspan="9" class="px-5 py-4">
                <div
                  class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                  role="alert"
                >
                  Belum ada data pencairan saldo.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div
          class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4"
        >
          <p class="text-xs text-slate-500">
            Menampilkan {{ pagedRows.length }} dari {{ rows.length }} data
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
          <!-- Pilih Nasabah (admin) -->
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
                v-for="n in nasabahOptions"
                :key="n.id"
                :value="n.id"
              >
                {{ n.nama }}
              </option>
            </select>
            <!-- Saldo nasabah -->
            <div v-if="form.nasabah_id" class="mt-2 text-xs">
              <span v-if="nasabahSaldoLoading">Mengambil saldo...</span>
              <span v-else-if="nasabahSaldoError" class="text-rose-600">{{ nasabahSaldoError }}</span>
              <span
                v-else-if="nasabahSaldo !== null"
                class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 font-semibold text-emerald-700"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Saldo: <b>Rp {{ Number(nasabahSaldo).toLocaleString("id-ID") }}</b>
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
              placeholder="Contoh: 200000"
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
              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
              required
            >
              <option value="">Pilih Metode Pencairan</option>
              <option
                v-for="option in methodOptions"
                :key="option"
                :value="option"
              >
                {{ option }}
              </option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
              Status
            </label>
            <select
              v-model="form.status"
              class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            >
              <option value="menunggu">Menunggu</option>
              <option value="diverifikasi">Diverifikasi</option>
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
              {{ saving ? "Mengajukan..." : "Ajukan Pencairan" }}
            </button>
          </div>
        </form>
        <p v-if="withdrawError" class="mt-2 text-sm text-rose-600">
          {{ withdrawError }}
        </p>
      </div>
    </template>
  </section>
</template>
