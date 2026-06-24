<template>
  <section class="toolbar">
    <div>
      <p class="muted">Evenements publies</p>
      <h1>Agenda professionnel</h1>
    </div>
    <RouterLink v-if="auth.isOrganizer" class="button" to="/events/create">Nouvel evenement</RouterLink>
  </section>

  <p v-if="error" class="alert error">{{ error }}</p>
  <section class="event-grid">
    <EventCard v-for="event in events" :key="event.id" :event="event" />
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
