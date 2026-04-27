<template>
  <button
    type="button"
    :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
    @click="$emit('toggle')"
    class="focus:outline-none"
  >
    <span v-if="show">
      <!-- Eye open icon -->
      <svg
        fill="#000000"
        width="24px"
        height="24px"
        viewBox="0 0 1.08 1.08"
        version="1.1"
        preserveAspectRatio="xMidYMid meet"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
      >
        <title>eye-line</title>
        <path
          d="M1.009 0.526c-0.101 -0.187 -0.278 -0.3 -0.475 -0.3S0.16 0.339 0.06 0.526l-0.008 0.014 0.008 0.014c0.101 0.187 0.278 0.3 0.475 0.3s0.374 -0.112 0.475 -0.3l0.008 -0.014Zm-0.475 0.267C0.365 0.793 0.21 0.699 0.12 0.54c0.09 -0.159 0.245 -0.253 0.414 -0.253S0.856 0.382 0.948 0.54c-0.091 0.159 -0.245 0.253 -0.414 0.253"
          class="clr-i-outline clr-i-outline-path-1"
        />
        <path
          d="M0.543 0.335A0.206 0.206 0 1 0 0.75 0.54a0.206 0.206 0 0 0 -0.207 -0.205m0 0.352A0.146 0.146 0 1 1 0.69 0.54a0.146 0.146 0 0 1 -0.147 0.147"
          class="clr-i-outline clr-i-outline-path-2"
        />
        <path
          x="0"
          y="0"
          width="36"
          height="36"
          fill-opacity="0"
          d="M0 0H1.08V1.08H0V0z"
        />
      </svg>
    </span>
    <span v-else>
      <!-- Eye closed icon -->
      <svg
        width="24px"
        height="24px"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M9.76406 5.29519C10.4664 5.10724 11.2123 5 12 5C18.3636 5 22 12 22 12C22 12 21.171 13.5958 19.612 15.2635M4.34912 8.77822C2.8152 10.4307 2 12 2 12C2 12 5.63636 19 12 19C12.8021 19 13.5608 18.8888 14.2744 18.6944M11.5 14.9585C10.4158 14.7766 9.52883 14.0132 9.17071 13M12.5 9.04148C13.7563 9.25224 14.7478 10.2437 14.9585 11.5M3 3L21 21"
          stroke="#001A72"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </span>
  </button>
</template>

<script setup lang="ts">
defineProps<{ show: boolean }>();
const emit = defineEmits<{ (e: "toggle"): void }>();
</script>
