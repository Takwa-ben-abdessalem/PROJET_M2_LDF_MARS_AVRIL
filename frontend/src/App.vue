<template>
  <AppNavbar />
  <main class="app-shell">
    <RouterView />
  </main>
  <CookieBanner />
</template>

<script setup>
import AppNavbar from './components/AppNavbar.vue'
import CookieBanner from './components/CookieBanner.vue'
</script>
