<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import api from "../../../api/http";

type Nasabah = {
  id: number;
  nama: string;
  alamat: string | null;
  no_hp: string | null;
  user?: { email?: string };
};

const rows = ref<Nasabah[]>([]);
const editingId = ref<number | null>(null);
const form = reactive({
  nama: "",
  alamat: "",
  no_hp: "",
  email: "",
  password: "",
});

async function loadNasabah() {
  const response = await api.get("/nasabah");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function startEdit(item: Nasabah) {
  editingId.value = item.id;
  form.nama = item.nama ?? "";
  form.alamat = item.alamat ?? "";
  form.no_hp = item.no_hp ?? "";
  form.email = item.user?.email ?? "";
  form.password = "";
}

function resetForm() {
  editingId.value = null;
  form.nama = "";
  form.alamat = "";
  form.no_hp = "";
  form.email = "";
  form.password = "";
}

async function save() {
  const payload: Record<string, string> = {
    nama: form.nama,
    alamat: form.alamat,
    no_hp: form.no_hp,
    email: form.email,
  };
  if (form.password) payload.password = form.password;

  if (editingId.value) {
    await api.put(`/nasabah/${editingId.value}`, payload);
  } else {
    await api.post("/nasabah", payload);
  }

  await loadNasabah();
  resetForm();
}

async function removeNasabah(id: number) {
  await api.delete(`/nasabah/${id}`);
  await loadNasabah();
}

onMounted(loadNasabah);
</script>

<template>
  <section class="space-y-6">
    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Form Nasabah</h3>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <input
          v-model="form.nama"
          placeholder="Nama"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <input
          v-model="form.email"
          placeholder="Email user nasabah"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <input
          v-model="form.no_hp"
          placeholder="No HP"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <input
          v-model="form.password"
          type="password"
          placeholder="Password (opsional saat edit)"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
      </div>
      <textarea
        v-model="form.alamat"
        rows="3"
        placeholder="Alamat"
        class="mt-4 w-full rounded-xl border border-slate-300 px-4 py-3"
      />
      <div class="mt-4 flex gap-3">
        <button
          class="rounded-xl bg-emerald-600 px-5 py-3 text-white"
          @click="save"
        >
          {{ editingId ? "Update" : "Tambah" }}
        </button>
        <button
          class="rounded-xl bg-slate-200 px-5 py-3 text-slate-700"
          @click="resetForm"
        >
          Reset
        </button>
      </div>
    </div>

    <div
      class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
    >
      <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-5 py-4">Nama</th>
            <th class="px-5 py-4">Email</th>
            <th class="px-5 py-4">No HP</th>
            <th class="px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in rows"
            :key="item.id"
            class="border-t border-slate-200"
          >
            <td class="px-5 py-4">{{ item.nama }}</td>
            <td class="px-5 py-4">{{ item.user?.email || "-" }}</td>
            <td class="px-5 py-4">{{ item.no_hp || "-" }}</td>
            <td class="px-5 py-4">
              <div class="flex gap-2">
                <button
                  class="rounded-lg bg-amber-400 px-3 py-2 text-xs"
                  @click="startEdit(item)"
                >
                  Edit
                </button>
                <button
                  class="rounded-lg bg-rose-500 px-3 py-2 text-xs text-white"
                  @click="removeNasabah(item.id)"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
