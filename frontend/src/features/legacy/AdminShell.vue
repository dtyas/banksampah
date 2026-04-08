<script setup lang="ts">
import { onMounted } from "vue";
import AppSidebar from "./components/AppSidebar.vue";

onMounted(() => {
  import("../../../assets/js/app.js");
});
</script>

<template>
  <div id="appShell" class="hidden flex min-h-screen flex-col lg:flex-row">
    <AppSidebar />

    <main class="flex min-h-screen flex-1 flex-col">
      <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div
          class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between"
        >
          <div>
            <h2 id="pageTitle" class="text-2xl font-bold text-slate-900">
              Dashboard
            </h2>
            <p id="pageSubtitle" class="mt-1 text-sm text-slate-500">
              Ringkasan operasional bank sampah hari ini.
            </p>
          </div>

          <div
            class="flex items-center gap-3 self-start rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm"
          >
            <button
              id="openProfilePanel"
              type="button"
              class="flex items-center gap-3 rounded-2xl text-left transition hover:opacity-90"
            >
              <div
                id="profileAvatar"
                class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-emerald-100 text-lg font-bold text-emerald-700"
              >
                AD
              </div>
              <div>
                <p
                  id="profileName"
                  class="text-sm font-semibold text-slate-900"
                >
                  Admin Utama
                </p>
                <p id="profileRole" class="text-xs text-slate-500">
                  Super Admin
                </p>
              </div>
            </button>
            <button
              id="logoutButton"
              type="button"
              class="ml-2 rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100"
            >
              Logout
            </button>
          </div>
        </div>
      </header>

      <section class="flex-1 px-6 py-6">
        <div id="dashboardPage" class="page-section">
          <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500">
                    Total Nasabah
                  </p>
                  <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                  <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
                </div>
                <span
                  class="flex h-14 w-14 items-center justify-center rounded-3xl bg-sky-100 text-sky-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-9a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                </span>
              </div>
            </article>
            <article
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500">
                    Total Sampah Masuk
                  </p>
                  <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                  <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
                </div>
                <span
                  class="flex h-14 w-14 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M7 8h10M7 12h6m-6 4h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                    />
                  </svg>
                </span>
              </div>
            </article>
            <article
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500">Total Saldo</p>
                  <h3
                    class="mt-4 text-4xl font-bold leading-tight text-slate-900"
                  >
                    -
                  </h3>
                  <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
                </div>
                <span
                  class="flex h-14 w-14 items-center justify-center rounded-3xl bg-amber-100 text-amber-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m9-6a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </span>
              </div>
            </article>
            <article
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500">
                    Total Transaksi
                  </p>
                  <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                  <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
                </div>
                <span
                  class="flex h-14 w-14 items-center justify-center rounded-3xl bg-rose-100 text-rose-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M9 17v-6m4 6V7m4 10v-3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                </span>
              </div>
            </article>
          </div>

          <div
            class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]"
          >
            <section
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div
                class="flex flex-col gap-4 border-b border-slate-100 pb-5 md:flex-row md:items-center md:justify-between"
              >
                <div>
                  <h3 class="text-lg font-semibold text-slate-900">
                    Grafik Transaksi Sampah
                  </h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Pantau tren transaksi setoran sampah per bulan.
                  </p>
                </div>
                <div
                  class="grid gap-3 md:grid-cols-2 xl:grid-cols-[180px_180px_auto_auto]"
                >
                  <label class="flex flex-col gap-1 text-sm text-slate-500">
                    Dari Tanggal
                    <input
                      type="date"
                      class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-base font-medium text-slate-700 outline-none transition focus:border-sky-400 focus:bg-white"
                    />
                  </label>
                  <label class="flex flex-col gap-1 text-sm text-slate-500">
                    Sampai Tanggal
                    <input
                      type="date"
                      class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-base font-medium text-slate-700 outline-none transition focus:border-sky-400 focus:bg-white"
                    />
                  </label>
                  <button
                    type="button"
                    class="rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 md:self-end"
                  >
                    Filter
                  </button>
                  <button
                    type="button"
                    class="rounded-2xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300 md:self-end"
                  >
                    Reset
                  </button>
                </div>
              </div>
              <div class="mt-6 h-[340px]">
                <canvas id="transactionChart"></canvas>
              </div>
            </section>

            <section
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
            >
              <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-semibold text-slate-900">
                  Ringkasan Aktivitas
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                  Highlight performa bank sampah bulan ini.
                </p>
              </div>
              <div class="mt-5 space-y-4">
                <div class="rounded-[24px] bg-sky-50 p-5">
                  <p class="text-sm font-semibold text-sky-700">
                    Setoran Tertinggi
                  </p>
                  <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                  <p class="mt-2 text-sm text-slate-600">Data belum tersedia</p>
                </div>
                <div class="rounded-[24px] bg-emerald-50 p-5">
                  <p class="text-sm font-semibold text-emerald-700">
                    Nasabah Aktif
                  </p>
                  <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                  <p class="mt-2 text-sm text-slate-600">Data belum tersedia</p>
                </div>
                <div class="rounded-[24px] bg-amber-50 p-5">
                  <p class="text-sm font-semibold text-amber-700">
                    Pencairan Saldo
                  </p>
                  <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                  <p class="mt-2 text-sm text-slate-600">Data belum tersedia</p>
                </div>
              </div>
            </section>
          </div>
        </div>

        <div id="nasabahPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div class="mb-6">
              <button
                id="openNasabahForm"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                + Tambah Nasabah
              </button>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
              <div class="bg-emerald-700 px-5 py-4">
                <h3 class="text-xl font-semibold text-white">
                  List Data Nasabah
                </h3>
              </div>
              <div class="overflow-x-auto p-5">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nama
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Alamat
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No HP
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Tanggal
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody id="nasabahTableBody"></tbody>
                </table>
              </div>
            </div>
          </section>
        </div>

        <div id="nasabahFormPage" class="page-section hidden">
          <section
            class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
          >
            <div class="bg-emerald-700 px-5 py-4">
              <h3 class="text-xl font-semibold text-white">
                Form Input Nasabah
              </h3>
            </div>
            <form class="space-y-6 p-5">
              <div>
                <label
                  for="idNasabah"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Id Nasabah</label
                >
                <input
                  id="idNasabah"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
              <div>
                <label
                  for="namaNasabah"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Nama Nasabah</label
                >
                <input
                  id="namaNasabah"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
              <div>
                <label
                  for="alamatNasabah"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Alamat</label
                >
                <textarea
                  id="alamatNasabah"
                  rows="3"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                ></textarea>
              </div>
              <div>
                <label
                  for="noHpNasabah"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >No HP</label
                >
                <input
                  id="noHpNasabah"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
              <div>
                <label
                  for="tanggalDaftar"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Tanggal Daftar</label
                >
                <input
                  id="tanggalDaftar"
                  type="date"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
            </form>
            <div class="flex gap-3 border-t border-slate-200 px-5 py-4">
              <button
                type="button"
                id="saveNasabah"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                Simpan
              </button>
              <button
                type="button"
                id="backToNasabahList"
                class="rounded-xl bg-slate-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-slate-600"
              >
                Kembali
              </button>
            </div>
          </section>
        </div>

        <div id="jenisSampahPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div class="mb-6">
              <button
                id="openJenisSampahForm"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                + Tambah Kategori Sampah
              </button>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
              <div class="bg-emerald-700 px-5 py-4">
                <h3 class="text-xl font-semibold text-white">
                  Data Kategori Sampah
                </h3>
              </div>
              <div class="overflow-x-auto p-5">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        ID Jenis
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nama Jenis
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody id="kategoriTableBody"></tbody>
                </table>
              </div>
            </div>
          </section>
        </div>

        <div id="jenisSampahFormPage" class="page-section hidden">
          <section
            class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
          >
            <div class="bg-emerald-700 px-5 py-4">
              <h3 class="text-xl font-semibold text-white">
                Form Input Kategori Sampah
              </h3>
            </div>
            <div class="p-5">
              <div class="overflow-hidden rounded-2xl border border-slate-200">
                <div class="border-b border-slate-200 px-5 py-4">
                  <h4 class="text-xl font-medium text-slate-900">
                    Tambah Kategori Sampah
                  </h4>
                </div>
                <form class="grid gap-6 p-5 md:grid-cols-2">
                  <div>
                    <label
                      for="idJenis"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >ID Jenis</label
                    >
                    <input
                      id="idJenis"
                      type="text"
                      placeholder="Masukkan ID Jenis"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                  <div>
                    <label
                      for="namaJenis"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >Kategori Sampah</label
                    >
                    <input
                      id="namaJenis"
                      type="text"
                      placeholder="Masukkan Nama Kategori"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                </form>
                <div class="flex gap-3 px-5 pb-5">
                  <button
                    type="button"
                    id="saveJenisSampah"
                    class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
                  >
                    Simpan
                  </button>
                  <button
                    type="button"
                    id="backToJenisSampahList"
                    class="rounded-xl bg-slate-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-slate-600"
                  >
                    Kembali
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div id="sampahPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div class="mb-6">
              <button
                id="openSampahForm"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                + Tambah Data Sampah
              </button>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
              <div class="bg-emerald-700 px-5 py-4">
                <h3 class="text-xl font-semibold text-white">Data Sampah</h3>
              </div>
              <div class="overflow-x-auto p-5">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        ID Sampah
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nama Sampah
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Harga per Kg
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Kategori Sampah
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody id="sampahTableBody"></tbody>
                </table>
              </div>
            </div>
          </section>
        </div>

        <div id="sampahFormPage" class="page-section hidden">
          <section
            class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
          >
            <div class="bg-emerald-700 px-5 py-4">
              <h3 class="text-xl font-semibold text-white">
                Form Input Data Sampah
              </h3>
            </div>
            <div class="p-5">
              <div class="overflow-hidden rounded-2xl border border-slate-200">
                <div class="border-b border-slate-200 px-5 py-4">
                  <h4 class="text-xl font-medium text-slate-900">
                    Tambah Data Sampah
                  </h4>
                </div>
                <form class="grid gap-6 p-5 md:grid-cols-2">
                  <div>
                    <label
                      for="idSampah"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >ID Sampah</label
                    >
                    <input
                      id="idSampah"
                      type="text"
                      placeholder="Masukkan ID Sampah"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                  <div>
                    <label
                      for="namaSampah"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >Nama Sampah</label
                    >
                    <input
                      id="namaSampah"
                      type="text"
                      placeholder="Masukkan Nama Sampah"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                  <div>
                    <label
                      for="hargaPerKg"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >Harga per Kg</label
                    >
                    <input
                      id="hargaPerKg"
                      type="text"
                      placeholder="Masukkan Harga"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                  <div>
                    <label
                      for="jenisSampahSelect"
                      class="mb-3 block text-sm font-medium text-slate-800"
                      >Kategori Sampah</label
                    >
                    <select
                      id="jenisSampahSelect"
                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                    >
                      <option value="">-- Pilih Jenis --</option>
                    </select>
                  </div>
                </form>
                <div class="flex gap-3 px-5 pb-5">
                  <button
                    type="button"
                    id="saveSampah"
                    class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
                  >
                    Simpan
                  </button>
                  <button
                    type="button"
                    id="backToSampah"
                    class="rounded-xl bg-slate-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-slate-600"
                  >
                    Kembali
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div id="transaksiPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div
              class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
            >
              <button
                id="openTransaksiForm"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                + Tambah Data Transaksi
              </button>
              <div
                class="grid gap-3 md:grid-cols-2 xl:grid-cols-[180px_180px_auto_auto]"
              >
                <div>
                  <label
                    for="filterTanggalMulai"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Dari Tanggal</label
                  >
                  <input
                    id="filterTanggalMulai"
                    type="date"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-4 outline-none transition focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label
                    for="filterTanggalSelesai"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Sampai Tanggal</label
                  >
                  <input
                    id="filterTanggalSelesai"
                    type="date"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-4 outline-none transition focus:border-emerald-500"
                  />
                </div>
                <button
                  id="applyTransactionFilter"
                  type="button"
                  class="mt-7 h-14 rounded-xl bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700"
                >
                  Filter
                </button>
                <button
                  id="resetTransactionFilter"
                  type="button"
                  class="mt-7 h-14 rounded-xl bg-slate-200 px-6 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
              <div class="bg-emerald-700 px-5 py-4">
                <h3 class="text-xl font-semibold text-white">Data Transaksi</h3>
              </div>
              <div class="overflow-x-auto p-5">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nama Nasabah
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Tanggal
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Total Berat
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Total Harga
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Jumlah Item
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Detail
                      </th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
                <p
                  id="transactionEmptyState"
                  class="hidden pt-4 text-sm text-slate-500"
                >
                  Tidak ada transaksi pada tanggal yang dipilih.
                </p>
              </div>
            </div>
          </section>
        </div>

        <div id="transaksiFormPage" class="page-section hidden">
          <section
            class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
          >
            <div class="bg-emerald-700 px-5 py-4">
              <h3 class="text-xl font-semibold text-white">Form Transaksi</h3>
            </div>
            <form class="space-y-6 p-5">
              <div>
                <label
                  for="searchNasabahTransaksi"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Nama Nasabah</label
                >
                <div class="relative">
                  <input
                    id="searchNasabahTransaksi"
                    type="text"
                    placeholder="Cari nasabah"
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                  <div
                    id="nasabahTransaksiResults"
                    class="absolute left-0 right-0 top-[calc(100%+8px)] z-20 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"
                  ></div>
                </div>
              </div>
              <div class="rounded-2xl border border-slate-200">
                <div
                  class="flex items-center justify-between border-b border-slate-200 px-4 py-4"
                >
                  <div>
                    <h4 class="text-base font-semibold text-slate-900">
                      Item Sampah
                    </h4>
                    <p class="mt-1 text-sm text-slate-500">
                      Tambahkan lebih dari satu item sampah dalam satu
                      transaksi.
                    </p>
                  </div>
                  <button
                    type="button"
                    id="addTransactionItem"
                    class="rounded-xl bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-200"
                  >
                    + Tambah Item Sampah
                  </button>
                </div>
                <div id="transactionItems" class="space-y-4 p-4">
                  <div
                    class="transaction-item grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end"
                  >
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-800"
                        >Sampah</label
                      >
                      <div class="relative transaction-sampah-search">
                        <input
                          type="text"
                          placeholder="Cari sampah"
                          autocomplete="off"
                          class="transaction-sampah-input w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                        />
                        <div
                          class="transaction-sampah-results absolute left-0 right-0 top-[calc(100%+8px)] z-20 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"
                        ></div>
                      </div>
                    </div>
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-800"
                        >Harga per Kg</label
                      >
                      <input
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                      />
                    </div>
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-800"
                        >Berat (Kg)</label
                      >
                      <input
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                      />
                    </div>
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-800"
                        >Subtotal</label
                      >
                      <input
                        type="text"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                      />
                    </div>
                    <div class="md:pb-[2px]">
                      <button
                        type="button"
                        class="remove-transaction-item rounded-xl bg-rose-100 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-200"
                      >
                        Hapus
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label
                    for="totalBeratTransaksi"
                    class="mb-3 block text-sm font-medium text-slate-800"
                    >Total Berat</label
                  >
                  <input
                    id="totalBeratTransaksi"
                    type="text"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label
                    for="totalHargaTransaksi"
                    class="mb-3 block text-sm font-medium text-slate-800"
                    >Total Harga</label
                  >
                  <input
                    id="totalHargaTransaksi"
                    type="text"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                </div>
              </div>
              <div>
                <label
                  for="tanggalTransaksi"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Tanggal</label
                >
                <input
                  id="tanggalTransaksi"
                  type="date"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
            </form>
            <div class="flex gap-3 border-t border-slate-200 px-5 py-4">
              <button
                type="button"
                id="saveTransaksi"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                Simpan
              </button>
              <button
                type="button"
                id="backToTransaksiList"
                class="rounded-xl bg-slate-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-slate-600"
              >
                Kembali
              </button>
            </div>
          </section>
        </div>

        <div id="pembayaranPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div
              class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between"
            >
              <button
                id="openPembayaranForm"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                + Tambah Pembayaran
              </button>
              <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div>
                  <label
                    for="filterPaymentId"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >ID Transaksi</label
                  >
                  <input
                    id="filterPaymentId"
                    type="text"
                    placeholder="Cari ID"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label
                    for="filterPaymentNasabah"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Nasabah</label
                  >
                  <input
                    id="filterPaymentNasabah"
                    type="text"
                    placeholder="Cari nasabah"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                </div>
                <div>
                  <label
                    for="filterPaymentMethod"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Metode</label
                  >
                  <select
                    id="filterPaymentMethod"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  >
                    <option value="">Semua</option>
                    <option value="Cash">Cash</option>
                    <option value="E-Wallet">E-Wallet</option>
                  </select>
                </div>
                <div>
                  <label
                    for="filterPaymentStatus"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Status</label
                  >
                  <select
                    id="filterPaymentStatus"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  >
                    <option value="">Semua</option>
                    <option value="Berhasil">Berhasil</option>
                    <option value="Diverifikasi">Diverifikasi</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Ditolak">Ditolak</option>
                  </select>
                </div>
                <div>
                  <label
                    for="filterPaymentDate"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Tanggal</label
                  >
                  <input
                    id="filterPaymentDate"
                    type="date"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                </div>
              </div>
            </div>
            <div class="mb-6 flex flex-wrap gap-3">
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700"
                data-payment-tab="all"
              >
                Semua
              </button>
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                data-payment-tab="Menunggu"
              >
                Menunggu Verifikasi
              </button>
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                data-payment-tab="Diverifikasi"
              >
                Diverifikasi
              </button>
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                data-payment-tab="Diproses"
              >
                Diproses
              </button>
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                data-payment-tab="Berhasil"
              >
                Berhasil
              </button>
              <button
                type="button"
                class="payment-status-tab rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                data-payment-tab="Ditolak"
              >
                Ditolak
              </button>
            </div>
            <div class="overflow-hidden rounded-2xl border border-slate-200">
              <div class="bg-emerald-700 px-5 py-4">
                <h3 class="text-xl font-semibold text-white">
                  Data Pengajuan Pencairan
                </h3>
              </div>
              <div class="overflow-x-auto p-5">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        No
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        ID Transaksi
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nasabah
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Jumlah Bayar
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Metode
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Tanggal
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Aksi Verifikasi
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody id="paymentTableBody"></tbody>
                </table>
                <p
                  id="paymentEmptyState"
                  class="hidden pt-4 text-sm text-slate-500"
                >
                  Tidak ada data pembayaran yang cocok dengan filter.
                </p>
              </div>
            </div>
          </section>
        </div>

        <div id="pembayaranFormPage" class="page-section hidden">
          <section
            class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200"
          >
            <div class="bg-emerald-700 px-5 py-4">
              <h3 class="text-xl font-semibold text-white">Form Pembayaran</h3>
            </div>
            <form class="space-y-6 p-5">
              <div>
                <label
                  for="searchNasabahPembayaran"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Nama Nasabah</label
                >
                <div class="relative">
                  <input
                    id="searchNasabahPembayaran"
                    type="text"
                    placeholder="Cari nasabah"
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  />
                  <div
                    id="nasabahSearchResults"
                    class="absolute left-0 right-0 top-[calc(100%+8px)] z-20 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"
                  ></div>
                </div>
              </div>
              <div>
                <label
                  for="idTransaksiPembayaran"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >ID Transaksi</label
                >
                <select
                  id="idTransaksiPembayaran"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                >
                  <option value="">Cari Transaksi</option>
                </select>
              </div>
              <div>
                <label
                  for="jumlahBayar"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Jumlah Bayar</label
                >
                <input
                  id="jumlahBayar"
                  type="text"
                  placeholder="Masukkan nominal pencairan"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label
                    for="metodePembayaran"
                    class="mb-3 block text-sm font-medium text-slate-800"
                    >Metode Pembayaran</label
                  >
                  <select
                    id="metodePembayaran"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  >
                    <option>Cash</option>
                    <option>E-Wallet</option>
                    <option>Transfer Bank</option>
                  </select>
                </div>
                <div>
                  <label
                    for="statusPembayaran"
                    class="mb-3 block text-sm font-medium text-slate-800"
                    >Status</label
                  >
                  <select
                    id="statusPembayaran"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                  >
                    <option>Menunggu</option>
                    <option>Diproses</option>
                    <option>Lunas</option>
                    <option>Gagal</option>
                  </select>
                </div>
              </div>
              <div
                id="ewalletFields"
                class="hidden rounded-2xl border border-sky-200 bg-sky-50 p-4"
              >
                <h4 class="text-base font-semibold text-sky-900">
                  Detail Pencairan E-Wallet
                </h4>
                <p class="mt-1 text-sm text-sky-700">
                  Lengkapi detail tujuan pencairan e-wallet.
                </p>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                  <div>
                    <label
                      for="ewalletChannel"
                      class="mb-2 block text-sm font-medium text-slate-800"
                      >Channel E-Wallet</label
                    >
                    <select
                      id="ewalletChannel"
                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-emerald-500"
                    >
                      <option>DANA</option>
                      <option>OVO</option>
                      <option>LinkAja</option>
                      <option>ShopeePay</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="ewalletNumber"
                      class="mb-2 block text-sm font-medium text-slate-800"
                      >Nomor E-Wallet</label
                    >
                    <input
                      id="ewalletNumber"
                      type="text"
                      placeholder="Masukkan nomor e-wallet"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                    />
                  </div>
                </div>
              </div>
              <div>
                <label
                  for="tanggalPembayaran"
                  class="mb-3 block text-sm font-medium text-slate-800"
                  >Tanggal</label
                >
                <input
                  id="tanggalPembayaran"
                  type="date"
                  class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-500"
                />
              </div>
            </form>
            <div class="flex gap-3 border-t border-slate-200 px-5 py-4">
              <button
                type="button"
                id="savePembayaran"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-emerald-700"
              >
                Simpan
              </button>
              <button
                type="button"
                id="backToPembayaran"
                class="rounded-xl bg-slate-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-slate-600"
              >
                Kembali
              </button>
            </div>
          </section>
        </div>

        <div id="laporanPage" class="page-section hidden">
          <div class="space-y-6">
            <section
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
              <div
                class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between"
              >
                <div>
                  <h3 class="text-xl font-semibold text-slate-900">
                    Filter Laporan
                  </h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Pilih periode dan kategori untuk menampilkan laporan yang
                    dibutuhkan.
                  </p>
                </div>
                <div class="flex flex-wrap gap-3">
                  <button
                    class="rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-100"
                  >
                    Export PDF
                  </button>
                  <button
                    class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-100"
                  >
                    Export Excel
                  </button>
                  <button
                    class="rounded-xl bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-600 transition hover:bg-sky-100"
                  >
                    Print
                  </button>
                </div>
              </div>
              <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Tanggal Mulai</label
                  >
                  <input
                    type="date"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Tanggal Selesai</label
                  >
                  <input
                    type="date"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Jenis Laporan</label
                  >
                  <select
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  >
                    <option>Semua Laporan</option>
                    <option>Laporan Transaksi</option>
                    <option>Laporan Pembayaran</option>
                    <option>Laporan Nasabah</option>
                  </select>
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Nasabah</label
                  >
                  <select
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  >
                    <option>Semua Nasabah</option>
                  </select>
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Jenis Sampah</label
                  >
                  <select
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  >
                    <option>Semua Jenis</option>
                  </select>
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Status Pembayaran</label
                  >
                  <select
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                  >
                    <option>Semua Status</option>
                  </select>
                </div>
              </div>
              <div class="mt-5 flex gap-3">
                <button
                  class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
                >
                  Tampilkan
                </button>
                <button
                  class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                >
                  Reset
                </button>
              </div>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <p class="text-sm font-medium text-slate-500">
                  Total Transaksi
                </p>
                <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <p class="text-sm font-medium text-slate-500">
                  Total Sampah Masuk
                </p>
                <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <p class="text-sm font-medium text-slate-500">
                  Total Saldo Masuk
                </p>
                <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <p class="text-sm font-medium text-slate-500">
                  Total Pencairan
                </p>
                <h3 class="mt-4 text-4xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
            </section>

            <section
              class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,1fr)]"
            >
              <div
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <div
                  class="flex flex-wrap gap-3 border-b border-slate-100 pb-4"
                >
                  <button
                    class="rounded-xl bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700"
                  >
                    Laporan Transaksi
                  </button>
                  <button
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                  >
                    Laporan Pembayaran
                  </button>
                  <button
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                  >
                    Laporan Nasabah
                  </button>
                  <button
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                  >
                    Jenis Sampah
                  </button>
                </div>
                <div class="mt-5 overflow-x-auto">
                  <table
                    class="min-w-full border border-slate-200 text-left text-sm"
                  >
                    <thead class="bg-white">
                      <tr class="text-slate-900">
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Tanggal
                        </th>
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Nama Nasabah
                        </th>
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Jenis Sampah
                        </th>
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Berat
                        </th>
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Subtotal
                        </th>
                        <th
                          class="border border-slate-200 px-4 py-3 font-semibold"
                        >
                          Status
                        </th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
              <div
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <div class="border-b border-slate-100 pb-4">
                  <h3 class="text-lg font-semibold text-slate-900">
                    Insight Laporan
                  </h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Ringkasan tren periode terpilih.
                  </p>
                </div>
                <div class="mt-5 space-y-4">
                  <div class="rounded-[24px] bg-sky-50 p-5">
                    <p class="text-sm font-semibold text-sky-700">
                      Transaksi Terbanyak
                    </p>
                    <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                    <p class="mt-2 text-sm text-slate-600">
                      Data belum tersedia
                    </p>
                  </div>
                  <div class="rounded-[24px] bg-emerald-50 p-5">
                    <p class="text-sm font-semibold text-emerald-700">
                      Jenis Sampah Teratas
                    </p>
                    <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                    <p class="mt-2 text-sm text-slate-600">
                      Data belum tersedia
                    </p>
                  </div>
                  <div class="rounded-[24px] bg-amber-50 p-5">
                    <p class="text-sm font-semibold text-amber-700">
                      Pencairan E-Wallet
                    </p>
                    <h4 class="mt-3 text-2xl font-bold text-slate-900">-</h4>
                    <p class="mt-2 text-sm text-slate-600">
                      Data belum tersedia
                    </p>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>

        <div id="userPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div
              class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between"
            >
              <div>
                <h3 class="text-xl font-semibold text-slate-900">
                  Manajemen User
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                  Super admin dapat membuat akses untuk petugas bank sampah dan
                  nasabah.
                </p>
              </div>
              <button
                id="openUserForm"
                type="button"
                class="rounded-xl bg-indigo-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600"
              >
                Add new user
              </button>
            </div>
            <div
              class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_220px_220px]"
            >
              <div>
                <label
                  for="filterUserName"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Name</label
                >
                <input
                  id="filterUserName"
                  type="text"
                  placeholder="Cari nama user"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                />
              </div>
              <div>
                <label
                  for="filterUserEmail"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Email</label
                >
                <input
                  id="filterUserEmail"
                  type="text"
                  placeholder="Cari email user"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                />
              </div>
              <div>
                <label
                  for="filterUserRole"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Role</label
                >
                <select
                  id="filterUserRole"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                >
                  <option value="">Semua Role</option>
                  <option value="Super Admin">Super Admin</option>
                  <option value="Petugas">Petugas</option>
                  <option value="Nasabah">Nasabah</option>
                </select>
              </div>
              <div>
                <label
                  for="filterUserStatus"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Status</label
                >
                <select
                  id="filterUserStatus"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
                >
                  <option value="">Semua Status</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="mt-4 flex gap-3">
              <button
                id="searchUserButton"
                type="button"
                class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
              >
                Cari
              </button>
              <button
                id="clearUserFilter"
                type="button"
                class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
              >
                Reset
              </button>
            </div>
            <div
              class="mt-6 overflow-hidden rounded-2xl border border-slate-200"
            >
              <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50">
                  <tr class="text-slate-600">
                    <th class="px-5 py-4 font-semibold">No</th>
                    <th class="px-5 py-4 font-semibold">Nama</th>
                    <th class="px-5 py-4 font-semibold">Email</th>
                    <th class="px-5 py-4 font-semibold">Role</th>
                    <th class="px-5 py-4 font-semibold">Status</th>
                    <th class="px-5 py-4 font-semibold">Created At</th>
                    <th class="px-5 py-4 font-semibold">Aksi</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
              <p
                id="userEmptyState"
                class="hidden px-5 py-4 text-sm text-slate-500"
              >
                Tidak ada user yang cocok dengan filter.
              </p>
            </div>
          </section>
        </div>

        <div id="userFormPage" class="page-section hidden">
          <section
            class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
          >
            <div class="mb-6">
              <p
                id="userFormBreadcrumb"
                class="text-sm font-medium text-slate-500"
              >
                User &rsaquo; Create
              </p>
              <h3
                id="userFormHeading"
                class="mt-2 text-xl font-semibold text-slate-900"
              >
                Create User
              </h3>
            </div>
            <form class="space-y-8">
              <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div class="xl:col-span-2">
                  <label
                    for="newUserName"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Name</label
                  >
                  <input
                    id="newUserName"
                    type="text"
                    placeholder="Masukkan nama"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                  />
                </div>
                <div>
                  <label
                    for="newUserEmail"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Email</label
                  >
                  <input
                    id="newUserEmail"
                    type="email"
                    placeholder="Masukkan email"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                  />
                </div>
                <div>
                  <label
                    for="newUserPassword"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Password</label
                  >
                  <input
                    id="newUserPassword"
                    type="password"
                    placeholder="Masukkan password atau password baru"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                  />
                </div>
                <div>
                  <label
                    for="newUserRole"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Role</label
                  >
                  <select
                    id="newUserRole"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                  >
                    <option>Super Admin</option>
                    <option>Petugas</option>
                    <option>Nasabah</option>
                  </select>
                </div>
                <div class="xl:col-span-3">
                  <label
                    for="newUserStatus"
                    class="mb-2 block text-sm font-medium text-slate-700"
                    >Status Akses</label
                  >
                  <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5"
                  >
                    <select
                      id="newUserStatus"
                      class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 outline-none transition focus:border-sky-400"
                    >
                      <option value="Aktif">Aktif</option>
                      <option value="Inactive">Inactive</option>
                    </select>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                      Jika nasabah atau petugas lupa password atau gagal login,
                      super admin dapat langsung melakukan reset password dari
                      sisi admin.
                    </p>
                  </div>
                </div>
              </div>

              <div class="space-y-6">
                <div>
                  <h4 class="text-base font-semibold text-slate-900">
                    User Custom Access
                  </h4>
                  <p class="mt-1 text-sm text-slate-500">
                    Akses menu dan fitur dapat dipilih manual menggunakan
                    checkbox.
                  </p>
                  <div class="mt-4 flex flex-wrap gap-3">
                    <button
                      id="checkAllUserAccess"
                      type="button"
                      class="rounded-xl bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-200"
                    >
                      Check All
                    </button>
                    <button
                      id="uncheckAllUserAccess"
                      type="button"
                      class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                    >
                      Uncheck All
                    </button>
                  </div>
                </div>

                <div id="userCustomAccessSection">
                  <h5
                    class="border-b border-slate-200 pb-3 text-sm font-semibold uppercase tracking-[0.12em] text-slate-700"
                  >
                    Akses Menu
                  </h5>
                  <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Dashboard
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Nasabah
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Jenis Sampah
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Sampah
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Transaksi
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Pembayaran
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Pencairan Saldo
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />User
                    </label>
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                    >
                      <input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Laporan
                    </label>
                  </div>
                </div>

                <div id="userOperationalAccessSection">
                  <div
                    class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-3"
                  >
                    <h5
                      class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-700"
                    >
                      Fitur Operasional
                    </h5>
                    <div class="flex flex-wrap gap-2">
                      <button
                        id="checkAllOperationalAccess"
                        type="button"
                        class="rounded-xl bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-200"
                      >
                        Check All Fitur
                      </button>
                      <button
                        id="uncheckAllOperationalAccess"
                        type="button"
                        class="rounded-xl bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-300"
                      >
                        Uncheck All Fitur
                      </button>
                    </div>
                  </div>
                  <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Create Nasabah</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Edit Nasabah</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Create Transaksi</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Approve Pembayaran</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Ajukan Pencairan Saldo</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Verifikasi Pencairan Saldo</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Export Laporan</label
                    >
                    <label
                      class="inline-flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700"
                      ><input
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                      />Manage User Access</label
                    >
                  </div>
                </div>
              </div>

              <div class="flex gap-3">
                <button
                  id="saveUserButton"
                  type="button"
                  class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
                >
                  Simpan User
                </button>
                <button
                  id="backToUserList"
                  type="button"
                  class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                >
                  Kembali
                </button>
              </div>
            </form>
          </section>
        </div>

        <div id="pencairanSaldoPage" class="page-section hidden">
          <div class="space-y-6">
            <section class="grid gap-4 md:grid-cols-3">
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100 md:col-span-2"
              >
                <p class="text-sm font-medium text-slate-500">Saldo Tersedia</p>
                <h3 class="mt-4 text-5xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
              <article
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <p class="text-sm font-medium text-slate-500">
                  Status Terakhir
                </p>
                <h3 class="mt-4 text-2xl font-bold text-slate-900">-</h3>
                <p class="mt-3 text-sm text-slate-500">Data belum tersedia</p>
              </article>
            </section>
            <section
              class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(340px,0.8fr)]"
            >
              <div
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
              >
                <div class="border-b border-slate-100 pb-4">
                  <h3 class="text-xl font-semibold text-slate-900">
                    Ajukan Pencairan Saldo
                  </h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Isi form berikut untuk mengajukan pencairan ke cash atau
                    e-wallet.
                  </p>
                </div>
                <form class="mt-5 space-y-5">
                  <div>
                    <label
                      for="withdrawNominal"
                      class="mb-2 block text-sm font-medium text-slate-700"
                      >Nominal Pencairan</label
                    >
                    <input
                      id="withdrawNominal"
                      type="text"
                      placeholder="Contoh: 250000"
                      class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                    />
                  </div>
                  <div>
                    <label
                      for="withdrawMethod"
                      class="mb-2 block text-sm font-medium text-slate-700"
                      >Metode Pencairan</label
                    >
                    <select
                      id="withdrawMethod"
                      class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                    >
                      <option>Cash</option>
                      <option>E-Wallet</option>
                    </select>
                  </div>
                  <div
                    id="nasabahEwalletFields"
                    class="hidden space-y-5 rounded-2xl border border-sky-200 bg-sky-50 p-4"
                  >
                    <div>
                      <label
                        for="withdrawEwalletType"
                        class="mb-2 block text-sm font-medium text-slate-700"
                        >Jenis E-Wallet</label
                      >
                      <select
                        id="withdrawEwalletType"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                      >
                        <option>DANA</option>
                        <option>OVO</option>
                        <option>GoPay</option>
                        <option>LinkAja</option>
                      </select>
                    </div>
                    <div>
                      <label
                        for="withdrawEwalletNumber"
                        class="mb-2 block text-sm font-medium text-slate-700"
                        >Nomor E-Wallet</label
                      >
                      <input
                        id="withdrawEwalletNumber"
                        type="text"
                        placeholder="Masukkan nomor e-wallet"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label
                      for="withdrawNotes"
                      class="mb-2 block text-sm font-medium text-slate-700"
                      >Catatan</label
                    >
                    <textarea
                      id="withdrawNotes"
                      rows="4"
                      placeholder="Tambahkan catatan jika diperlukan"
                      class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-sky-400 focus:bg-white"
                    ></textarea>
                  </div>
                  <div class="flex gap-3">
                    <button
                      type="button"
                      class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    >
                      Ajukan Pencairan
                    </button>
                    <button
                      type="button"
                      class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                    >
                      Reset Form
                    </button>
                  </div>
                </form>
              </div>
              <div
                class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-100"
              >
                <div class="border-b border-slate-100 pb-4">
                  <h3 class="text-lg font-semibold text-slate-900">
                    Panduan Nasabah
                  </h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Langkah singkat sebelum mengajukan pencairan.
                  </p>
                </div>
                <div class="mt-5 space-y-4">
                  <div class="rounded-[24px] bg-sky-50 p-5">
                    <p class="text-sm font-semibold text-sky-700">
                      1. Cek Saldo
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                      Pastikan saldo tersedia cukup dan nominal yang diajukan
                      sesuai.
                    </p>
                  </div>
                  <div class="rounded-[24px] bg-emerald-50 p-5">
                    <p class="text-sm font-semibold text-emerald-700">
                      2. Pilih Metode
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                      Pilih cash atau e-wallet, lalu lengkapi data pencairan.
                    </p>
                  </div>
                  <div class="rounded-[24px] bg-amber-50 p-5">
                    <p class="text-sm font-semibold text-amber-700">
                      3. Tunggu Verifikasi
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                      Admin akan meninjau permintaan sebelum status berubah
                      menjadi berhasil.
                    </p>
                  </div>
                </div>
              </div>
            </section>
            <section
              class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
              <div class="border-b border-slate-100 pb-4">
                <h3 class="text-xl font-semibold text-slate-900">
                  Riwayat Pencairan
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                  Lihat status pencairan saldo yang pernah diajukan nasabah.
                </p>
              </div>
              <div class="mt-5 overflow-x-auto">
                <table
                  class="min-w-full border border-slate-200 text-left text-sm"
                >
                  <thead class="bg-white">
                    <tr class="text-slate-900">
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Tanggal
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Nominal
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Metode
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Tujuan
                      </th>
                      <th
                        class="border border-slate-200 px-4 py-3 font-semibold"
                      >
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </section>
          </div>
        </div>
      </section>

      <footer class="border-t border-slate-200 bg-white px-6 py-5">
        <p class="text-sm font-medium text-slate-500">
          &copy; 2026 Sistem Informasi Bank Sampah Sedap Malam Berbasis Web
        </p>
      </footer>
    </main>
  </div>

  <div
    id="profilePanelOverlay"
    class="fixed inset-0 z-50 hidden bg-slate-900/40 p-4 backdrop-blur-sm"
  >
    <div class="flex min-h-full items-center justify-center">
      <section
        class="w-full max-w-lg rounded-[28px] bg-white p-6 shadow-xl ring-1 ring-slate-200"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-xl font-semibold text-slate-900">
              Edit Profil Admin
            </h3>
            <p class="mt-1 text-sm text-slate-500">
              Klik profil untuk mengubah nama, role, atau menambahkan foto.
            </p>
          </div>
          <button
            id="closeProfilePanel"
            type="button"
            class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200"
          >
            Tutup
          </button>
        </div>

        <form id="profileForm" class="mt-6 space-y-5">
          <div
            class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4"
          >
            <div
              id="profilePreview"
              class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-emerald-100 text-xl font-bold text-emerald-700"
            >
              AD
            </div>
            <div>
              <p class="text-sm font-semibold text-slate-900">Preview Profil</p>
              <p class="text-xs text-slate-500">
                Foto akan tampil di header dashboard.
              </p>
            </div>
          </div>
          <div>
            <label
              for="profileNameInput"
              class="mb-2 block text-sm font-medium text-slate-700"
              >Nama</label
            >
            <input
              id="profileNameInput"
              type="text"
              class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
            />
          </div>
          <div>
            <label
              for="profileRoleInput"
              class="mb-2 block text-sm font-medium text-slate-700"
              >Role</label
            >
            <input
              id="profileRoleInput"
              type="text"
              class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
            />
          </div>
          <div>
            <label
              for="profilePhotoInput"
              class="mb-2 block text-sm font-medium text-slate-700"
              >Foto Profil</label
            >
            <input
              id="profilePhotoInput"
              type="file"
              class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-sky-400 focus:bg-white"
            />
          </div>
          <div class="flex gap-3">
            <button
              type="submit"
              class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
            >
              Simpan
            </button>
            <button
              id="resetProfilePhoto"
              type="button"
              class="rounded-xl bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
            >
              Reset Foto
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
</template>
