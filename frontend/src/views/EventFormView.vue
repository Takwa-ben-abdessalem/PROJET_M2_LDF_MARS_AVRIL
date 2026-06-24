<template>
  <section class="panel readable">
    <p class="kicker">{{ id ? 'Édition' : 'Création' }}</p>
    <h1>{{ id ? 'Modifier l’événement' : 'Nouvel événement' }}</h1>
    <p class="muted">Renseignez les informations clés, puis publiez l'événement lorsqu'il est prêt.</p>

    <form @submit.prevent="submit">
      <BaseInput v-model="form.title" label="Titre" required />
      <BaseInput v-model="form.description" label="Description" type="textarea" required />
      <BaseInput v-model="form.eventDate" label="Date" type="datetime-local" :min="todayMin" required />
      <BaseInput v-model="form.location" label="Lieu" required />
      <BaseInput v-model="form.maxParticipants" label="Participants max" type="number" required />
      <label class="check-line">
        <input v-model="form.isPublished" type="checkbox" />
        Publier l'événement immédiatement
      </label>
      <p v-if="success" class="alert success">{{ success }}</p>
      <p v-if="error" class="alert error">{{ error }}</p>
      <div class="form-actions">
        <button class="button" :disabled="loading" type="submit">Enregistrer</button>
        <RouterLink class="button ghost" to="/dashboard/my-events">Annuler</RouterLink>
      </div>
    </form>
  </section>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { http } from '../api/http'
import BaseInput from '../components/BaseInput.vue'
import { useRequest } from '../composables/useRequest'

const props = defineProps({ id: { type: String, default: '' } })
const router = useRouter()
const { loading, error, success, run } = useRequest()
const form = reactive({
  title: '',
  description: '',
  eventDate: '',
  location: '',
  maxParticipants: 50,
  isPublished: true,
})

const todayMin = computed(() => {
  const now = new Date()
  now.setHours(0, 0, 0, 0)
  const offset = now.getTimezoneOffset() * 60000
  return new Date(now.getTime() - offset).toISOString().slice(0, 16)
})

onMounted(async () => {
  if (!props.id) return
  await run(async () => {
    const { data } = await http.get(`/events/${props.id}`)
    form.title = data.title
    form.description = data.description
    form.eventDate = data.eventDate.slice(0, 16)
    form.location = data.location
    form.maxParticipants = data.maxParticipants
    form.isPublished = data.isPublished
  })
})

async function submit() {
  const payload = { ...form, maxParticipants: Number(form.maxParticipants) }
  const request = props.id ? http.put(`/events/${props.id}`, payload) : http.post('/events', payload)
  await run(() => request, 'Événement enregistré.')
  router.push('/dashboard/my-events')
}
</script>
