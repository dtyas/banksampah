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
const canApproveWithdraw = computed(() =>
  canDoOperation(authStore.user, "approveWithdraw"),
);
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
  await loadData();

  // Polling keeps disbursement status in sync with async webhook updates.
  refreshTimer = window.setInterval(loadData, 12000);
});

onUnmounted(() => {
  if (refreshTimer !== null) {
    window.clearInterval(refreshTimer);
  }
});
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <div
      class="flex items-center justify-between border-b border-slate-200 px-5 py-3"
    >
      <div class="text-sm text-slate-600">
        Status disbursement diperbarui otomatis setiap 12 detik.
      </div>
      <button
        class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"
        :disabled="refreshing"
        @click="loadData"
      >
        {{ refreshing ? "Memuat..." : "Refresh" }}
      </button>
    </div>
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
  </section>
</template>
