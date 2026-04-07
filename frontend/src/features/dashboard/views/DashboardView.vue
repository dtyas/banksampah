<script setup lang="ts">
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  try {
    await authStore.hydrateUser();
  } catch (error) {
    await authStore.signOut();
    await router.push({ name: "login" });
  }
});

async function logout() {
  await authStore.signOut();
  await router.push({ name: "login" });
}
</script>

<template>
  <main class="dashboard-wrap">
    <header class="topbar">
      <h1>Dashboard Bank Sampah</h1>
      <button @click="logout">Logout</button>
    </header>

    <section class="panel">
      <h2>Selamat datang</h2>
      <p v-if="authStore.user">
        {{ authStore.user.nama }} ({{ authStore.user.role }})
      </p>
      <p v-else>Memuat profil user...</p>
    </section>
  </main>
</template>

<style scoped>
.dashboard-wrap {
  min-height: 100vh;
  padding: 1.5rem;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.panel {
  background: #ffffff;
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
}

button {
  border: 0;
  border-radius: 10px;
  padding: 0.55rem 0.8rem;
  background: #ef4444;
  color: #fff;
  cursor: pointer;
}
</style>
