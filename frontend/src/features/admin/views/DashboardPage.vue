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
const loading = ref(false);

async function loadSummary() {
  loading.value = true;
  try {
    const response = await api.get("/laporan/summary");
    summary.value = response.data?.data ?? null;
  } finally {
    loading.value = false;
  }
}

onMounted(loadSummary);
</script>

<template>
  <section>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Total Nasabah</p>
        <h3 class="mt-4 text-4xl font-bold text-slate-900">
          {{ summary?.total_nasabah ?? "-" }}
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Total Transaksi</p>
        <h3 class="mt-4 text-4xl font-bold text-slate-900">
          {{ summary?.total_transaksi ?? "-" }}
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Total Berat</p>
        <h3 class="mt-4 text-4xl font-bold text-slate-900">
          {{ summary ? `${summary.total_berat} Kg` : "-" }}
        </h3>
      </article>
      <article
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Total Harga</p>
        <h3 class="mt-4 text-4xl font-bold text-slate-900">
          {{
            summary ? `Rp ${summary.total_harga.toLocaleString("id-ID")}` : "-"
          }}
        </h3>
      </article>
    </div>

    <div
      class="mt-6 rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
    >
      <h3 class="text-lg font-semibold text-slate-900">
        Ringkasan Operasional
      </h3>
      <p class="mt-2 text-sm text-slate-500" v-if="loading">
        Memuat data dashboard...
      </p>
      <p class="mt-2 text-sm text-slate-500" v-else>
        Pencairan berhasil:
        <strong>{{
          summary
            ? `Rp ${summary.total_pembayaran_berhasil.toLocaleString("id-ID")}`
            : "-"
        }}</strong>
      </p>
    </div>
  </section>
</template>
