<template>
  <section class="toolbar">
    <div>
      <p class="kicker">Espace utilisateur</p>
      <h1>Mes inscriptions</h1>
      <p class="muted">Suivez les événements auxquels vous participez et gérez vos réservations.</p>
    </div>
    <RouterLink class="button ghost" to="/events">Voir les événements</RouterLink>
  </section>

  <p v-if="success" class="alert success">{{ success }}</p>
  <p v-if="error" class="alert error">{{ error }}</p>

  <section v-if="registrations.length" class="my-events-list">
    <article v-for="registration in registrations" :key="registration.id" class="my-event-row">
      <div>
        <span class="status-pill published">{{ statusLabel(registration.status) }}</span>
        <h2>{{ registration.event.title }}</h2>
        <p class="meta-line">
          <span class="meta-chip">📅 {{ formatDate(registration.event.eventDate) }}</span>
          <span class="meta-chip">📍 {{ registration.event.location }}</span>
        </p>
        <p>Inscription effectuée le {{ formatDate(registration.registeredAt) }}</p>
      </div>
      <div class="row-actions">
        <RouterLink class="button ghost" :to="`/events/${registration.event.id}`">Voir</RouterLink>
        <button class="button danger" type="button" @click="cancelRegistration(registration)">
          Annuler
        </button>
      </div>
    </article>
  </section>

  <section v-else class="panel empty-state">
    <h2>Aucune inscription active</h2>
    <p>Vous pouvez consulter les événements disponibles et vous inscrire à ceux qui vous intéressent.</p>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { http } from '../api/http'
import { useRequest } from '../composables/useRequest'

const registrations = ref([])
const { error, success, run } = useRequest()

onMounted(loadRegistrations)

async function loadRegistrations() {
  await run(async () => {
    const { data } = await http.get('/me/registrations')
    registrations.value = data
  }).catch(() => {})
}

async function cancelRegistration(registration) {
  if (!confirm(`Annuler votre inscription à "${registration.event.title}" ?`)) return

  await run(async () => {
    await http.delete(`/registrations/${registration.id}`)
    await loadRegistrations()
  }, 'Inscription annulée.')
}

function formatDate(value) {
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

function statusLabel(status) {
  const labels = {
    confirmed: 'Confirmée',
    pending: 'En attente',
    cancelled: 'Annulée',
  }

  return labels[status] || status
}
</script>
