<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch, nextTick } from "vue";
import Chart from "chart.js/auto";
import api from "../../../api/http";
import { usePagination } from "../../../composables/usePagination";

type Summary = {
  total_nasabah: number;
  total_transaksi: number;
  total_berat: number;
  total_harga: number;
  total_pembayaran_berhasil: number;
  top_sampah: string;
  top_kategori: string;
  top_berat: number;
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
  detail_transaksi?: Array<{
    id: number;
    berat: number;
    subtotal: number;
    sampah?: {
      nama_sampah: string;
    } | null;
  }>;
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
const filterNasabah = ref("");
const filterSampah = ref("");
const nasabahOptions = ref<Array<{ id: number; nama: string }>>([]);
const sampahOptions = ref<Array<{ id: number; nama_sampah: string }>>([]);
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

const chartInsight = computed(() => {
  if (!chart.value || chart.value.labels.length < 2) {
    return null;
  }

  const datasets = chart.value.datasets;
  const labels = chart.value.labels;
  const countData = datasets[0].data;
  const amountData = datasets[1].data;

  const currentCount = countData[countData.length - 1];
  const prevCount = countData[countData.length - 2];
  const currentAmount = amountData[amountData.length - 1];
  const prevAmount = amountData[amountData.length - 2];

  const countDiff = currentCount - prevCount;
  const amountDiff = currentAmount - prevAmount;

  const countTrend = prevCount !== 0 ? (countDiff / prevCount) * 100 : 0;
  const amountTrend = prevAmount !== 0 ? (amountDiff / prevAmount) * 100 : 0;

  // Temukan puncak tertinggi
  const maxAmount = Math.max(...amountData);
  const peakIndex = amountData.indexOf(maxAmount);
  const peakLabel = labels[peakIndex];

  return {
    countTrend,
    amountTrend,
    peakLabel,
    peakAmount: maxAmount,
    isCountUp: countDiff >= 0,
    isAmountUp: amountDiff >= 0,
    lastPeriod: labels[labels.length - 1],
  };
});

function statusBadgeClass(status?: string): string {
  const value = (status ?? "").toLowerCase();
  if (value === "berhasil" || !value) {
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
    console.warn("Canvas element tidak ditemukan");
    return;
  }

  // ✅ Pastikan context canvas valid
  const ctx = chartCanvas.value.getContext("2d");
  if (!ctx) {
    console.warn("Canvas context tidak tersedia");
    return;
  }

  chartInstance.value?.destroy();

  const labels = chart.value?.labels ?? [];
  const transaksiData = chart.value?.datasets?.[0]?.data ?? [];
  const hargaData = chart.value?.datasets?.[1]?.data ?? [];

  if (labels.length === 0) {
    console.log("Tidak ada data untuk ditampilkan di chart");
    return;
  }

  chartInstance.value = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Jumlah Transaksi",
          data: transaksiData,
          borderColor: "#0ea5e9",
          backgroundColor: "rgba(14,165,233,0.15)",
          fill: true,
          tension: 0.1,
          pointRadius: 4,
          pointBackgroundColor: "#0ea5e9",
          borderWidth: 3,
        },
        {
          label: "Total Harga",
          data: hargaData,
          borderColor: "#10b981",
          backgroundColor: "rgba(16,185,129,0.15)",
          fill: true,
          tension: 0.1,
          pointRadius: 4,
          pointBackgroundColor: "#10b981",
          borderWidth: 3,
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
          beginAtZero: true, // ✅ Tambahkan agar skala lebih rapi
          ticks: { color: "#64748b" },
          grid: { color: "rgba(148,163,184,0.2)" },
        },
        y1: {
          position: "right",
          beginAtZero: true,
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
    const params: any = {
      ...(startDate.value ? { start_date: startDate.value } : {}),
      ...(endDate.value ? { end_date: endDate.value } : {}),
      ...(filterNasabah.value ? { nasabah_id: filterNasabah.value } : {}),
      ...(filterSampah.value ? { sampah_id: filterSampah.value } : {}),
    };

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
    
    // ✅ SANITASI DATA CHART - Ganti null/undefined dengan 0
    const rawChart = chartResponse.data?.data ?? null;
    if (rawChart) {
      chart.value = {
        labels: rawChart.labels ?? [],
        datasets: [
          {
            label: "Jumlah Transaksi",
            data: (rawChart.datasets?.[0]?.data ?? []).map((v: any) => 
              typeof v === 'number' ? v : 0
            ),
          },
          {
            label: "Total Harga",
            data: (rawChart.datasets?.[1]?.data ?? []).map((v: any) => 
              typeof v === 'number' ? v : 0
            ),
          },
        ],
      };
    } else {
      chart.value = null;
    }
    
    transaksiRows.value = transaksiResponse.data?.data ?? [];
    
    // Kita tidak perlu panggil renderChart() di sini karena ada watcher pada 'chart'
    
  } catch (error) {
    console.error("Gagal memuat laporan:", error);
  } finally {
    loading.value = false;
  }
}

