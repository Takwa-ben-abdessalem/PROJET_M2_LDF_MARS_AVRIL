<template>
  <label class="field">
    <span>{{ label }}</span>
    <textarea v-if="type === 'textarea'" :value="modelValue" :required="required" @input="$emit('update:modelValue', $event.target.value)" />
    <span v-else-if="type === 'password'" class="password-control">
      <input
        :type="passwordVisible ? 'text' : 'password'"
        :value="modelValue"
        :required="required"
        :min="min"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <button
        class="password-toggle"
        type="button"
        :aria-label="passwordVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
        :title="passwordVisible ? 'Masquer' : 'Afficher'"
        @click="passwordVisible = !passwordVisible"
      >
        <span class="eye-icon" :class="{ hidden: passwordVisible }" aria-hidden="true"></span>
      </button>
    </span>
    <input v-else :type="type" :value="modelValue" :required="required" :min="min" @input="$emit('update:modelValue', $event.target.value)" />
  </label>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  label: { type: String, required: true },
  modelValue: { type: [String, Number], default: '' },
  type: { type: String, default: 'text' },
  required: { type: Boolean, default: false },
  min: { type: String, default: undefined },
})

defineEmits(['update:modelValue'])

const passwordVisible = ref(false)
</script>
