<template>
  <section class="toolbar">
    <div>
      <p class="kicker">Espace organisateur</p>
      <h1>Mes événements</h1>
      <p class="muted">Pilotez vos brouillons et vos événements publiés depuis un seul endroit.</p>
    </div>
    <RouterLink class="button" to="/events/create">Créer un événement</RouterLink>
  </section>

  <p v-if="error" class="alert error">{{ error }}</p>

  <section v-if="events.length" class="my-events-list">
    <article v-for="event in events" :key="event.id" class="my-event-row">
      <div>
        <span :class="['status-pill', event.isPublished ? 'published' : 'draft']">
          {{ event.isPublished ? 'Publié' : 'Brouillon' }}
        </span>
        <h2>{{ event.title }}</h2>
        <p class="meta-line">
          <span class="meta-chip">📅 {{ formatDate(event.eventDate) }}</span>
          <span class="meta-chip">📍 {{ event.location }}</span>
        </p>
        <p>{{ event.description }}</p>
      </div>
      <div class="row-actions">
        <RouterLink v-if="event.isPublished" class="button ghost" :to="`/events/${event.id}`">Voir</RouterLink>
        <RouterLink class="button" :to="`/events/${event.id}/edit`">Modifier</RouterLink>
        <button class="button danger" type="button" @click="deleteEvent(event)">Supprimer</button>
      </div>
    </article>
  </section>

  <section v-else class="panel empty-state">
    <h2>Aucun événement pour le moment</h2>
    <p>Créez un événement publié pour le rendre visible dans la liste publique, ou gardez un brouillon pour le finaliser plus tard.</p>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { http } from '../api/http'
import { useRequest } from '../composables/useRequest'

const events = ref([])
const { error, run } = useRequest()

onMounted(() => {
  run(async () => {
    const { data } = await http.get('/events/my')
    events.value = data
  }).catch(() => {})
})

function formatDate(value) {
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

async function deleteEvent(event) {
  if (!confirm(`Supprimer définitivement "${event.title}" ?`)) return

  await run(async () => {
    await http.delete(`/events/${event.id}`)
    events.value = events.value.filter((item) => item.id !== event.id)
  })
}
</script>
