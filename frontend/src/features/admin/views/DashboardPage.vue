<script setup lang="ts">
import {
  computed,
  onMounted,
  onBeforeUnmount,
  ref,
  watch,
  nextTick,
} from "vue";
import Chart from "chart.js/auto";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { isFeatureEnabled } from "../../../config/features";

type Summary = {
  total_nasabah: number;
  total_transaksi: number;
  total_berat: number;
  total_harga: number;
  total_pembayaran_berhasil: number;
};

type ChartData = {
  labels: string[];
  datasets: {
    label: string;
    key: string;
    data: number[];
  }[];
};

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
    endOfMonth: formatDate(end),
  };
}

const summary = ref<Summary | null>(null);
const balance = ref<number | null>(null);
const balanceLoading = ref(false);
const balanceError = ref("");

// Initialize with default period (last 30 days)
const defaultPeriod = getDefaultPeriod();
const filterStart = ref(defaultPeriod.startOfMonth);
const filterEnd = ref(defaultPeriod.endOfMonth);

const chartData = ref<ChartData | null>(null);
const chartInstance = ref<Chart | null>(null);
const chartCanvasId = "transactionChart";
const authStore = useAuthStore();
const isStaff = computed(() =>
  ["super_admin", "petugas"].includes(authStore.user?.role ?? ""),
);

function formatCurrency(value?: number | null): string {
  if (value === null || value === undefined) {
    return "-";
  }
  return `Rp ${Number(value || 0).toLocaleString("id-ID")}`;
}

function formatWeight(value?: number | null): string {
  if (value === null || value === undefined) {
    return "-";
  }
  return `${Number(value || 0).toLocaleString("id-ID")} kg`;
}

async function loadSummary() {
  try {
    const response = await api.get("/laporan/summary");
    summary.value = response.data?.data ?? null;
  } catch (error) {
    summary.value = null;
  }
}

async function loadBalance() {
  if (!isStaff.value || !isFeatureEnabled("enableXenditDisbursement")) {
    return;
  }

  balanceLoading.value = true;
  balanceError.value = "";
  try {
    const response = await api.get("/xendit/balance", {
      params: {
        account_type: "CASH",
        currency: "IDR",
      },
    });
    balance.value = Number(response.data?.data?.balance ?? 0);
  } catch (error) {
    balanceError.value = "Saldo Xendit tidak tersedia";
  } finally {
    balanceLoading.value = false;
  }
}

async function loadChartData() {
  try {
    const params: Record<string, string> = {
      start_date: filterStart.value,
      end_date: filterEnd.value,
    };

    const response = await api.get("/laporan/chart", { params });
    chartData.value = response.data?.data ?? null;
    await nextTick();
    renderChart();
  } catch (error) {
    chartData.value = null;
  }
}

function renderChart() {
  const canvas = document.getElementById(chartCanvasId) as HTMLCanvasElement;
  if (!canvas || !chartData.value) return;

  // Destroy existing chart instance
  if (chartInstance.value) {
    chartInstance.value.destroy();
  }

  const ctx = canvas.getContext("2d");
  if (!ctx) return;

  chartInstance.value = new Chart(ctx, {
    type: "line",
    data: {
      labels: chartData.value.labels,
      datasets: chartData.value.datasets.map((dataset, index) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: index === 0 ? "#0ea5e9" : "#10b981",
        backgroundColor:
          index === 0 ? "rgba(14, 165, 233, 0.1)" : "rgba(16, 185, 129, 0.1)",
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: index === 0 ? "#0ea5e9" : "#10b981",
      })),
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: "top",
          labels: {
            usePointStyle: true,
            padding: 16,
            font: {
              size: 12,
            },
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
          backgroundColor: "rgba(0, 0, 0, 0.8)",
          titleFont: { size: 13 },
          bodyFont: { size: 12 },
          padding: 12,
          cornerRadius: 8,
        },
      },
      scales: {
        x: {
          grid: {
            display: false,
          },
          ticks: {
            font: {
              size: 11,
            },
          },
        },
        y: {
          beginAtZero: true,
          grid: {
            color: "rgba(0, 0, 0, 0.05)",
          },
          ticks: {
            font: {
              size: 11,
            },
          },
        },
      },
    },
  });
}

async function applyFilter() {
  await loadChartData();
}

function resetFilter() {
  // Reset to default period (last 30 days)
  const { startOfMonth, endOfMonth } = getDefaultPeriod();
  filterStart.value = startOfMonth;
  filterEnd.value = endOfMonth;
  loadChartData();
}

watch([filterStart, filterEnd], () => {
  // Auto-apply filter when dates change (optional, currently disabled)
  // applyFilter();
});

onMounted(() => {
  loadSummary();
  loadBalance();
  loadChartData();
});

onBeforeUnmount(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy();
  }
});
</script>

