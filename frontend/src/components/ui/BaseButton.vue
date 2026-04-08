<script setup lang="ts">
defineProps<{
  type?: "button" | "submit" | "reset";
  class?: string;
  disabled?: boolean;
}>();
</script>

<template>
  <button :type="type ?? 'button'" :class="class" :disabled="disabled">
    <slot />
  </button>
</template>
