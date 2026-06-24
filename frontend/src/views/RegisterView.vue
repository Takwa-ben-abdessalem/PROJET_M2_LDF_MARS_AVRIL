<template>
  <section class="auth-panel">
    <p class="kicker">Créer un accès</p>
    <h1>Inscription</h1>
    <p class="muted">Créez votre compte utilisateur ou activez le rôle organisateur dès maintenant.</p>
    <form @submit.prevent="submit">
      <BaseInput v-model="form.firstName" label="Prénom" required />
      <BaseInput v-model="form.lastName" label="Nom" required />
      <BaseInput v-model="form.email" label="Email" type="email" required />
      <BaseInput v-model="form.phone" label="Téléphone" />
      <BaseInput v-model="form.password" label="Mot de passe" type="password" required />
      <BaseInput
        v-if="isOrganizer"
        v-model="form.organizerInviteCode"
        label="Code organisateur"
        type="password"
        required
      />
      <label class="check-line">
        <input v-model="isOrganizer" type="checkbox" />
        Je crée aussi des événements
      </label>
      <label class="check-line">
        <input v-model="form.consentAccepted" type="checkbox" required />
        J'accepte le traitement de mes données selon la politique de confidentialité.
      </label>
      <p v-if="error" class="alert error">{{ error }}</p>
      <button class="button" :disabled="loading" type="submit">Créer mon compte</button>
    </form>
    <p class="auth-switch">
      Déjà un compte ?
      <RouterLink to="/login">Se connecter</RouterLink>
    </p>
  </section>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '../components/BaseInput.vue'
import { useAuthStore } from '../stores/auth'
import { useRequest } from '../composables/useRequest'

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  password: '',
  organizerInviteCode: '',
  consentAccepted: false,
  consentVersion: 'v1.0',
})
const isOrganizer = ref(false)
const auth = useAuthStore()
const router = useRouter()
const { loading, error, run } = useRequest()

async function submit() {
  await run(() => auth.register({
    ...form,
    organizerInviteCode: isOrganizer.value ? form.organizerInviteCode : null,
    roles: isOrganizer.value ? ['ROLE_ORGANIZER'] : ['ROLE_USER'],
  }))
  router.push('/dashboard')
}
</script>
