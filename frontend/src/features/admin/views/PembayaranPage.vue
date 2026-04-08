<script setup lang="ts">
import { onMounted, ref } from "vue";
import api from "../../../api/http";

type Pembayaran = {
  id: number;
  transaksi_id: number;
  jumlah: number;
  metode: string;
  status: string;
  tanggal: string;
  verified_at?: string | null;
  verifier?: {
    id: number;
    nama: string;
    email: string;
  } | null;
};

const rows = ref<Pembayaran[]>([]);

function statusBadgeClass(status?: string): string {
  const value = (status ?? "").toLowerCase();
  if (value === "berhasil") {
    return "bg-emerald-100 text-emerald-700";
  }
  if (value === "ditolak") {
    return "bg-rose-100 text-rose-700";
  }
  if (value === "diproses" || value === "diverifikasi") {
    return "bg-amber-100 text-amber-700";
  }

  return "bg-slate-100 text-slate-700";
}

function formatDate(value?: string | null): string {
  if (!value) {
    return "-";
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleString("id-ID", {
    dateStyle: "medium",
    timeStyle: "short",
  });
}

async function loadPembayaran() {
  const response = await api.get("/pembayaran");
  rows.value = response.data?.data?.data ?? response.data?.data ?? [];
}

onMounted(loadPembayaran);
</script>

<template>
  <section
    class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
  >
    <table class="min-w-full text-left text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-5 py-4">Transaksi</th>
          <th class="px-5 py-4">Jumlah</th>
          <th class="px-5 py-4">Metode</th>
          <th class="px-5 py-4">Status</th>
          <th class="px-5 py-4">Verifier</th>
          <th class="px-5 py-4">Waktu Verifikasi</th>
          <th class="px-5 py-4">Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="item in rows"
          :key="item.id"
          class="border-t border-slate-200"
        >
          <td class="px-5 py-4">{{ item.transaksi_id }}</td>
          <td class="px-5 py-4">
            Rp {{ Number(item.jumlah || 0).toLocaleString("id-ID") }}
          </td>
          <td class="px-5 py-4">{{ item.metode }}</td>
          <td class="px-5 py-4">
            <span
              class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
              :class="statusBadgeClass(item.status)"
            >
              {{ item.status }}
            </span>
          </td>
          <td class="px-5 py-4">{{ item.verifier?.nama || "-" }}</td>
          <td class="px-5 py-4">{{ formatDate(item.verified_at) }}</td>
          <td class="px-5 py-4">{{ formatDate(item.tanggal) }}</td>
        </tr>
        <tr v-if="rows.length === 0" class="border-t border-slate-200">
          <td colspan="7" class="px-5 py-4">
            <div
              class="alert alert-info rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-center text-sm text-sky-700"
              role="alert"
            >
              Belum ada data pembayaran.
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
