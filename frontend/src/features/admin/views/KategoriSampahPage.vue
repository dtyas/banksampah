<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";

const rows = ref<Array<{ id: number; nama_kategori: string }>>([]);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const showForm = ref(false);
const saving = ref(false);
const form = ref({
  nama_kategori: "",
});

async function loadKategori() {
  const response = await api.get("/kategori-sampah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadKategori);

async function submitForm() {
  if (!form.value.nama_kategori.trim()) {
    return;
  }

  saving.value = true;
  try {
    await api.post("/kategori-sampah", {
      nama_kategori: form.value.nama_kategori.trim(),
    });
    form.value.nama_kategori = "";
    showForm.value = false;
    await loadKategori();
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <section class="space-y-4">
    <div class="flex items-center justify-between">
      <div class="text-sm text-slate-500">
        Kelola data kategori sampah untuk kebutuhan harga dan transaksi.
      </div>
      <button
        v-if="canCreate"
        type="button"
        class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
        @click="showForm = !showForm"
      >
        {{ showForm ? "Tutup" : "Tambah Kategori" }}
      </button>
    </div>

    <div
      v-if="showForm"
      class="rounded-[24px] border border-emerald-100 bg-emerald-50/40 p-5"
    >
      <form
        class="grid gap-4 md:grid-cols-[1.2fr_auto]"
        @submit.prevent="submitForm"
      >
        <div>
          <label class="mb-2 block text-sm font-medium text-slate-700">
            Nama Kategori
          </label>
          <input
            v-model="form.nama_kategori"
            type="text"
            placeholder="Contoh: Plastik"
            class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
            required
          />
        </div>
        <div class="flex items-end">
          <button
            type="submit"
            class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
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
    </div>
  </section>
</template>