<template>
  <section class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Total Nasabah</p>
            <h3 class="mt-4 text-3xl font-bold text-slate-900">
              {{ summary?.total_nasabah ?? "-" }}
            </h3>
            <p class="mt-2 text-xs text-slate-500">
              Total transaksi: {{ summary?.total_transaksi ?? "-" }}
            </p>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16 11a4 4 0 10-8 0 4 4 0 008 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 20v-1a6 6 0 0112 0v1"
              />
            </svg>
          </div>
        </div>
      </article>

      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Total Sampah Masuk</p>
            <h3 class="mt-4 text-3xl font-bold text-slate-900">
              {{ formatWeight(summary?.total_berat ?? null) }}
            </h3>
            <p class="mt-2 text-xs text-slate-500">
              Nilai transaksi:
              {{ formatCurrency(summary?.total_harga ?? null) }}
            </p>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M7 9h10M7 13h10"
              />
            </svg>
          </div>
        </div>
      </article>

      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Total Saldo</p>
            <h3 class="mt-4 text-3xl font-bold text-slate-900">
              {{ formatCurrency(summary?.total_harga ?? null) }}
            </h3>
            <!-- do not remove , future use -->
            <template v-if="isFeatureEnabled('enableXenditDisbursement')">
              <p class="mt-2 text-xs text-amber-600">
                {{
                  balanceError
                    ? "Saldo Xendit tidak tersedia"
                    : isStaff
                      ? balanceLoading
                        ? "Memuat saldo Xendit..."
                        : `Saldo Xendit ${formatCurrency(balance)}`
                      : "Perlu verifikasi transaksi"
                }}
              </p>
              <p class="mt-2 text-xs text-slate-500">
                Pencairan berhasil:
                {{ formatCurrency(summary?.total_pembayaran_berhasil ?? null) }}
              </p>
            </template>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6v12m6-6H6"
              />
              <circle cx="12" cy="12" r="9" />
            </svg>
          </div>
        </div>
      </article>

      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500">Total Transaksi</p>
            <h3 class="mt-4 text-3xl font-bold text-slate-900">
              {{ summary?.total_transaksi ?? "-" }}
            </h3>
            <p class="mt-2 text-xs text-slate-500">
              Total nasabah: {{ summary?.total_nasabah ?? "-" }}
            </p>
          </div>
          <div
            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M8 11h8M8 15h5"
              />
            </svg>
          </div>
        </div>
      </article>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
      <section
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div
          class="flex flex-col gap-4 border-b border-slate-100 pb-5 md:flex-row md:items-center md:justify-between"
        >
          <div>
            <h3 class="text-lg font-semibold text-slate-900">
              Grafik Transaksi Sampah
            </h3>
            <p class="mt-1 text-sm text-slate-500">
              Pantau tren transaksi setoran sampah per bulan.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <label class="text-xs text-slate-500">
              Dari Tanggal
              <input
                v-model="filterStart"
                type="date"
                class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
              />
            </label>
            <label class="text-xs text-slate-500">
              Sampai Tanggal
              <input
                v-model="filterEnd"
                type="date"
                class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
              />
            </label>
            <button
              type="button"
              class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white"
              @click="applyFilter"
            >
              Filter
            </button>
            <button
              type="button"
              class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700"
              @click="resetFilter"
            >
              Reset
            </button>
          </div>
        </div>

        <div class="mt-6 h-[340px] rounded-3xl bg-sky-50 p-6">
          <div
            v-if="!chartData"
            class="flex h-full items-center justify-center"
          >
            <p class="text-sm text-slate-500">Memuat data chart...</p>
          </div>
          <div v-else class="relative h-full">
            <canvas :id="chartCanvasId"></canvas>
          </div>
        </div>
      </section>

      <section
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <div class="border-b border-slate-100 pb-4">
          <h3 class="text-lg font-semibold text-slate-900">
            Ringkasan Aktivitas
          </h3>
          <p class="mt-1 text-sm text-slate-500">
            Highlight performa bank sampah bulan ini.
          </p>
        </div>

        <div class="mt-5 space-y-4">
          <article class="rounded-2xl bg-sky-50 p-4">
            <p class="text-xs font-semibold text-sky-600">Setoran Tertinggi</p>
            <h4 class="mt-2 text-lg font-semibold text-slate-900">
              Plastik PET
            </h4>
            <p class="mt-1 text-xs text-slate-500">
              {{ formatWeight(summary?.total_berat ?? null) }} terkumpul bulan
              ini.
            </p>
          </article>
          <article class="rounded-2xl bg-emerald-50 p-4">
            <p class="text-xs font-semibold text-emerald-600">Nasabah Aktif</p>
            <h4 class="mt-2 text-lg font-semibold text-slate-900">
              {{ summary?.total_nasabah ?? "-" }} Nasabah
            </h4>
            <p class="mt-1 text-xs text-slate-500">
              Melakukan minimal 1 transaksi pada bulan ini.
            </p>
          </article>
          <article class="rounded-2xl bg-amber-50 p-4">
            <p class="text-xs font-semibold text-amber-600">Pencairan Saldo</p>
            <h4 class="mt-2 text-lg font-semibold text-slate-900">
              {{ formatCurrency(summary?.total_pembayaran_berhasil ?? null) }}
            </h4>
            <p class="mt-1 text-xs text-slate-500">
              Total saldo yang berhasil dicairkan bulan ini.
            </p>
          </article>
        </div>
      </section>
    </div>
  </section>
</template>
