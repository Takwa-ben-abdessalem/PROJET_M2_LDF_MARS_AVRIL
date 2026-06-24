<template>
  <section class="home-welcome">
    <div class="welcome-copy">
      <span class="eyebrow">Plateforme de gestion d'evenements</span>
      <h1>Bienvenue sur EventFlow</h1>
      <p class="home-lead">
        Creez, publiez et suivez vos evenements depuis une interface simple, avec les inscriptions et les actions RGPD au meme endroit.
      </p>

      <div v-if="!auth.isAuthenticated" class="actions">
        <RouterLink class="button" to="/register">Creer un compte</RouterLink>
        <RouterLink class="button ghost" to="/login">Se connecter</RouterLink>
        <RouterLink class="button soft" to="/privacy">Confidentialite</RouterLink>
      </div>
      <div v-else class="actions">
        <RouterLink class="button" to="/dashboard">Ouvrir mon espace</RouterLink>
        <RouterLink class="button ghost" to="/events">Voir les evenements</RouterLink>
        <RouterLink v-if="auth.isOrganizer" class="button soft" to="/events/create">Nouvel evenement</RouterLink>
      </div>
    </div>

    <aside class="welcome-panel" aria-label="Resume EventFlow">
      <div class="welcome-panel-header">
        <span class="status-dot"></span>
        <strong>EventFlow</strong>
      </div>

      <div class="welcome-summary">
        <div>
          <span>Acces public</span>
          <strong>Evenements publies</strong>
        </div>
        <div>
          <span>Espace connecte</span>
          <strong>Inscriptions et profil</strong>
        </div>
        <div>
          <span>Organisateur</span>
          <strong>Brouillons, publication, suppression</strong>
        </div>
      </div>
    </aside>
  </section>

  <section class="home-workflow" aria-label="Fonctionnement EventFlow">
    <article>
      <span>01</span>
      <h2>Creer un compte</h2>
      <p>Le consentement RGPD est demande clairement a l'inscription.</p>
    </article>
    <article>
      <span>02</span>
      <h2>Participer</h2>
      <p>Un utilisateur connecte peut s'inscrire puis annuler son inscription.</p>
    </article>
    <article>
      <span>03</span>
      <h2>Organiser</h2>
      <p>Un organisateur gere ses evenements publies et ses brouillons.</p>
    </article>
    <article>
      <span>04</span>
      <h2>Controler ses donnees</h2>
      <p>Le profil permet de consulter, modifier, exporter ou anonymiser les donnees.</p>
    </article>
  </section>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>
