<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import api from "../../../api/http";

type UserRow = {
  id: number;
  nama: string;
  email: string;
  role: string;
  status: string;
  menu_access?: string[];
  operational_access?: string[];
};

const rows = ref<UserRow[]>([]);
const editingId = ref<number | null>(null);
const form = reactive({
  nama: "",
  email: "",
  password: "",
  role: "petugas",
  status: "Aktif",
  menu_access: "",
  operational_access: "",
});

async function loadUsers() {
  const response = await api.get("/users");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

function startEdit(user: UserRow) {
  editingId.value = user.id;
  form.nama = user.nama;
  form.email = user.email;
  form.password = "";
  form.role = user.role;
  form.status = user.status || "Aktif";
  form.menu_access = (user.menu_access || []).join(", ");
  form.operational_access = (user.operational_access || []).join(", ");
}

function resetForm() {
  editingId.value = null;
  form.nama = "";
  form.email = "";
  form.password = "";
  form.role = "petugas";
  form.status = "Aktif";
  form.menu_access = "";
  form.operational_access = "";
}

async function save() {
  const payload: Record<string, unknown> = {
    nama: form.nama,
    email: form.email,
    role: form.role,
    status: form.status,
    menu_access: form.menu_access
      .split(",")
      .map((value) => value.trim())
      .filter(Boolean),
    operational_access: form.operational_access
      .split(",")
      .map((value) => value.trim())
      .filter(Boolean),
  };

  if (form.password) payload.password = form.password;

  if (editingId.value) {
    await api.put(`/users/${editingId.value}`, payload);
  } else {
    if (!form.password) return;
    await api.post("/users", payload);
  }

  await loadUsers();
  resetForm();
}

async function removeUser(id: number) {
  await api.delete(`/users/${id}`);
  await loadUsers();
}

onMounted(loadUsers);
</script>

<template>
  <section class="space-y-6">
    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h3 class="text-lg font-semibold text-slate-900">Form User</h3>
      <div class="mt-4 grid gap-4 md:grid-cols-2">
        <input
          v-model="form.nama"
          placeholder="Nama"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <input
          v-model="form.email"
          placeholder="Email"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <input
          v-model="form.password"
          type="password"
          placeholder="Password"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
        <select
          v-model="form.role"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        >
          <option value="super_admin">Super Admin</option>
          <option value="petugas">Petugas</option>
          <option value="nasabah">Nasabah</option>
        </select>
        <select
          v-model="form.status"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        >
          <option value="Aktif">Aktif</option>
          <option value="Inactive">Inactive</option>
        </select>
        <input
          v-model="form.menu_access"
          placeholder="Menu access (pisah koma)"
          class="w-full rounded-xl border border-slate-300 px-4 py-3"
        />
      </div>
      <input
        v-model="form.operational_access"
        placeholder="Operational access (pisah koma)"
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
            <th class="px-5 py-4">Role</th>
            <th class="px-5 py-4">Status</th>
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
            <td class="px-5 py-4">{{ item.email }}</td>
            <td class="px-5 py-4">{{ item.role }}</td>
            <td class="px-5 py-4">{{ item.status }}</td>
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
                  @click="removeUser(item.id)"
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
