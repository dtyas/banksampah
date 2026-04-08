<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

type Summary = {
  total_nasabah: number;
  total_transaksi: number;
  total_berat: number;
  total_harga: number;
  total_pembayaran_berhasil: number;
};

const summary = ref<Summary | null>(null);
const chart = ref<{
  labels: string[];
  datasets: Array<{ label: string; data: number[] }>;
} | null>(null);

async function loadLaporan() {
  const [summaryResponse, chartResponse] = await Promise.all([
    api.get("/laporan/summary"),
    api.get("/laporan/chart"),
  ]);
  summary.value = summaryResponse.data?.data ?? null;
  chart.value = chartResponse.data?.data ?? null;
}

onMounted(loadLaporan);
</script>

<template>
  <section class="space-y-6">
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
          {{
            summary ? `Rp ${summary.total_harga.toLocaleString("id-ID")}` : "-"
          }}
        </h3>
      </article>
    </div>

    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Data Chart (Backend)</h3>
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
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>
