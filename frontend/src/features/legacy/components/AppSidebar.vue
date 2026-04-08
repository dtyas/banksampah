<template>
  <aside
    class="w-full border-b border-slate-200 bg-white lg:min-h-screen lg:w-[300px] lg:border-b-0 lg:border-r"
  >
    <div class="px-6 py-7">
      <h1 class="text-2xl font-bold uppercase tracking-[0.28em] text-emerald-500">
        Bank Sampah
      </h1>
      <p class="mt-2 text-lg font-semibold text-slate-900">Admin Dashboard</p>
    </div>

    <nav class="px-5 pb-6">
      <ul class="space-y-2">
        <li>
          <a
            href="#"
            data-page="dashboard"
            class="nav-link flex items-center gap-4 rounded-[22px] bg-sky-50 px-5 py-3.5 text-sky-700"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M3 12l8.485-8.485a2 2 0 012.828 0L22 12M5 10.5V19a2 2 0 002 2h10a2 2 0 002-2v-8.5"
                />
              </svg>
            </span>
            <span class="text-[15px] font-semibold">Dashboard</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="nasabah"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
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
            <span class="text-[15px] font-medium">Nasabah</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="jenisSampah"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M7 8h10M7 12h10M7 16h7M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                />
              </svg>
            </span>
            <span class="text-[15px] font-medium">Kategori Sampah</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="sampah"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                />
              </svg>
            </span>
            <span class="text-[15px] font-medium">Sampah</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="transaksi"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
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
            <span class="text-[15px] font-medium">Transaksi</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="pembayaran"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M17 9V7a5 5 0 00-10 0v2M6 9h12l-1 10H7L6 9zm6 3v4m-2-2h4"
                />
              </svg>
            </span>
            <span class="text-[15px] font-medium">Pembayaran</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="pencairanSaldo"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10v12m8-6a8 8 0 11-16 0 8 8 0 0116 0z"
                />
              </svg>
            </span>
            <span class="text-[15px] font-medium">Pencairan Saldo</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="user"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
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
            <span class="text-[15px] font-medium">User</span>
          </a>
        </li>
        <li>
          <a
            href="#"
            data-page="laporan"
            class="nav-link flex items-center gap-4 rounded-[22px] px-5 py-3.5 text-slate-700 transition hover:bg-slate-100"
          >
            <span
              class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-500"
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
            <span class="text-[15px] font-medium">Laporan</span>
          </a>
        </li>
      </ul>
    </nav>
  </aside>
</template>
