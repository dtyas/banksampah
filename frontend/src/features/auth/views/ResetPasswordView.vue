<script setup lang="ts">
import { ref, reactive } from "vue";
import { useRouter, useRoute } from "vue-router";
import { resetPassword } from "../../../api/auth.api";

const router = useRouter();
const route = useRoute();
const token = ref(route.query.token as string || "");
const email = ref(route.query.email as string || "");
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = reactive({
  password: "",
  password_confirmation: "",
});

async function submitResetPassword() {
  errorMessage.value = "";
  successMessage.value = "";
  loading.value = true;

  if (form.password !== form.password_confirmation) {
    errorMessage.value = "Konfirmasi password tidak cocok";
    loading.value = false;
    return;
  }

  try {
    const response = await resetPassword({
      token: token.value,
      email: email.value,
      password: form.password,
      password_confirmation: form.password_confirmation,
    });

    if (!response.status) {
      throw new Error(response.message || "Reset password gagal");
    }

    successMessage.value = response.message;
    setTimeout(() => {
      router.push({ name: "login" });
    }, 2000);
  } catch (error) {
    const message =
      error?.response?.data?.message || error.message || "Reset password gagal";
    errorMessage.value = message;
  } finally {
    loading.value = false;
  }
}

function backToLogin() {
  router.push({ name: "login" });
}
</script>

<template>
  <div id="resetPasswordPage" class="min-h-screen bg-slate-50 px-6 py-10">
    <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center">
      <div
        class="grid w-full gap-8 overflow-hidden rounded-[36px] bg-white shadow-sm ring-1 ring-slate-200 lg:grid-cols-[1.15fr_0.85fr]"
      >
        <!-- Left Panel - Branding -->
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

        <!-- Right Panel - Form -->
        <section class="px-8 py-10 lg:px-12 lg:py-14">
          <div class="mx-auto max-w-md">
            <p
              class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-500"
            >
              Reset Password
            </p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">
              Buat Password Baru
            </h2>
            <p class="mt-3 text-sm text-slate-500">
              Masukkan password baru Anda untuk reset akses akun.
            </p>

            <!-- Success State -->
            <div v-if="successMessage" class="mt-8">
              <div
                class="rounded-2xl bg-emerald-50 p-6 text-center ring-1 ring-emerald-100"
              >
                <div
                  class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-emerald-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-emerald-900">
                  Password Berhasil Direset
                </h3>
                <p class="mt-2 text-sm text-emerald-700">
                  {{ successMessage }}
                </p>
                <p class="mt-3 text-xs text-emerald-600">
                  Mengalihkan ke halaman login...
                </p>
              </div>
            </div>

            <!-- Form State -->
            <form v-else class="mt-8 space-y-5" @submit.prevent="submitResetPassword">
              <div v-if="!token" class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 ring-1 ring-amber-100">
                ⚠️ Token reset tidak ditemukan. Silakan gunakan link dari email.
              </div>
              <div>
                <label
                  for="resetEmail"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Email</label
                >
                <input
                  id="resetEmail"
                  v-model="email"
                  type="email"
                  placeholder="nama@email.com"
                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 outline-none transition focus:border-emerald-400 focus:bg-white"
                  required
                />
              </div>
              <div>
                <label
                  for="resetPassword"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Password Baru</label
                >
                <div class="relative">
                  <input
                    id="resetPassword"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-12 outline-none transition focus:border-emerald-400 focus:bg-white"
                    required
                  />
                  <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-emerald-600"
                    @click="showPassword = !showPassword"
                  >
                    {{ showPassword ? "Hide" : "Show" }}
                  </button>
                </div>
              </div>
              <div>
                <label
                  for="resetPasswordConfirm"
                  class="mb-2 block text-sm font-medium text-slate-700"
                  >Konfirmasi Password</label
                >
                <div class="relative">
                  <input
                    id="resetPasswordConfirm"
                    v-model="form.password_confirmation"
                    :type="showPasswordConfirmation ? 'text' : 'password'"
                    placeholder="Ulangi password baru"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-12 outline-none transition focus:border-emerald-400 focus:bg-white"
                    required
                  />
                  <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-emerald-600"
                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                  >
                    {{ showPasswordConfirmation ? "Hide" : "Show" }}
                  </button>
                </div>
              </div>
              <button
                type="submit"
                class="w-full rounded-2xl bg-emerald-600 px-5 py-3.5 text-base font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-70"
                :disabled="loading || !token"
              >
                {{ loading ? "Mereset..." : "Reset Password" }}
              </button>
              <p v-if="errorMessage" class="text-sm font-medium text-rose-600">
                {{ errorMessage }}
              </p>
              <div class="text-center">
                <button
                  type="button"
                  class="text-sm font-medium text-slate-600 transition hover:text-emerald-600"
                  @click="backToLogin"
                >
                  ← Kembali ke Login
                </button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>
