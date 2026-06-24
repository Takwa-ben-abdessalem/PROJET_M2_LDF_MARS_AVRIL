<template>
  <article class="event-card">
    <div>
      <div class="event-meta">
        <span class="meta-chip">📅 {{ formattedDate }}</span>
        <span class="meta-chip">📍 {{ event.location }}</span>
      </div>
      <h2>{{ event.title }}</h2>
      <p v-if="event.description" class="event-card-description">{{ event.description }}</p>
      <div class="capacity-row">
        <span class="capacity">{{ event.registeredCount }}/{{ event.maxParticipants }} participants</span>
        <span class="muted">{{ capacityPercent }}%</span>
      </div>
      <div class="capacity-bar" aria-hidden="true">
        <span :style="{ width: `${capacityPercent}%` }"></span>
      </div>
    </div>
    <RouterLink class="button ghost" :to="`/events/${event.id}`">Voir le détail</RouterLink>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  event: { type: Object, required: true },
})

const formattedDate = computed(() => new Intl.DateTimeFormat('fr-FR', {
  dateStyle: 'medium',
  timeStyle: 'short',
}).format(new Date(props.event.eventDate)))

const capacityPercent = computed(() => {
  if (!props.event.maxParticipants) return 0
  return Math.min(100, Math.round((props.event.registeredCount / props.event.maxParticipants) * 100))
})
</script>
