<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import Chart from "chart.js/auto";
import api from "../../../api/http";
import { usePagination } from "../../../composables/usePagination";

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
const chartCanvas = ref<HTMLCanvasElement | null>(null);
const chartInstance = ref<Chart | null>(null);
const transaksiRows = ref<LaporanTransaksi[]>([]);
const loading = ref(false);
const startDate = ref("");
const endDate = ref("");
const showFilter = ref(false);

// Auto-set default period (last 30 days) as fallback
function getDefaultPeriod() {
  const now = new Date();
  const end = now; // Today
  const start = new Date();
  start.setDate(start.getDate() - 30); // 30 days ago
  
  const formatDate = (date: Date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  };
  
  return { 
    startOfMonth: formatDate(start),
    endOfMonth: formatDate(end)
  };
}

const printablePeriod = computed(() => {
  if (!startDate.value && !endDate.value) {
    // Show current month period as default
    const { startOfMonth, endOfMonth } = getDefaultPeriod();
    return `${startOfMonth} s/d ${endOfMonth}`;
  }

  return `${startDate.value || "Awal"} s/d ${endDate.value || "Sekarang"}`;
});

const chartRows = computed(() => {
  if (!chart.value) {
    return [];
  }

  return chart.value.labels.map((label, index) => ({
    label,
    transaksi: chart.value?.datasets?.[0]?.data?.[index] ?? 0,
    totalHarga: chart.value?.datasets?.[1]?.data?.[index] ?? 0,
  }));
});

const {
  currentPage: chartPage,
  totalPages: chartPages,
  pagedRows: chartPagedRows,
  setPage: setChartPage,
} = usePagination(chartRows);
const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(transaksiRows);

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

function renderChart() {
  if (!chartCanvas.value) {
    return;
  }

  chartInstance.value?.destroy();

  const labels = chart.value?.labels ?? [];
  const transaksiData = chart.value?.datasets?.[0]?.data ?? [];
  const hargaData = chart.value?.datasets?.[1]?.data ?? [];

  chartInstance.value = new Chart(chartCanvas.value, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Jumlah Transaksi",
          data: transaksiData,
          borderColor: "#38bdf8",
          backgroundColor: "rgba(56,189,248,0.18)",
          tension: 0.35,
        },
        {
          label: "Total Harga",
          data: hargaData,
          borderColor: "#34d399",
          backgroundColor: "rgba(52,211,153,0.18)",
          tension: 0.35,
          yAxisID: "y1",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: "index", intersect: false },
      plugins: {
        legend: { position: "bottom" },
        tooltip: { enabled: true },
      },
      scales: {
        y: {
          ticks: { color: "#64748b" },
          grid: { color: "rgba(148,163,184,0.2)" },
        },
        y1: {
          position: "right",
          ticks: { color: "#64748b" },
          grid: { drawOnChartArea: false },
        },
        x: {
          ticks: { color: "#64748b" },
          grid: { display: false },
        },
      },
    },
  });
}

async function loadLaporan() {
  loading.value = true;
  try {
    const params = {
      ...(startDate.value ? { start_date: startDate.value } : {}),
      ...(endDate.value ? { end_date: endDate.value } : {}),
    };

    // Auto-apply default period if no filters set
    if (!startDate.value && !endDate.value) {
      const { startOfMonth, endOfMonth } = getDefaultPeriod();
      params.start_date = startOfMonth;
      params.end_date = endOfMonth;
    }

    const [summaryResponse, chartResponse, transaksiResponse] =
      await Promise.all([
        api.get("/laporan/summary", { params }),
        api.get("/laporan/chart", { params }),
        api.get("/laporan/transaksi", { params }),
      ]);
    summary.value = summaryResponse.data?.data ?? null;
    chart.value = chartResponse.data?.data ?? null;
    transaksiRows.value = transaksiResponse.data?.data ?? [];
    renderChart();
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

watch(chart, () => {
  renderChart();
});
</script>

<template>
  <section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="text-sm text-slate-500">
        Ringkasan laporan transaksi dan performa bank sampah.
      </div>
      <button
        type="button"
        class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
        @click="showFilter = !showFilter"
      >
        {{ showFilter ? "Tutup Filter" : "Filter Laporan" }}
      </button>
    </div>

    <div
      v-if="showFilter"
      class="no-print rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
    >
      <div
        class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between"
      >
        <div>
          <h3 class="text-lg font-semibold text-slate-900">Filter Laporan</h3>
          <p class="mt-1 text-sm text-slate-500">
            Pilih periode untuk menampilkan laporan yang dibutuhkan.
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
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
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <label class="text-sm text-slate-600">
          Dari Tanggal
          <input
            v-model="startDate"
            type="date"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
          />
        </label>
        <label class="text-sm text-slate-600">
          Sampai Tanggal
          <input
            v-model="endDate"
            type="date"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
          />
        </label>
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
      <div class="rounded-2xl bg-emerald-700 px-5 py-4 text-white">
        <h3 class="text-lg font-semibold">Data Chart (Backend)</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Periode laporan: {{ printablePeriod }}
        </p>
      </div>
      <div class="mt-4 overflow-x-auto">
        <div class="mb-4 h-[320px]">
          <canvas ref="chartCanvas"></canvas>
        </div>
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
              v-for="item in chartPagedRows"
              :key="item.label"
              class="border-t border-slate-200"
            >
              <td class="px-4 py-3">{{ item.label }}</td>
              <td class="px-4 py-3">{{ item.transaksi }}</td>
              <td class="px-4 py-3">{{ item.totalHarga }}</td>
            </tr>
            <tr v-if="chartRows.length === 0" class="border-t border-slate-200">
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
      <div
        class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4"
      >
        <p class="text-xs text-slate-500">
          Menampilkan {{ chartPagedRows.length }} dari {{ chartRows.length }}
          data
        </p>
        <div class="flex items-center gap-2 text-xs">
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="chartPage === 1"
            @click="setChartPage(chartPage - 1)"
          >
            Sebelumnya
          </button>
          <span class="text-slate-500">
            Halaman {{ chartPage }} / {{ chartPages }}
          </span>
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="chartPage === chartPages"
            @click="setChartPage(chartPage + 1)"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="rounded-2xl bg-emerald-700 px-5 py-4 text-white">
        <h3 class="text-lg font-semibold">Laporan Transaksi</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Ringkasan transaksi sesuai filter laporan.
        </p>
      </div>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3">No</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Nasabah</th>
              <th class="px-4 py-3">Total Berat</th>
              <th class="px-4 py-3">Total Harga</th>
              <th class="px-4 py-3">Status Pembayaran</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in pagedRows"
              :key="item.id"
              class="border-t border-slate-200"
            >
              <td class="px-4 py-3">{{ (currentPage - 1) * 10 + index + 1 }}</td>
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
      <div
        class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4"
      >
        <p class="text-xs text-slate-500">
          Menampilkan {{ pagedRows.length }} dari {{ transaksiRows.length }}
          data
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
