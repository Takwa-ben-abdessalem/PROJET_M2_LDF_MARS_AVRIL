<template>
  <section class="toolbar">
    <div>
      <p class="kicker">Espace utilisateur</p>
      <h1>Tableau de bord</h1>
      <p class="muted">Retrouvez vos événements, vos inscriptions et vos données personnelles.</p>
    </div>
    <div v-if="auth.isOrganizer" class="actions">
      <RouterLink class="button ghost" to="/dashboard/my-events">Mes événements</RouterLink>
      <RouterLink class="button" to="/events/create">Créer un événement</RouterLink>
    </div>
  </section>

  <section class="dashboard-grid">
    <article class="panel">
      <p class="kicker">Agenda</p>
      <h2>Événements</h2>
      <p>Consultez les événements disponibles et inscrivez-vous à ceux qui vous intéressent.</p>
      <div class="actions">
        <RouterLink class="button ghost" to="/events">Voir les événements</RouterLink>
        <RouterLink class="button ghost" to="/dashboard/registrations">Mes inscriptions</RouterLink>
      </div>
    </article>

    <article v-if="auth.isOrganizer" class="panel">
      <p class="kicker">Organisateur</p>
      <h2>Mes événements</h2>
      <p>Retrouvez vos brouillons et vos événements publiés au même endroit.</p>
      <RouterLink class="button ghost" to="/dashboard/my-events">Gérer mes événements</RouterLink>
    </article>

    <article class="panel">
      <p class="kicker">RGPD</p>
      <h2>Mes données</h2>
      <p>Consultez votre profil, modifiez vos informations et gérez vos actions RGPD.</p>
      <RouterLink class="button ghost" to="/profile">Ouvrir mon profil</RouterLink>
    </article>
  </section>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>
