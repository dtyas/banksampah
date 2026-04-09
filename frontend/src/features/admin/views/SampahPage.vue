<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";

type Sampah = {
  id: number;
  nama_sampah: string;
  harga_per_kg: number;
  kategori_sampah?: { nama_kategori?: string };
};

type Kategori = {
  id: number;
  nama_kategori: string;
};

const rows = ref<Sampah[]>([]);
const kategoriRows = ref<Kategori[]>([]);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const showForm = ref(false);
const saving = ref(false);
const form = ref({
  nama_sampah: "",
  harga_per_kg: "",
  kategori_sampah_id: "",
});

async function loadSampah() {
  const response = await api.get("/sampah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

async function loadKategori() {
  const response = await api.get("/kategori-sampah");
  kategoriRows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(async () => {
  await Promise.all([loadSampah(), loadKategori()]);
});

async function submitForm() {
  if (!form.value.nama_sampah.trim()) {
    return;
  }

  saving.value = true;
  try {
    await api.post("/sampah", {
      nama_sampah: form.value.nama_sampah.trim(),
      harga_per_kg: Number(form.value.harga_per_kg || 0),
      kategori_sampah_id: Number(form.value.kategori_sampah_id || 0),
    });
    form.value = { nama_sampah: "", harga_per_kg: "", kategori_sampah_id: "" };
    showForm.value = false;
    await loadSampah();
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <section class="space-y-4">
    <div class="flex items-center justify-between">
      <div class="text-sm text-slate-500">
        Pastikan harga per kilogram selalu terbaru untuk kalkulasi transaksi.
      </div>
      <button
        v-if="canCreate"
        type="button"
        class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
        @click="showForm = !showForm"
      >
        {{ showForm ? "Tutup" : "Tambah Sampah" }}
      </button>
    </div>

    <div
      v-if="showForm"
      class="rounded-[24px] border border-emerald-100 bg-emerald-50/40 p-5"
    >
      <form class="grid gap-4 lg:grid-cols-3" @submit.prevent="submitForm">
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Nama Sampah
          </label>
          <input
            v-model="form.nama_sampah"
            type="text"
            placeholder="Contoh: Botol Plastik"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Kategori
          </label>
          <select
            v-model="form.kategori_sampah_id"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          >
            <option value="" disabled>Pilih kategori</option>
            <option
              v-for="item in kategoriRows"
              :key="item.id"
              :value="item.id"
            >
              {{ item.nama_kategori }}
            </option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Harga/Kg
          </label>
          <input
            v-model="form.harga_per_kg"
            type="number"
            min="0"
            step="0.01"
            placeholder="Contoh: 2500"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>
        <div class="flex items-end lg:col-span-3">
          <button
            type="submit"
            class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
            :disabled="saving"
          >
            {{ saving ? "Menyimpan..." : "Simpan" }}
          </button>
        </div>
      </form>
    </div>

    <div
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
    </div>
  </section>
</template>
