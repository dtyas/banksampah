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
const loading = ref(false);
const balance = ref<number | null>(null);
const balanceLoading = ref(false);
const balanceError = ref("");
const authStore = useAuthStore();
const isStaff = computed(() =>
  ["super_admin", "petugas"].includes(authStore.user?.role ?? ""),
);

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
      <article
        v-if="isStaff"
        class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
      >
        <p class="text-sm font-medium text-slate-500">Saldo Xendit</p>
        <h3 class="mt-4 text-4xl font-bold text-slate-900">
          <span v-if="balanceLoading">...</span>
          <span v-else-if="balanceError">-</span>
          <span v-else
            >Rp {{ Number(balance ?? 0).toLocaleString("id-ID") }}</span
          >
        </h3>
        <p v-if="balanceError" class="mt-2 text-xs text-rose-500">
          {{ balanceError }}
        </p>
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