function resetFilter() {
  startDate.value = "";
  endDate.value = "";
  filterNasabah.value = "";
  filterSampah.value = "";
  void loadLaporan();
}

async function loadOptions() {
  try {
    const [nasabahRes, sampahRes] = await Promise.all([
      api.get("/nasabah"),
      api.get("/sampah"),
    ]);
    nasabahOptions.value = nasabahRes.data?.data?.data ?? nasabahRes.data?.data ?? [];
    sampahOptions.value = sampahRes.data?.data?.data ?? sampahRes.data?.data ?? [];
  } catch (e) {
    console.error("Gagal memuat opsi filter:", e);
  }
}

function printLaporan() {
  window.print();
}

onMounted(() => {
  void loadLaporan();
  void loadOptions();
});

onBeforeUnmount(() => {
  chartInstance.value?.destroy();
});

watch(chart, async () => {
  await nextTick();
  renderChart();
});
</script>

<template>
  <section class="space-y-6">
    <!-- Print Only Header -->
    <div class="print-only mb-8 text-center border-b-2 border-slate-900 pb-6">
      <h1 class="text-3xl font-bold uppercase tracking-tight">Laporan Operasional Bank Sampah</h1>
      <p class="mt-2 text-lg text-slate-600">Sistem Informasi Bank Sampah Sedap Malam</p>
      <div class="mt-4 flex justify-center gap-8 text-sm font-medium">
        <span>Periode: {{ printablePeriod }}</span>
        <span>Dicetak pada: {{ new Date().toLocaleString('id-ID') }}</span>
      </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 no-print">
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
        <label class="text-sm text-slate-600">
          Filter Nasabah
          <select
            v-model="filterNasabah"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
          >
            <option value="">Semua Nasabah</option>
            <option v-for="n in nasabahOptions" :key="n.id" :value="n.id">{{ n.nama }}</option>
          </select>
        </label>
        <label class="text-sm text-slate-600">
          Jenis Sampah
          <select
            v-model="filterSampah"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
          >
            <option value="">Semua Jenis</option>
            <option v-for="s in sampahOptions" :key="s.id" :value="s.id">{{ s.nama_sampah }}</option>
          </select>
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
      <article class="rounded-[24px] bg-sky-50 p-5 ring-1 ring-sky-100">
        <p class="text-sm text-sky-600 font-semibold">Setoran Tertinggi</p>
        <h3 class="mt-2 text-2xl font-bold text-slate-900">
          {{ summary?.top_sampah ?? "-" }}
        </h3>
        <p class="mt-1 text-xs text-slate-500">
          {{ summary?.top_kategori ? `Kategori: ${summary.top_kategori}` : "" }}
          <span v-if="summary?.top_berat" class="ml-1 text-sky-700 font-medium">
            ({{ summary.top_berat }} Kg)
          </span>
        </p>
      </article>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="rounded-2xl bg-emerald-700 px-5 py-4 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold">Data Chart Performa</h3>
            <p class="mt-1 text-sm text-emerald-100">
              Periode laporan: {{ printablePeriod }}
            </p>
          </div>
          
          <!-- Chart Insight Analysis -->
          <div v-if="chartInsight" class="flex flex-wrap gap-3">
            <div class="rounded-xl bg-white/10 px-3 py-2 backdrop-blur-sm">
              <p class="text-[10px] uppercase tracking-wider text-emerald-200">Tren Transaksi</p>
              <p class="flex items-center gap-1 text-sm font-bold">
                <span :class="chartInsight.isCountUp ? 'text-emerald-300' : 'text-rose-300'">
                  {{ chartInsight.isCountUp ? '↑' : '↓' }} {{ Math.abs(chartInsight.countTrend).toFixed(1) }}%
                </span>
              </p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-2 backdrop-blur-sm">
              <p class="text-[10px] uppercase tracking-wider text-emerald-200">Tren Omzet</p>
              <p class="flex items-center gap-1 text-sm font-bold">
                <span :class="chartInsight.isAmountUp ? 'text-emerald-300' : 'text-rose-300'">
                  {{ chartInsight.isAmountUp ? '↑' : '↓' }} {{ Math.abs(chartInsight.amountTrend).toFixed(1) }}%
                </span>
              </p>
            </div>
            <div class="hidden lg:block rounded-xl bg-white/10 px-3 py-2 backdrop-blur-sm">
              <p class="text-[10px] uppercase tracking-wider text-emerald-200">Puncak Performa</p>
              <p class="text-sm font-bold">{{ chartInsight.peakLabel }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Insight Text Description -->
      <div v-if="chartInsight" class="mt-4 rounded-2xl bg-slate-50 border border-slate-100 p-4">
        <div class="flex items-start gap-3">
          <div class="mt-1 rounded-full bg-emerald-100 p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-slate-900">Analisis Perkembangan</h4>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">
              Pada periode terakhir (<b>{{ chartInsight.lastPeriod }}</b>), terjadi 
              {{ chartInsight.isCountUp ? 'kenaikan' : 'penurunan' }} aktivitas sebesar <b>{{ Math.abs(chartInsight.countTrend).toFixed(1) }}%</b> 
              dibandingkan periode sebelumnya. Secara finansial, nilai setoran 
              {{ chartInsight.isAmountUp ? 'meningkat' : 'menurun' }} sebanyak <b>{{ Math.abs(chartInsight.amountTrend).toFixed(1) }}%</b>.
              Performa tertinggi tercapai pada <b>{{ chartInsight.peakLabel }}</b> dengan total setoran mencapai <b>{{ toCurrency(chartInsight.peakAmount) }}</b>.
            </p>
          </div>
        </div>
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
              <th class="px-4 py-3">Rincian Sampah</th>
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
              <td class="px-4 py-3">
                <div class="text-xs space-y-0.5">
                  <div v-for="detail in item.detail_transaksi" :key="detail.id" class="text-slate-600">
                    • {{ detail.sampah?.nama_sampah || 'Sampah' }}: {{ detail.berat }}Kg
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">{{ Number(item.total_berat || 0) }} Kg</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ toCurrency(item.total_harga) }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                  :class="statusBadgeClass(item.pembayaran?.status)"
                >
                  {{ item.pembayaran?.status || "Berhasil" }}
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
.print-only {
  display: none;
}

@media print {
  /* Hide unnecessary elements from the Layout */
  :deep(aside), 
  :deep(header), 
  :deep(footer),
  .no-print {
    display: none !important;
  }

  .print-only {
    display: block !important;
  }

  /* Reset main container layout for printing */
  :deep(main) {
    margin: 0 !important;
    padding: 0 !important;
    min-height: auto !important;
  }

  :deep(.flex) {
    display: block !important;
  }

  section {
    padding: 0 !important;
    margin: 0 !important;
    background: #fff;
  }

  /* Force cards to show border when printing */
  .ring-1 {
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
  }

  .rounded-\[28px\] {
    border-radius: 12px !important;
  }
}
</style>
