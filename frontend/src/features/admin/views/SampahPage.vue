<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

type Sampah = {
  id: number;
  nama_sampah: string;
  harga_per_kg: number;
  kategori_sampah?: { nama_kategori?: string };
};

const rows = ref<Sampah[]>([]);

async function loadSampah() {
  const response = await api.get("/sampah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadSampah);
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-4">Nama</th>
          <th class="px-5 py-4">Kategori</th>
          <th class="px-5 py-4">Harga/Kg</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in rows"
          :key="item.id"
          class="border-t border-slate-200"
        >
          <td class="px-5 py-4">{{ item.nama_sampah }}</td>
          <td class="px-5 py-4">
            {{ item.kategori_sampah?.nama_kategori || "-" }}
          </td>
          <td class="px-5 py-4">
            Rp {{ Number(item.harga_per_kg || 0).toLocaleString("id-ID") }}
          </td>
        </tr>
        <tr v-if="rows.length === 0" class="border-t border-slate-200">
          <td colspan="3" class="px-5 py-4">
            <div
              class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
              role="alert"
            >
              Belum ada data sampah.
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
