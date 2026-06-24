<template>
  <section v-if="event" class="detail-layout">
    <article class="panel detail-main">
      <p class="kicker">Détail de l'événement</p>
      <h1>{{ event.title }}</h1>
      <div class="detail-meta">
        <span class="meta-chip">📅 {{ formattedDate }}</span>
        <span class="meta-chip">📍 {{ event.location }}</span>
      </div>
      <p class="detail-description">{{ event.description }}</p>

      <div class="metrics-grid">
        <article class="stat-card">
          <strong>{{ event.registeredCount }}</strong>
          <span>inscrits</span>
        </article>
        <article class="stat-card">
          <strong>{{ event.maxParticipants }}</strong>
          <span>places</span>
        </article>
        <article class="stat-card">
          <strong>{{ remainingSeats }}</strong>
          <span>restantes</span>
        </article>
      </div>

      <div class="capacity-row">
        <span class="capacity">Remplissage</span>
        <span class="muted">{{ capacityPercent }}%</span>
      </div>
      <div class="capacity-bar" aria-hidden="true">
        <span :style="{ width: `${capacityPercent}%` }"></span>
      </div>

      <p v-if="success" class="alert success">{{ success }}</p>
      <p v-if="error" class="alert error">{{ error }}</p>
    </article>

    <aside class="detail-side-card">
      <p class="kicker">Action rapide</p>
      <h2>Participer</h2>
      <p class="muted">Connectez-vous pour confirmer votre inscription à cet événement.</p>
      <div class="detail-actions">
        <button v-if="auth.isAuthenticated" class="button" :disabled="loading" type="button" @click="register">S'inscrire</button>
        <RouterLink v-else class="button" to="/login">Se connecter</RouterLink>
        <RouterLink v-if="canEdit" class="button ghost" :to="`/events/${event.id}/edit`">Modifier</RouterLink>
      </div>
    </aside>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { http } from '../api/http'
import { useAuthStore } from '../stores/auth'
import { useRequest } from '../composables/useRequest'

const props = defineProps({ id: { type: String, required: true } })
const event = ref(null)
const auth = useAuthStore()
const { loading, error, success, run } = useRequest()

const formattedDate = computed(() => event.value ? new Intl.DateTimeFormat('fr-FR', {
  dateStyle: 'full',
  timeStyle: 'short',
}).format(new Date(event.value.eventDate)) : '')

const capacityPercent = computed(() => {
  if (!event.value?.maxParticipants) return 0
  return Math.min(100, Math.round((event.value.registeredCount / event.value.maxParticipants) * 100))
})

const remainingSeats = computed(() => {
  if (!event.value) return 0
  return Math.max(0, event.value.maxParticipants - event.value.registeredCount)
})

const canEdit = computed(() => auth.isOrganizer && auth.user?.id === event.value?.organizer?.id)

onMounted(load)

async function load() {
  await run(async () => {
    const { data } = await http.get(`/events/${props.id}`)
    event.value = data
  })
}

async function register() {
  await run(() => http.post(`/events/${props.id}/register`), 'Inscription confirmée.')
  await load()
}
</script>
