<template>
  <section v-if="visible" class="cookie-banner" aria-label="Préférences cookies">
    <div>
      <strong>Préférences cookies</strong>
      <p>Choisissez les catégories acceptées pour cette démonstration.</p>
    </div>
    <label><input type="checkbox" checked disabled /> Nécessaires</label>
    <label><input v-model="stats" type="checkbox" /> Statistiques</label>
    <label><input v-model="marketing" type="checkbox" /> Marketing</label>
    <button class="button" type="button" @click="save">Enregistrer</button>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { http } from '../api/http'
import { useAuthStore } from '../stores/auth'

const saved = JSON.parse(localStorage.getItem('eventflow_cookie_preferences') || 'null')
const visible = ref(!saved)
const stats = ref(saved?.statistics ?? saved?.stats ?? false)
const marketing = ref(saved?.marketing || false)
const auth = useAuthStore()

onMounted(async () => {
  if (!auth.isAuthenticated) return

  try {
    const { data } = await http.get('/me/cookie-preferences')
    stats.value = data.statistics
    marketing.value = data.marketing
  } catch {
    // Les visiteurs gardent simplement le choix local si l'API n'est pas disponible.
  }
})

async function save() {
  const preferences = {
    necessary: true,
    statistics: stats.value,
    marketing: marketing.value,
  }

  localStorage.setItem('eventflow_cookie_preferences', JSON.stringify(preferences))

  if (auth.isAuthenticated) {
    try {
      await http.put('/me/cookie-preferences', preferences)
    } catch {
      // Le choix local reste la source de secours si la session expire.
    }
  }

  visible.value = false
}
</script>
