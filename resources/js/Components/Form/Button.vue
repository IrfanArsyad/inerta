<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})

const sizeClasses = { sm: 'btn-sm', md: '', lg: 'btn-lg' }

const buttonClasses = computed(() => ['btn', `btn-${props.variant}`, sizeClasses[props.size]].filter(Boolean).join(' '))
</script>

<template>
  <button :type="type" :disabled="disabled || loading" :class="buttonClasses">
    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
    <slot />
  </button>
</template>
