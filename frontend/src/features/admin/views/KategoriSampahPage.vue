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
        <tr v-if="rows.length === 0" class="border-t border-slate-200">
          <td colspan="2" class="px-5 py-4">
            <div
              class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
              role="alert"
            >
              Belum ada data kategori sampah.
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
