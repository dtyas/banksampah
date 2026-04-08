<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import api from "../../../api/http";

type Summary = {
  total_nasabah: number;
  total_transaksi: number;
  total_berat: number;
  total_harga: number;
  total_pembayaran_berhasil: number;
};

type LaporanTransaksi = {
  id: number;
  tanggal: string;
  total_berat: number;
  total_harga: number;
  nasabah?: {
    id: number;
    nama: string;
  } | null;
  pembayaran?: {
    status?: string;
    jumlah?: number;
  } | null;
};

const summary = ref<Summary | null>(null);
const chart = ref<{
  labels: string[];
  datasets: Array<{ label: string; data: number[] }>;
} | null>(null);
const transaksiRows = ref<LaporanTransaksi[]>([]);
const loading = ref(false);
const startDate = ref("");
const endDate = ref("");

const printablePeriod = computed(() => {
  if (!startDate.value && !endDate.value) {
    return "Semua Periode";
  }

  return `${startDate.value || "Awal"} s/d ${endDate.value || "Sekarang"}`;
});

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

function formatDate(value?: string): string {
  if (!value) {
    return "-";
  }

  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return value;
  }

  return parsed.toLocaleDateString("id-ID", { dateStyle: "medium" });
}

function toCurrency(value: number | undefined): string {
  return `Rp ${Number(value ?? 0).toLocaleString("id-ID")}`;
}

async function loadLaporan() {
  loading.value = true;
  try {
    const params = {
      ...(startDate.value ? { start_date: startDate.value } : {}),
      ...(endDate.value ? { end_date: endDate.value } : {}),
    };

    const [summaryResponse, chartResponse, transaksiResponse] = await Promise.all([
      api.get("/laporan/summary", { params }),
      api.get("/laporan/chart", { params }),
      api.get("/laporan/transaksi", { params }),
    ]);
    summary.value = summaryResponse.data?.data ?? null;
    chart.value = chartResponse.data?.data ?? null;
    transaksiRows.value = transaksiResponse.data?.data ?? [];
  } finally {
    loading.value = false;
  }
}

function resetFilter() {
  startDate.value = "";
  endDate.value = "";
  void loadLaporan();
}

function printLaporan() {
  window.print();
}

onMounted(loadLaporan);
</script>

<template>
  <section class="space-y-6">
    <div class="no-print rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Filter Laporan</h3>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <label class="text-sm text-slate-600">
          Dari Tanggal
          <input
            v-model="startDate"
            type="date"
            class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2"
          />
        </label>
        <label class="text-sm text-slate-600">
          Sampai Tanggal
          <input
            v-model="endDate"
            type="date"
            class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2"
          />
        </label>
      </div>
      <div class="mt-4 flex flex-wrap gap-2">
        <button
          class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white"
          :disabled="loading"
          @click="loadLaporan"
        >
          {{ loading ? "Memuat..." : "Terapkan Filter" }}
        </button>
        <button
          class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700"
          :disabled="loading"
          @click="resetFilter"
        >
          Reset
        </button>
        <button
          class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-medium text-white"
          @click="printLaporan"
        >
          Cetak Laporan
        </button>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <article class="rounded-[24px] bg-white p-5 ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Total Nasabah</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary?.total_nasabah ?? "-" }}
        </h3>
      </article>
      <article class="rounded-[24px] bg-white p-5 ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Total Transaksi</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary?.total_transaksi ?? "-" }}
        </h3>
      </article>
      <article class="rounded-[24px] bg-white p-5 ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Total Berat</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary ? `${summary.total_berat} Kg` : "-" }}
        </h3>
      </article>
      <article class="rounded-[24px] bg-white p-5 ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Total Harga</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary ? toCurrency(summary.total_harga) : "-" }}
        </h3>
      </article>
      <article class="rounded-[24px] bg-white p-5 ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Total Pembayaran Berhasil</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary ? toCurrency(summary.total_pembayaran_berhasil) : "-" }}
        </h3>
      </article>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Data Chart (Backend)</h3>
      <p class="mt-1 text-sm text-slate-500">
        Periode laporan: {{ printablePeriod }}
      </p>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3">Periode</th>
              <th class="px-4 py-3">Jumlah Transaksi</th>
              <th class="px-4 py-3">Total Harga</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(label, idx) in chart?.labels || []"
              :key="label"
              class="border-t border-slate-200"
            >
              <td class="px-4 py-3">{{ label }}</td>
              <td class="px-4 py-3">
                {{ chart?.datasets?.[0]?.data?.[idx] ?? 0 }}
              </td>
              <td class="px-4 py-3">
                {{ chart?.datasets?.[1]?.data?.[idx] ?? 0 }}
              </td>
            </tr>
            <tr
              v-if="(chart?.labels?.length ?? 0) === 0"
              class="border-t border-slate-200"
            >
              <td colspan="3" class="px-4 py-3">
                <div
                  class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                  role="alert"
                >
                  Belum ada data laporan.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Laporan Transaksi</h3>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Nasabah</th>
              <th class="px-4 py-3">Total Berat</th>
              <th class="px-4 py-3">Total Harga</th>
              <th class="px-4 py-3">Status Pembayaran</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in transaksiRows"
              :key="item.id"
              class="border-t border-slate-200"
            >
              <td class="px-4 py-3">{{ formatDate(item.tanggal) }}</td>
              <td class="px-4 py-3">{{ item.nasabah?.nama || "-" }}</td>
              <td class="px-4 py-3">{{ Number(item.total_berat || 0) }} Kg</td>
              <td class="px-4 py-3">{{ toCurrency(item.total_harga) }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                  :class="statusBadgeClass(item.pembayaran?.status)"
                >
                  {{ item.pembayaran?.status || "-" }}
                </span>
              </td>
            </tr>
            <tr
              v-if="transaksiRows.length === 0"
              class="border-t border-slate-200"
            >
              <td colspan="5" class="px-4 py-3">
                <div
                  class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
                  role="alert"
                >
                  Belum ada data laporan transaksi.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<style scoped>
@media print {
  .no-print {
    display: none;
  }

  section {
    background: #fff;
  }
}
</style>
