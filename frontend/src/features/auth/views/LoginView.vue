<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const errorMessage = ref("");

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
  }
}
</script>

<template>
  <main class="login-wrap">
    <section class="login-card">
      <h1>Bank Sampah SPA</h1>
      <p>Masuk untuk mengakses dashboard operasional.</p>

      <form class="login-form" @submit.prevent="submitLogin">
        <label>
          Email
          <input v-model="form.email" type="email" required />
        </label>

        <label>
          Password
          <input v-model="form.password" type="password" required />
        </label>

        <button type="submit" :disabled="authStore.loading">
          {{ authStore.loading ? "Memproses..." : "Login" }}
        </button>

        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </form>
    </section>
  </main>
</template>

<style scoped>
.login-wrap {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 1.5rem;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
}

.login-form {
  display: grid;
  gap: 1rem;
}

input {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 12px;
  padding: 0.7rem 0.9rem;
  margin-top: 0.45rem;
}

button {
  border: 0;
  border-radius: 12px;
  padding: 0.7rem 0.9rem;
  background: #0f766e;
  color: #fff;
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error {
  color: #be123c;
}
</style>
