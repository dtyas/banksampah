<script setup lang="ts">
defineProps<{
  id?: string;
  type?: string;
  class?: string;
  placeholder?: string;
  autocomplete?: string;
}>();
</script>

<template>
  <input
    :id="id"
    :type="type ?? 'text'"
    :class="class"
    :placeholder="placeholder"
    :autocomplete="autocomplete"
  />
</template>
