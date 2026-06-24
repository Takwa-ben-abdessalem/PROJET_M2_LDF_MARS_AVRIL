<template>
  <section class="auth-panel">
    <p class="kicker">Bienvenue</p>
    <h1>Connexion</h1>
    <p class="muted">Accédez à votre espace EventFlow et reprenez là où vous vous êtes arrêtée.</p>
    <form @submit.prevent="submit">
      <BaseInput v-model="email" label="Email" type="email" required />
      <BaseInput v-model="password" label="Mot de passe" type="password" required />
      <p v-if="error" class="alert error">{{ error }}</p>
      <button class="button" :disabled="loading" type="submit">Se connecter</button>
    </form>
    <p class="auth-switch">
      Pas encore de compte ?
      <RouterLink to="/register">Créer un compte</RouterLink>
    </p>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '../components/BaseInput.vue'
import { useAuthStore } from '../stores/auth'
import { useRequest } from '../composables/useRequest'

const email = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()
const { loading, error, run } = useRequest()

async function submit() {
  try {
    await run(() => auth.login({ email: email.value, password: password.value }))
    router.push('/dashboard')
  } catch {
    // The form already displays the API error through useRequest.
  }
}
</script>
