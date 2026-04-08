<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

const rows = ref<
  Array<{
    id: number;
    jumlah: number;
    metode: string;
    status: string;
    tanggal: string;
  }>
>([]);

async function loadData() {
  const response = await api.get("/pembayaran");
  const data = response.data?.data?.data ?? response.data?.data ?? [];
  rows.value = data.filter(
    (item: { metode?: string }) =>
      item.metode?.toLowerCase().includes("wallet") ||
      item.metode?.toLowerCase().includes("cash"),
  );
}

onMounted(loadData);
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-4">ID</th>
          <th class="px-5 py-4">Jumlah</th>
          <th class="px-5 py-4">Metode</th>
          <th class="px-5 py-4">Status</th>
          <th class="px-5 py-4">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in rows"
          :key="item.id"
          class="border-t border-slate-200"
        >
          <td class="px-5 py-4">{{ item.id }}</td>
          <td class="px-5 py-4">
            Rp {{ Number(item.jumlah || 0).toLocaleString("id-ID") }}
          </td>
          <td class="px-5 py-4">{{ item.metode }}</td>
          <td class="px-5 py-4">{{ item.status }}</td>
          <td class="px-5 py-4">{{ item.tanggal }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
