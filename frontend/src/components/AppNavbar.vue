<template>
  <nav class="app-navbar">
    <div class="app-navbar-inner">
      <RouterLink class="brand-mark" to="/" aria-label="Accueil EventFlow">
        <span class="brand-icon">EF</span>
        <span>EventFlow</span>
      </RouterLink>

      <div class="nav-links">
        <RouterLink v-if="auth.isAuthenticated" class="nav-link" to="/events">Événements</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" class="nav-link" to="/dashboard/registrations">Inscriptions</RouterLink>
        <RouterLink v-if="auth.isOrganizer" class="nav-link" to="/dashboard/my-events">Mes événements</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" class="nav-link" to="/dashboard">Dashboard</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" class="nav-link" to="/profile">Profil RGPD</RouterLink>
        <RouterLink class="nav-link" to="/privacy">Confidentialité</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" class="nav-button secondary" to="/register">Inscription</RouterLink>
        <RouterLink v-if="!auth.isAuthenticated" class="nav-button primary" to="/login">Connexion</RouterLink>
        <button v-else class="nav-button secondary" type="button" @click="logout">Déconnexion</button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

function logout() {
  auth.logout()
  router.push('/login')
}
</script>
