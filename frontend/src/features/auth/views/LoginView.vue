<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const errorMessage = ref("");
const showPassword = ref(false);

const form = reactive({
  email: "",
  password: "",
  device_name: "vue-spa",
});

async function submitLogin() {
  errorMessage.value = "";

  try {
    await authStore.signIn(form);
    await router.push({ name: "dashboard" });
  } catch (error) {
    const message =
      error?.response?.data?.message || error.message || "Login gagal";
    errorMessage.value = message;
    form.password = "";
  }
}

function togglePassword(): void {
  showPassword.value = !showPassword.value;
}
</script>

<template>
  <div id="loginPage" class="min-h-screen bg-slate-50 px-6 py-10">
    <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center">
      <div
        class="grid w-full gap-8 overflow-hidden rounded-[36px] bg-white shadow-sm ring-1 ring-slate-200 lg:grid-cols-[1.15fr_0.85fr]"
      >
        <section
          class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-500 to-sky-500 px-8 py-10 text-white lg:px-12 lg:py-14"
        >
          <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.16),transparent_30%)]"
          ></div>
          <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
              class="absolute left-10 top-10 flex h-24 w-24 items-center justify-center rounded-[28px] bg-white/12 backdrop-blur-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 text-white/80"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M5 7h14M10 11v6m4-6v6M9 4h6l1 3H8l1-3zm-2 3h10l-1 12a2 2 0 01-2 2H9a2 2 0 01-2-2L6 7z"
                />
              </svg>
            </div>
            <div
              class="absolute bottom-12 right-10 flex h-28 w-28 items-center justify-center rounded-full bg-white/12 backdrop-blur-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-14 w-14 text-white/80"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M12 3v18m4-14.5A3.5 3.5 0 0012.5 3H11a4 4 0 000 8h2a4 4 0 110 8H11.5A3.5 3.5 0 018 17.5"
                />
              </svg>
            </div>
            <div
              class="absolute right-24 top-24 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white/75"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M8 7V6a4 4 0 118 0v1m-9 0h10l-1 12a2 2 0 01-2 2H10a2 2 0 01-2-2L7 7zm3 4h4m-4 4h4"
                />
              </svg>
            </div>
          </div>
          <div
            class="relative flex min-h-full items-center justify-center text-center"
          >
            <div class="max-w-2xl">
              <div
                class="mx-auto flex w-fit items-center gap-3 rounded-full border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-sm"
              >
                <span
                  class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M5 7h14M10 11v6m4-6v6M9 4h6l1 3H8l1-3zm-2 3h10l-1 12a2 2 0 01-2 2H9a2 2 0 01-2-2L6 7z"
                    />
                  </svg>
                </span>
                <span class="text-lg font-semibold text-white/95"
                  >Recycle to Value</span
                >
                <span
                  class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M12 3v18m4-14.5A3.5 3.5 0 0012.5 3H11a4 4 0 000 8h2a4 4 0 110 8H11.5A3.5 3.5 0 018 17.5"
                    />
                  </svg>
                </span>
              </div>
              <p
                class="text-xs font-semibold uppercase tracking-[0.45em] text-emerald-100"
              >
                Bank Sampah
              </p>
              <h1
                class="mx-auto mt-4 max-w-md text-4xl font-bold leading-tight"
              >
                Sistem Informasi Bank Sampah Sedap Malam
              </h1>
              <p class="mt-4 max-w-2xl text-base text-emerald-50/90">
                Kelola nasabah, transaksi setoran sampah, pencairan saldo, dan
                laporan operasional dalam satu dashboard admin.
              </p>
            </div>
          </div>
        </section>

        <section class="px-8 py-10 lg:px-12 lg:py-14">
          <div class="mx-auto max-w-md">
            <p
              class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-500"
            >
              Admin Login
            </p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">
              Masuk ke Dashboard
            </h2>
            <p class="mt-3 text-sm text-slate-500">
              Gunakan akun admin untuk mengakses seluruh fitur pengelolaan bank
              sampah.
            </p>

            <form class="mt-8 space-y-5" @submit.prevent="submitLogin">
              <div>
                <label
                  for="loginEmail"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Email / Username</label
                >
                <input
                  id="loginEmail"
                  v-model="form.email"
                  type="text"
                  placeholder="admin@banksampah.id"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-emerald-400 focus:bg-white"
                  required
                />
              </div>
              <div>
                <label
                  for="loginPassword"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Password</label
                >
                <div class="relative">
                  <input
                    id="loginPassword"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Masukkan password"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-12 outline-none transition focus:border-emerald-400 focus:bg-white"
                    required
                  />
                  <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-emerald-600"
                    :aria-label="
                      showPassword
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                    "
                    @click="togglePassword"
                  >
                    <svg
                      v-if="showPassword"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path
                        d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"
                      />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M3 3l18 18" />
                      <path d="M10.1 10.2a2.8 2.8 0 013.7 3.7" />
                      <path
                        d="M7.6 7.7C4.5 9.2 2 12 2 12s3.5 7 10 7c2.3 0 4.2-.6 5.7-1.5"
                      />
                      <path
                        d="M14.5 6.2A9.6 9.6 0 0112 5c-6.5 0-10 7-10 7a17.7 17.7 0 004.1 4.9"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <button
                type="submit"
                class="w-full rounded-2xl bg-emerald-600 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
                :disabled="authStore.loading"
              >
                Login
              </button>
              <p v-if="errorMessage" class="text-sm font-medium text-rose-600">
                {{ errorMessage }}
              </p>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
