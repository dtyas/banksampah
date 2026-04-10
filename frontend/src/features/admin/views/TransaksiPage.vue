<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import api from "../../../api/http";
import { useAuthStore } from "../../../stores/auth";
import { canDoOperation } from "../../auth/access-control";
import { usePagination } from "../../../composables/usePagination";

type Transaksi = {
  id: number;
  tanggal: string;
  total_berat: number;
  total_harga: number;
  nasabah?: { nama?: string };
};

type Nasabah = {
  id: number;
  nama: string;
};

type SampahOption = {
  id: number;
  nama_sampah: string;
  harga_per_kg: number;
};

const rows = ref<Transaksi[]>([]);
const nasabahOptions = ref<Nasabah[]>([]);
const sampahOptions = ref<SampahOption[]>([]);
const authStore = useAuthStore();
const canCreate = computed(() => canDoOperation(authStore.user, "create"));
const showForm = ref(false);
const saving = ref(false);
const searchTerm = ref("");
const filterStart = ref("");
const filterEnd = ref("");
const form = ref({
  nasabah_id: "",
  tanggal: new Date().toISOString().slice(0, 10),
  items: [
    {
      sampah_id: "",
      berat: "",
      subtotal: 0,
    },
  ],
});

const totals = computed(() => {
  return form.value.items.reduce(
    (acc, item) => {
      acc.totalBerat += Number(item.berat || 0);
      acc.totalHarga += Number(item.subtotal || 0);
      return acc;
    },
    { totalBerat: 0, totalHarga: 0 },
  );
});

const filteredRows = computed(() => {
  const keyword = searchTerm.value.trim().toLowerCase();
  const start = filterStart.value;
  const end = filterEnd.value;

  return rows.value.filter((item) => {
    const haystack = [item.nasabah?.nama, item.tanggal]
      .filter(Boolean)
      .join(" ")
      .toLowerCase();
    if (keyword && !haystack.includes(keyword)) {
      return false;
    }

    if (!start && !end) {
      return true;
    }

    const parsed = new Date(item.tanggal);
    if (Number.isNaN(parsed.getTime())) {
      return false;
    }
    if (start) {
      const startDate = new Date(start);
      if (parsed < startDate) {
        return false;
      }
    }
    if (end) {
      const endDate = new Date(end);
      endDate.setHours(23, 59, 59, 999);
      if (parsed > endDate) {
        return false;
      }
    }

    return true;
  });
});

const hasFilters = computed(
  () => !!searchTerm.value || !!filterStart.value || !!filterEnd.value,
);

const { currentPage, totalPages, pagedRows, setPage } =
  usePagination(filteredRows);

