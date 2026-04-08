<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

type Transaksi = {
  id: number;
  tanggal: string;
  total_berat: number;
  total_harga: number;
  nasabah?: { nama?: string };
};

const rows = ref<Transaksi[]>([]);

async function loadTransaksi() {
  const response = await api.get("/transaksi");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadTransaksi);
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-4">Nasabah</th>
          <th class="px-5 py-4">Tanggal</th>
          <th class="px-5 py-4">Total Berat</th>
          <th class="px-5 py-4">Total Harga</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in rows"
          :key="item.id"
          class="border-t border-slate-200"
        >
          <td class="px-5 py-4">{{ item.nasabah?.nama || "-" }}</td>
          <td class="px-5 py-4">{{ item.tanggal }}</td>
          <td class="px-5 py-4">{{ item.total_berat }} Kg</td>
          <td class="px-5 py-4">
            Rp {{ Number(item.total_harga || 0).toLocaleString("id-ID") }}
          </td>
        </tr>
        <tr v-if="rows.length === 0" class="border-t border-slate-200">
          <td colspan="4" class="px-5 py-4">
            <div
              class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
              role="alert"
            >
              Belum ada data transaksi.
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
