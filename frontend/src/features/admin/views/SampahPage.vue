<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";

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
const canEditDelete = computed(() => {
  // Hanya super_admin dan petugas (admin) yang boleh edit/hapus
  return (
    authStore.user?.role === "super_admin" || authStore.user?.role === "petugas"
  );
});
const showForm = ref(false);
const searchTerm = ref("");
const saving = ref(false);
const editingId = ref<number | null>(null);
const form = ref({
  nama_sampah: "",
  harga_per_kg: "",
  kategori_sampah_id: "",
});

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  if (!keyword) {
    return rows.value;
  }

  return rows.value.filter((item) => {
    const values = [
      item.nama_sampah,
      item.kategori_sampah?.nama_kategori ?? "",
      String(item.harga_per_kg ?? ""),
    ]
      .join(" ")
      .toLowerCase();

    return values.includes(keyword);
  });
});

const hasFilters = computed(() => !!searchTerm.value);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

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

watch(searchTerm, () => {
  setPage(1);
});

function resetFilters() {
  searchTerm.value = "";
  setPage(1);
}

function startEdit(item: Sampah) {
  editingId.value = item.id;
  form.value = {
    nama_sampah: item.nama_sampah,
    harga_per_kg: String(item.harga_per_kg),
    kategori_sampah_id: item.kategori_sampah?.id
      ? String(item.kategori_sampah.id)
      : "",
  };
  showForm.value = true;
}

function resetForm() {
  editingId.value = null;
  form.value = { nama_sampah: "", harga_per_kg: "", kategori_sampah_id: "" };
  showForm.value = false;
}

async function submitForm() {
  if (!form.value.nama_sampah.trim()) {
    return;
  }

  saving.value = true;
  try {
    if (editingId.value) {
      await api.put(`/sampah/${editingId.value}`, {
        nama_sampah: form.value.nama_sampah.trim(),
        harga_per_kg: Number(form.value.harga_per_kg || 0),
        kategori_sampah_id: Number(form.value.kategori_sampah_id || 0),
      });
    } else {
      await api.post("/sampah", {
        nama_sampah: form.value.nama_sampah.trim(),
        harga_per_kg: Number(form.value.harga_per_kg || 0),
        kategori_sampah_id: Number(form.value.kategori_sampah_id || 0),
      });
    }
    resetForm();
    await loadSampah();
  } finally {
    saving.value = false;
  }
}

async function removeSampah(id: number, nama: string) {
  const confirmed = window.confirm(`Hapus data sampah "${nama}"?`);
  if (!confirmed) return;
  saving.value = true;
  try {
    await api.delete(`/sampah/${id}`);
    await loadSampah();
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <section class="space-y-4">
    <div
      class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
    >
      <div class="text-sm text-slate-500">
        Pastikan harga per kilogram selalu terbaru untuk kalkulasi transaksi.
      </div>
      <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_auto_auto]">
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama sampah atau kategori"
            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="canCreate"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup" : "Tambah Sampah" }}
          </button>
          <button
            type="button"
            class="rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
            :disabled="!hasFilters"
            @click="resetFilters"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <div
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <div class="bg-emerald-700 px-5 py-4">
        <h3 class="text-xl font-semibold text-white">Data Sampah</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Daftar jenis sampah dan harga per kilogram.
        </p>
      </div>
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Nama</th>
            <th class="px-5 py-4">Kategori</th>
            <th class="px-5 py-4">Harga/Kg</th>
            <th v-if="canEditDelete" class="px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in pagedRows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ (currentPage - 1) * 10 + index + 1 }}</td>
            <td class="px-5 py-4">{{ item.nama_sampah }}</td>
            <td class="px-5 py-4">
              {{ item.kategori_sampah?.nama_kategori || "-" }}
            </td>
            <td class="px-5 py-4">
              Rp {{ Number(item.harga_per_kg || 0).toLocaleString("id-ID") }}
            </td>
            <td v-if="canEditDelete" class="px-5 py-4">
              <div class="flex gap-2">
                <button
                  class="rounded-lg bg-amber-400 px-3 py-2 text-xs"
                  @click="startEdit(item)"
                >
                  Edit
                </button>
                <button
                  class="rounded-lg bg-rose-500 px-3 py-2 text-xs text-white"
                  @click="removeSampah(item.id, item.nama_sampah)"
                  :disabled="saving"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
          <tr
            v-if="filteredRows.length === 0"
            class="border-t border-slate-200"
          >
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
      <div
        class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4"
      >
        <p class="text-xs text-slate-500">
          Menampilkan {{ pagedRows.length }} dari {{ filteredRows.length }} data
          <span v-if="hasFilters" class="text-slate-400">
            (total {{ rows.length }})
          </span>
        </p>
        <div class="flex items-center gap-2 text-xs">
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="currentPage === 1"
            @click="setPage(currentPage - 1)"
          >
            Sebelumnya
          </button>
          <span class="text-slate-500">
            Halaman {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            class="rounded-lg border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-60"
            :disabled="currentPage === totalPages"
            @click="setPage(currentPage + 1)"
          >
            Berikutnya
          </button>
        </div>
      </div>
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
        <div class="flex items-end gap-2 lg:col-span-3">
          <button
            type="submit"
            class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
            :disabled="saving"
          >
            {{
              saving
                ? editingId
                  ? "Mengupdate..."
                  : "Menyimpan..."
                : editingId
                  ? "Update"
                  : "Simpan"
            }}
          </button>
          <button
            type="button"
            v-if="editingId !== null"
            class="rounded-2xl bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-700"
            @click="resetForm"
            :disabled="saving"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  </section>
</template>
