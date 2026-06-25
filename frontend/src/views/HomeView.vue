<template>
  <section class="home-illustrated">
    <div class="home-story">
      <span class="eyebrow">Organisation d'evenements</span>
      <h1>EventFlow</h1>
      <p>
        Creez vos evenements, suivez les inscriptions et gardez toutes les informations importantes au meme endroit.
      </p>
      <div class="hero-proof" aria-label="Fonctionnalites principales">
        <span>Evenements</span>
        <span>Inscriptions</span>
        <span>Profil</span>
        <span>Confidentialite</span>
      </div>

      <div v-if="!auth.isAuthenticated" class="actions">
        <RouterLink class="button" to="/register">Creer un compte</RouterLink>
        <RouterLink class="button ghost" to="/login">Se connecter</RouterLink>
      </div>
      <div v-else class="actions">
        <RouterLink class="button" to="/dashboard">Ouvrir mon espace</RouterLink>
        <RouterLink class="button ghost" to="/events">Voir les evenements</RouterLink>
        <RouterLink v-if="auth.isOrganizer" class="button soft" to="/events/create">Nouvel evenement</RouterLink>
      </div>
    </div>

    <div class="hero-illustration" aria-hidden="true">
      <div class="analytics-board">
        <div class="board-row">
          <div class="check-list">
            <span></span><span></span><i></i>
            <span></span><span></span><i></i>
            <span></span><span></span><i></i>
          </div>
          <div class="donut-card">
            <div class="donut"></div>
            <div class="chart-lines">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </div>
        </div>
        <div class="line-chart">
          <span></span>
        </div>
        <div class="mail-row">
          <div class="mail-icon"></div>
          <div class="mail-lines">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>

      <div class="calendar-card"></div>
      <div class="gear one"></div>
      <div class="target-ring"></div>
    </div>
  </section>

  <section class="home-modules" aria-label="Modules EventFlow">
    <article>
      <strong>Creer</strong>
      <p>Publication, brouillons, modification et suppression par organisateur.</p>
    </article>
    <article>
      <strong>Participer</strong>
      <p>Inscription utilisateur, suivi personnel et annulation possible.</p>
    </article>
    <article>
      <strong>Gerer ses donnees</strong>
      <p>Acces au profil, modification, export et anonymisation du compte.</p>
    </article>
    <article>
      <strong>Organiser</strong>
      <p>Espace dedie pour gerer uniquement ses propres evenements.</p>
    </article>
  </section>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>
