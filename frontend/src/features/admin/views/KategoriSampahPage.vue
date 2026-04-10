<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";

const rows = ref<Array<{ id: number; nama_kategori: string }>>([]);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const canUpdate = computed(() => canDoOperation(authStore.user, "update"));
const canDelete = computed(() => canDoOperation(authStore.user, "delete"));
const editingId = ref<number | null>(null);
const showForm = ref(false);
const searchTerm = ref("");
const saving = ref(false);
const deletingId = ref<number | null>(null);
const form = ref({
  nama_kategori: "",
});

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  if (!keyword) {
    return rows.value;
  }

  return rows.value.filter((item) => {
    const values = `${item.id} ${item.nama_kategori}`.toLowerCase();
    return values.includes(keyword);
  });
});

const hasFilters = computed(() => !!searchTerm.value);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

async function loadKategori() {
  const response = await api.get("/kategori-sampah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadKategori);

watch(searchTerm, () => {
  setPage(1);
});

function resetFilters() {
  searchTerm.value = "";
  setPage(1);
}

function startEdit(item: { id: number; nama_kategori: string }) {
  editingId.value = item.id;
  form.value.nama_kategori = item.nama_kategori;
  showForm.value = true;
}

function resetForm() {
  editingId.value = null;
  form.value.nama_kategori = "";
  showForm.value = false;
}

async function submitForm() {
  if (!form.value.nama_kategori.trim()) {
    return;
  }

  saving.value = true;
  try {
    const payload = { nama_kategori: form.value.nama_kategori.trim() };
    if (editingId.value) {
      if (!canUpdate.value) {
        return;
      }
      await api.put(`/kategori-sampah/${editingId.value}`, payload);
    } else {
      if (!canCreate.value) {
        return;
      }
      await api.post("/kategori-sampah", payload);
    }
    resetForm();
    await loadKategori();
  } finally {
    saving.value = false;
  }
}

async function removeKategori(item: { id: number; nama_kategori: string }) {
  if (!canDelete.value) {
    return;
  }

  const confirmed = window.confirm(`Hapus kategori "${item.nama_kategori}"?`);
  if (!confirmed) {
    return;
  }

  deletingId.value = item.id;
  try {
    await api.delete(`/kategori-sampah/${item.id}`);
    await loadKategori();
  } finally {
    deletingId.value = null;
  }
}
</script>

<template>
  <section class="space-y-4">
    <div
      class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
    >
      <div class="text-sm text-slate-500">
        Kelola data kategori sampah untuk kebutuhan harga dan transaksi.
      </div>
      <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_auto_auto]">
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama kategori"
            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="canCreate || canUpdate"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{
              showForm
                ? "Tutup"
                : editingId
                  ? "Edit Kategori"
                  : "Tambah Kategori"
            }}
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
        <h3 class="text-xl font-semibold text-white">Data Kategori Sampah</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Kelompok kategori untuk harga dan transaksi.
        </p>
      </div>
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">ID</th>
            <th class="px-5 py-4">Nama Kategori</th>
            <th class="px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in pagedRows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ item.id }}</td>
            <td class="px-5 py-4">{{ item.nama_kategori }}</td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <button
                  v-if="canUpdate"
                  type="button"
                  class="rounded-lg bg-amber-400 px-3 py-2 text-xs"
                  @click="startEdit(item)"
                >
                  Edit
                </button>
                <button
                  v-if="canDelete"
                  type="button"
                  class="rounded-lg bg-rose-500 px-3 py-2 text-xs text-white disabled:opacity-60"
                  :disabled="deletingId === item.id"
                  @click="removeKategori(item)"
                >
                  {{ deletingId === item.id ? "Menghapus..." : "Hapus" }}
                </button>
                <span
                  v-if="!canUpdate && !canDelete"
                  class="text-xs text-slate-400"
                >
                  Tidak ada akses aksi
                </span>
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
                Belum ada data kategori sampah.
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
          <div class="flex w-full items-center justify-end gap-2">
            <button
              type="button"
              class="rounded-2xl bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-700"
              @click="resetForm"
            >
              Batal
            </button>
            <button
              type="submit"
              class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
              :disabled="saving"
            >
              {{ saving ? "Menyimpan..." : editingId ? "Update" : "Simpan" }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </section>
</template>