async function loadTransaksi() {
  const response = await api.get("/transaksi");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

async function loadNasabah() {
  const response = await api.get("/nasabah");
  nasabahOptions.value = response.data?.data?.data ?? response.data?.data ?? [];
}

async function loadSampah() {
  const response = await api.get("/sampah");
  sampahOptions.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function addItem() {
  form.value.items.push({ sampah_id: "", berat: "", subtotal: 0 });
}

function removeItem(index: number) {
  if (form.value.items.length <= 1) {
    return;
  }
  form.value.items.splice(index, 1);
}

function updateItemSubtotal(index: number) {
  const item = form.value.items[index];
  const sampah = sampahOptions.value.find(
    (option) => option.id === Number(item.sampah_id),
  );
  const harga = sampah ? Number(sampah.harga_per_kg || 0) : 0;
  const berat = Number(item.berat || 0);
  item.subtotal = Number((harga * berat).toFixed(2));
}

function resetFilters() {
  searchTerm.value = "";
  filterStart.value = "";
  filterEnd.value = "";
  setPage(1);
}

onMounted(async () => {
  await Promise.all([loadTransaksi(), loadNasabah(), loadSampah()]);
});

watch([searchTerm, filterStart, filterEnd], () => {
  setPage(1);
});

async function submitForm() {
  const userId = authStore.user?.id;
  if (!userId) {
    return;
  }

  const items = form.value.items
    .filter((item) => item.sampah_id && Number(item.berat) > 0)
    .map((item) => ({
      sampah_id: Number(item.sampah_id),
      berat: Number(item.berat),
      subtotal: Number(item.subtotal || 0),
    }));

  if (!form.value.nasabah_id || items.length === 0) {
    return;
  }

  saving.value = true;
  try {
    await api.post("/transaksi", {
      user_id: userId,
      nasabah_id: Number(form.value.nasabah_id),
      tanggal: form.value.tanggal,
      items,
    });
    form.value = {
      nasabah_id: "",
      tanggal: new Date().toISOString().slice(0, 10),
      items: [{ sampah_id: "", berat: "", subtotal: 0 }],
    };
    showForm.value = false;
    await loadTransaksi();
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
        Input transaksi setoran sampah untuk nasabah.
      </div>
      <div
        class="grid gap-3 md:grid-cols-2 xl:grid-cols-[180px_180px_auto_auto]"
      >
        <label class="text-xs text-slate-600">
          Dari
          <input
            v-model="filterStart"
            type="date"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <label class="text-xs text-slate-600">
          Sampai
          <input
            v-model="filterEnd"
            type="date"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <label class="text-xs text-slate-600">
          Cari
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Nama nasabah"
            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
          />
        </label>
        <div class="flex items-end gap-2">
          <button
            v-if="canCreate"
            type="button"
            class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
            @click="showForm = !showForm"
          >
            {{ showForm ? "Tutup" : "Tambah Transaksi" }}
          </button>
          <button
            type="button"
            class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700"
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
        <h3 class="text-xl font-semibold text-white">Data Transaksi</h3>
        <p class="mt-1 text-sm text-emerald-100">
          Riwayat transaksi setoran sampah nasabah.
        </p>
      </div>
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
            v-for="item in pagedRows"
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
          <tr
            v-if="filteredRows.length === 0"
            class="border-t border-slate-200"
          >
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
      <form class="grid gap-4" @submit.prevent="submitForm">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
              Nasabah
            </label>
            <select
              v-model="form.nasabah_id"
              class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
              required
            >
              <option value="" disabled>Pilih nasabah</option>
              <option
                v-for="item in nasabahOptions"
                :key="item.id"
                :value="item.id"
              >
                {{ item.nama }}
              </option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
              Tanggal
            </label>
            <input
              v-model="form.tanggal"
              type="date"
              class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400"
              required
            />
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Item Sampah</h3>
            <button
              type="button"
              class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"
              @click="addItem"
            >
              Tambah Item
            </button>
          </div>
          <div
            v-for="(item, index) in form.items"
            :key="index"
            class="grid gap-3 rounded-2xl border border-emerald-100 bg-white p-4 md:grid-cols-[1.2fr_0.6fr_0.6fr_auto]"
          >
            <div>
              <label class="mb-1 block text-xs font-medium text-slate-600">
                Sampah
              </label>
              <select
                v-model="item.sampah_id"
                class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-400"
                @change="updateItemSubtotal(index)"
                required
              >
                <option value="" disabled>Pilih sampah</option>
                <option
                  v-for="option in sampahOptions"
                  :key="option.id"
                  :value="option.id"
                >
                  {{ option.nama_sampah }}
                </option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-slate-600">
                Berat (Kg)
              </label>
              <input
                v-model="item.berat"
                type="number"
                min="0"
                step="0.01"
                class="w-full rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-400"
                @input="updateItemSubtotal(index)"
                required
              />
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-slate-600">
                Subtotal
              </label>
              <input
                :value="item.subtotal"
                type="number"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500"
                readonly
              />
            </div>
            <div class="flex items-end">
              <button
                type="button"
                class="rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600"
                @click="removeItem(index)"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>

        <div
          class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 text-sm text-slate-600"
        >
          <div>Total Berat: {{ totals.totalBerat.toFixed(2) }} Kg</div>
          <div>
            Total Harga: Rp {{ totals.totalHarga.toLocaleString("id-ID") }}
          </div>
        </div>

        <button
          type="submit"
          class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
          :disabled="saving"
        >
          {{ saving ? "Menyimpan..." : "Simpan Transaksi" }}
        </button>
      </form>
    </div>
  </section>
</template>
