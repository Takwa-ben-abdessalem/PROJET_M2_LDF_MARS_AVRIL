<template>
  <section class="two-columns">
    <div class="panel">
      <p class="kicker">Compte personnel</p>
      <h1>Mes données</h1>
      <p class="muted">Modifiez vos informations et gardez le contrôle sur vos données.</p>

      <form @submit.prevent="save">
        <BaseInput v-model="form.firstName" label="Prénom" required />
        <BaseInput v-model="form.lastName" label="Nom" required />
        <BaseInput v-model="form.phone" label="Téléphone" />
        <p class="meta-chip">✉️ {{ auth.user?.email }}</p>
        <button class="button profile-main-action" type="submit">Mettre à jour mes informations</button>
      </form>

      <div class="profile-actions">
        <button class="button ghost" type="button" @click="exportData">Exporter mes données</button>
        <button class="button danger" type="button" @click="anonymize">Anonymiser mon compte</button>
      </div>
      <p v-if="success" class="alert success">{{ success }}</p>
      <p v-if="error" class="alert error">{{ error }}</p>
    </div>

    <div class="panel">
      <div class="panel-heading">
        <div>
          <p class="kicker">Traçabilité</p>
          <h2>Historique RGPD</h2>
        </div>
      </div>
      <p class="muted">
        Cette liste conserve les actions importantes liées à vos données personnelles.
      </p>
      <div v-if="logs.length" class="privacy-log-list">
        <article v-for="log in logs" :key="log.id" class="privacy-log-item">
          <div>
            <strong>{{ actionLabel(log.action) }}</strong>
            <span>{{ new Date(log.timestamp).toLocaleString('fr-FR') }}</span>
          </div>
          <p>{{ log.details }}</p>
        </article>
      </div>
      <p v-else class="empty-state">Aucune action RGPD enregistrée pour le moment.</p>
    </div>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { http } from '../api/http'
import BaseInput from '../components/BaseInput.vue'
import { useAuthStore } from '../stores/auth'
import { useRequest } from '../composables/useRequest'

const auth = useAuthStore()
const router = useRouter()
const logs = ref([])
const form = reactive({ firstName: '', lastName: '', phone: '' })
const { error, success, run } = useRequest()

onMounted(async () => {
  await auth.fetchProfile()
  form.firstName = auth.user.firstName
  form.lastName = auth.user.lastName
  form.phone = auth.user.phone || ''
  await loadLogs()
})

async function loadLogs() {
  const { data } = await http.get('/me/consent-logs')
  logs.value = data
}

async function save() {
  await run(async () => {
    await http.put('/me', form)
    await auth.fetchProfile()
    await loadLogs()
  }, 'Profil mis à jour.')
}

async function exportData() {
  await run(async () => {
    const { data } = await http.get('/me/export')
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'eventflow-export.json'
    link.click()
    URL.revokeObjectURL(url)
    await loadLogs()
  }, 'Export généré.')
}

async function anonymize() {
  if (!confirm('Confirmer l’anonymisation définitive du compte ?')) return
  await run(() => http.delete('/me'), 'Compte anonymisé.')
  auth.logout()
  router.push('/register')
}

function actionLabel(action) {
  const labels = {
    consent_given: 'Consentement donné',
    consent_withdrawn: 'Consentement retiré',
    data_accessed: 'Données consultées',
    data_deleted: 'Compte anonymisé',
  }

  return labels[action] || action
}
</script>
