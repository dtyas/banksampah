<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

const rows = ref<Array<{ id: number; nama_kategori: string }>>([]);

async function loadKategori() {
  const response = await api.get("/kategori-sampah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadKategori);
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-4">ID</th>
          <th class="px-5 py-4">Nama Kategori</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in rows"
          :key="item.id"
          class="border-t border-slate-200"
        >
          <td class="px-5 py-4">{{ item.id }}</td>
          <td class="px-5 py-4">{{ item.nama_kategori }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
