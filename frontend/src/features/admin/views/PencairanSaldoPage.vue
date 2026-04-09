<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";

type PencairanRow = {
  id: number;
  transaksi_id: number;
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
const saving = ref(false);
const transaksiOptions = ref<
  Array<{ id: number; nasabah?: { nama?: string } }>
>([]);
const form = ref({
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
const channelOptions = [
  "ID_BCA",
  "ID_BNI",
  "ID_BRI",
  "ID_MANDIRI",
  "ID_OVO",
  "ID_DANA",
  "ID_GOPAY",
];
let refreshTimer: number | null = null;

function isDisbursementMethod(metode?: string): boolean {
  const value = (metode ?? "").toLowerCase();

  return [
    "wallet",
    "transfer",
    "bank",
    "disbursement",
    "xendit",
    "pencairan",
  ].some((keyword) => value.includes(keyword));
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
    if (ledger.value?.nasabah?.payout_channel) {
      form.value.metode = ledger.value.nasabah.payout_channel;
    }
    accountForm.value = {
      payout_channel: ledger.value?.nasabah?.payout_channel || "",
      account_number: ledger.value?.nasabah?.account_number || "",
      account_holder_name: ledger.value?.nasabah?.account_holder_name || "",
    };
    transaksiOptions.value = (ledger.value?.transaksi_terakhir || []).map(
      (item) => ({
        id: item.id,
        nasabah: { nama: authStore.user?.nama || "Nasabah" },
      }),
    );
  } catch (error) {
    ledgerError.value = "Ledger nasabah tidak tersedia";
  } finally {
    ledgerLoading.value = false;
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
      transaksi_id: "",
      jumlah: "",
      metode: "Pencairan Saldo",
      status: "diverifikasi",
      tanggal: new Date().toISOString().slice(0, 10),
    };
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

onMounted(async () => {
  if (isNasabah.value) {
    await Promise.all([loadLedger(), loadTransaksi()]);
  } else {
    await Promise.all([loadData(), loadTransaksi()]);
  }

  // Polling keeps disbursement status in sync with async webhook updates.
  if (!isNasabah.value) {
    refreshTimer = window.setInterval(loadData, 12000);
  }
});

onUnmounted(() => {
  if (refreshTimer !== null) {
    window.clearInterval(refreshTimer);
  }
});
</script>

<template>
  <section class="space-y-4">
    <div v-if="isNasabah" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Saldo Tersedia</p>
        <h3 class="mt-4 text-3xl font-bold text-slate-900">
          <span v-if="ledgerLoading">...</span>
          <span v-else
            >Rp
            {{
              Number(ledger?.saldo?.saldo_tersedia || 0).toLocaleString("id-ID")
            }}</span
          >
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Total Setoran</p>
        <h3 class="mt-4 text-3xl font-bold text-slate-900">
          Rp
          {{
            Number(ledger?.saldo?.total_setoran || 0).toLocaleString("id-ID")
          }}
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Pencairan Berhasil</p>
        <h3 class="mt-4 text-3xl font-bold text-slate-900">
          Rp
          {{
            Number(ledger?.saldo?.total_pencairan_berhasil || 0).toLocaleString(
              "id-ID",
            )
          }}
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Pencairan Pending</p>
        <h3 class="mt-4 text-3xl font-bold text-slate-900">
          Rp
          {{
            Number(ledger?.saldo?.total_pencairan_pending || 0).toLocaleString(
              "id-ID",
            )
          }}
        </h3>
      </article>
    </div>
    <p v-if="ledgerError" class="text-sm text-rose-500">{{ ledgerError }}</p>
    <div
      v-if="isNasabah"
      class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
    >
      <h3 class="text-lg font-semibold text-slate-900">Rekening/E-Wallet</h3>
      <p class="mt-1 text-sm text-slate-500">
        Lengkapi data rekening agar pencairan dapat diproses.
      </p>
      <div class="mt-4 grid gap-4 md:grid-cols-3">
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
              v-for="option in channelOptions"
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
      <div class="mt-4 flex items-center gap-3">
        <button
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
    <div class="flex items-center justify-between">
      <div class="text-sm text-slate-600">
        <span v-if="isNasabah">Ajukan pencairan saldo dari buku tabungan.</span>
        <span v-else>
          Status disbursement diperbarui otomatis setiap 12 detik.</span
        >
      </div>
      <div class="flex items-center gap-2">
        <button
          v-if="canCreate || isNasabah"
          class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white"
          @click="showForm = !showForm"
        >
          {{
            showForm
              ? "Tutup"
              : isNasabah
                ? "Ajukan Pencairan"
                : "Tambah Pencairan"
          }}
        </button>
        <button
          v-if="!isNasabah"
          class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"
          :disabled="refreshing"
          @click="loadData"
        >
          {{ refreshing ? "Memuat..." : "Refresh" }}
        </button>
      </div>
    </div>

    <div
      v-if="showForm"
      class="rounded-[24px] border border-emerald-100 bg-emerald-50/40 p-5"
    >
      <form class="grid gap-4 lg:grid-cols-2" @submit.prevent="submitForm">
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Transaksi
          </label>
          <select
            v-model="form.transaksi_id"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          >
            <option value="" disabled>Pilih transaksi</option>
            <option
              v-for="item in transaksiOptions"
              :key="item.id"
              :value="item.id"
            >
              #{{ item.id }} - {{ item.nasabah?.nama || "Nasabah" }}
            </option>
          </select>
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
          <input
            v-model="form.metode"
            type="text"
            placeholder="Contoh: BCA, DANA, OVO"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Status
          </label>
          <select
            v-model="form.status"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            :disabled="isNasabah"
          >
            <option value="menunggu">Menunggu</option>
            <option value="diverifikasi">Diverifikasi</option>
          </select>
        </div>
        <div v-if="!isNasabah">
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
            {{
              saving
                ? "Menyimpan..."
                : isNasabah
                  ? "Ajukan"
                  : "Simpan Pencairan"
            }}
          </button>
        </div>
      </form>
      <p v-if="withdrawError" class="mt-2 text-sm text-rose-600">
        {{ withdrawError }}
      </p>
    </div>

    <div
      v-if="!isNasabah"
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">ID</th>
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
            v-for="item in rows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ item.id }}</td>
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
                v-if="item.status === 'diverifikasi' && canApproveWithdraw"
                class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white disabled:cursor-not-allowed disabled:bg-slate-300"
                :disabled="processingId === item.id"
                @click="ajukanDisbursement(item)"
              >
                {{
                  processingId === item.id
                    ? "Memproses..."
                    : "Ajukan Disbursement"
                }}
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
    </div>

    <div
      v-else
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">Tanggal</th>
            <th class="px-5 py-4">Jumlah</th>
            <th class="px-5 py-4">Status</th>
            <th class="px-5 py-4">Metode</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in ledger?.pencairan_terakhir || []"
            :key="item.id"
            class="border-t border-slate-200"
          >
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
            v-if="(ledger?.pencairan_terakhir || []).length === 0"
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
    </div>
  </section>
</template>
