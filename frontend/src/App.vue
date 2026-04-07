<script setup>
import { onMounted, ref } from "vue";
import { Toaster } from "vue-sonner";
import legacyHtml from "../index-legacy.html?raw";

const legacyBody = ref("");

onMounted(() => {
  const bodyMatch = legacyHtml.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
  const rawBody = bodyMatch ? bodyMatch[1] : legacyHtml;
  legacyBody.value = rawBody.replace(
    /<script[^>]*src="assets\/js\/app\.js"[^>]*><\/script>/i,
    "",
  );

  import("../assets/js/app.js");
});
</script>

<template>
  <div
    class="min-h-screen bg-slate-50 text-slate-800"
    v-html="legacyBody"
  ></div>
  <Toaster richColors position="top-right" closeButton />
</template>
