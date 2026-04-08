<script setup lang="ts">
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import AppSidebar from "./components/AppSidebar.vue";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const pageMeta = computed<Record<string, { title: string; subtitle: string }>>(
  () => ({
    dashboard: {
      title: "Dashboard",
      subtitle: "Ringkasan operasional bank sampah hari ini.",
    },
    nasabah: {
      title: "Data Nasabah",
      subtitle: "Kelola data nasabah berbasis user role nasabah.",
    },
    "kategori-sampah": {
      title: "Kategori Sampah",
      subtitle: "Kelola jenis kategori sampah.",
    },
    sampah: {
      title: "Data Sampah",
      subtitle: "Kelola data sampah dan harga per kilogram.",
    },
    transaksi: {
      title: "Transaksi",
      subtitle: "Kelola transaksi setoran sampah nasabah.",
    },
    pembayaran: {
      title: "Pembayaran",
      subtitle: "Kelola pembayaran dan status verifikasi.",
    },
    "pencairan-saldo": {
      title: "Pencairan Saldo",
      subtitle: "Pantau pengajuan dan status pencairan saldo.",
    },
    user: {
      title: "User",
      subtitle: "Kelola user, role, menu access, dan operational access.",
    },
    laporan: {
      title: "Laporan",
      subtitle: "Laporan rekap transaksi, saldo, dan performa operasional.",
    },
  }),
);

const currentMeta = computed(() => {
  const key = String(route.name || "dashboard");
  return pageMeta.value[key] ?? pageMeta.value.dashboard;
});

async function logout(): Promise<void> {
  await authStore.signOut();
  await router.push({ name: "login" });
}
</script>

<template>
  <div class="flex min-h-screen flex-col lg:flex-row">
    <AppSidebar />

    <main class="flex min-h-screen flex-1 flex-col">
      <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div
          class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between"
        >
          <div>
            <h2 class="text-2xl font-bold text-slate-900">
              {{ currentMeta.title }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
              {{ currentMeta.subtitle }}
            </p>
          </div>

          <div
            class="flex items-center gap-3 self-start rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm"
          >
            <div class="flex items-center gap-3 rounded-2xl text-left">
              <div
                class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-emerald-100 text-lg font-bold text-emerald-700"
              >
                AD
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-900">
                  {{ authStore.user?.nama || "Admin" }}
                </p>
                <p class="text-xs text-slate-500">
                  {{ authStore.user?.role || "super_admin" }}
                </p>
              </div>
            </div>
            <button
              type="button"
              class="ml-2 rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100"
              @click="logout"
            >
              Logout
            </button>
          </div>
        </div>
      </header>

      <section class="flex-1 px-6 py-6">
        <router-view />
      </section>

      <footer class="border-t border-slate-200 bg-white px-6 py-5">
        <p class="text-sm font-medium text-slate-500">
          &copy; 2026 Sistem Informasi Bank Sampah Sedap Malam Berbasis Web
        </p>
      </footer>
    </main>
  </div>
</template>
