<template>
  <section class="toolbar">
    <div>
      <p class="kicker">Événements publiés</p>
      <h1>Agenda professionnel</h1>
      <p class="muted">Découvrez les prochaines rencontres et inscrivez-vous en quelques clics.</p>
    </div>
    <RouterLink v-if="auth.isOrganizer" class="button" to="/events/create">Nouvel événement</RouterLink>
  </section>

  <p v-if="error" class="alert error">{{ error }}</p>

  <section v-if="events.length" class="event-grid">
    <EventCard v-for="event in events" :key="event.id" :event="event" />
  </section>

  <section v-else class="panel empty-state">
    <h2>Aucun événement publié</h2>
    <p>Revenez bientôt ou créez un premier événement depuis l'espace organisateur.</p>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { http } from '../api/http'
import EventCard from '../components/EventCard.vue'
import { useAuthStore } from '../stores/auth'
import { useRequest } from '../composables/useRequest'

const events = ref([])
const auth = useAuthStore()
const { error, run } = useRequest()

onMounted(() => {
  run(async () => {
    const { data } = await http.get('/events')
    events.value = data
  }).catch(() => {})
})
</script>
