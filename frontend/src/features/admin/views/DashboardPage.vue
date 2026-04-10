<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";

type Summary = {
  total_nasabah: number;
  total_transaksi: number;
  total_berat: number;
  total_harga: number;
  total_pembayaran_berhasil: number;
};

const summary = ref<Summary | null>(null);
const balance = ref<number | null>(null);
const balanceLoading = ref(false);
const balanceError = ref("");
const filterStart = ref("");
const filterEnd = ref("");
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

onMounted(loadSummary);

async function loadBalance() {
  if (!isStaff.value) {
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

onMounted(loadBalance);
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
            >
              Filter
            </button>
            <button
              type="button"
              class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700"
            >
              Reset
            </button>
          </div>
        </div>

        <div class="mt-6 h-[340px] rounded-3xl bg-sky-50 p-6">
          <div class="flex h-full flex-col justify-between">
            <div class="text-sm text-slate-500">Data bulan ini</div>
            <svg
              class="h-56 w-full"
              viewBox="0 0 600 220"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20 180 C 80 120, 140 90, 200 110 S 320 190, 400 140 S 520 60, 580 70"
                stroke="#0ea5e9"
                stroke-width="4"
                fill="none"
              />
              <path
                d="M20 180 C 80 120, 140 90, 200 110 S 320 190, 400 140 S 520 60, 580 70"
                stroke="#0ea5e9"
                stroke-width="10"
                stroke-linecap="round"
                stroke-dasharray="2 48"
              />
            </svg>
            <div class="flex justify-between text-xs text-slate-500">
              <span>Minggu 1</span>
              <span>Minggu 2</span>
              <span>Minggu 3</span>
              <span>Minggu 4</span>
            </div>
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
