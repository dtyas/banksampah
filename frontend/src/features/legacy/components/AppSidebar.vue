<script setup lang="ts">
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "../../../stores/auth";
import { canAccessMenu } from "../../auth/access-control";

const route = useRoute();
const authStore = useAuthStore();
const isOpen = ref(false);

function toggleSidebar() {
  isOpen.value = !isOpen.value;
}

// Tutup sidebar otomatis saat berpindah halaman di mobile
watch(route, () => {
  isOpen.value = false;
});

function isActive(name: string): boolean {
  return route.name === name;
}

function canMenu(menuLabel: string): boolean {
  return (
    Array.isArray(authStore.user?.menu_access) &&
    authStore.user.menu_access.includes(menuLabel)
  );
}

// Daftar menu beserta icon dan route
const menuList = [
  {
    label: "Dashboard",
    routeName: "dashboard",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l8.485-8.485a2 2 0 012.828 0L22 12M5 10.5V19a2 2 0 002 2h10a2 2 0 002-2v-8.5"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Nasabah",
    routeName: "nasabah",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-9a4 4 0 11-8 0 4 4 0 018 0z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Kategori Sampah",
    routeName: "kategori-sampah",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8h10M7 12h10M7 16h7M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Sampah",
    routeName: "sampah",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Transaksi",
    routeName: "transaksi",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m9-6a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Pembayaran",
    routeName: "pembayaran",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a5 5 0 00-10 0v2M6 9h12l-1 10H7L6 9zm6 3v4m-2-2h4\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Pencairan Saldo",
    routeName: "pencairan-saldo",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m8-6a8 8 0 11-16 0 8 8 0 0116 0z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "User",
    routeName: "user",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-9a4 4 0 11-8 0 4 4 0 018 0z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
  {
    label: "Laporan",
    routeName: "laporan",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m4 6V7m4 10v-3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\"/></svg>`,
    iconWrapperClass:
      "flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100",
    activeClass: "bg-sky-50 text-sky-700",
    inactiveClass: "text-slate-700 hover:bg-slate-100",
  },
];
</script>

<template>
  <!-- Hamburger Button (Mobile Only) -->
  <div
    class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4 lg:hidden"
  >
    <h1 class="text-xl font-bold tracking-wider text-emerald-500">
      Bank Sampah
    </h1>
    <button
      type="button"
      class="rounded-xl bg-slate-50 p-2 text-slate-600 ring-1 ring-slate-200"
      @click="toggleSidebar"
    >
      <svg
        v-if="!isOpen"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16m-7 6h7"
        />
      </svg>
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  </div>

  <!-- Mobile Backdrop -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
    @click="toggleSidebar"
  ></div>

  <!-- Sidebar Content -->
  <aside
    class="fixed inset-y-0 left-0 z-50 w-[280px] transform border-r border-slate-200 bg-white transition-transform duration-300 lg:static lg:w-[300px] lg:translate-x-0"
    :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <div class="px-6 py-7">
      <h1
        class="text-2xl font-bold uppercase tracking-[0.28em] text-emerald-500"
      >
        Bank Sampah
      </h1>
      <!-- tampilkan role user yang login -->
      <p class="mt-2 text-lg font-semibold text-slate-900">
        {{
          authStore.user?.role
            ? authStore.user.role
                .replace("_", " ")
                .replace(/\b\w/g, (c) => c.toUpperCase()) + " Dashboard"
            : "Dashboard"
        }}
      </p>
    </div>

    <nav class="px-5 pb-6">
      <ul class="space-y-2">
        <template v-for="menu in menuList" :key="menu.label">
          <li v-if="canMenu(menu.label)">
            <router-link
              :to="{ name: menu.routeName }"
              class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 transition"
              :class="
                isActive(menu.routeName) ? menu.activeClass : menu.inactiveClass
              "
            >
              <span :class="menu.iconWrapperClass" v-html="menu.icon"></span>
              <span class="text-[15px] font-medium">{{ menu.label }}</span>
            </router-link>
          </li>
        </template>
      </ul>
    </nav>
  </aside>
</template>
