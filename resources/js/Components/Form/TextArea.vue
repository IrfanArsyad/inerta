<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  rows: { type: Number, default: 3 },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  id: { type: String, default: () => `textarea-${Math.random().toString(36).substr(2, 9)}` },
})

const emit = defineEmits(['update:modelValue'])

const textareaClasses = computed(() => ['form-control', props.error ? 'is-invalid' : ''].filter(Boolean).join(' '))
</script>

<template>
  <div class="mb-3">
    <label v-if="label" :for="id" class="form-label">{{ label }} <span v-if="required" class="text-danger">*</span></label>
    <textarea :id="id" :value="modelValue" :placeholder="placeholder" :rows="rows" :disabled="disabled" :class="textareaClasses" @input="emit('update:modelValue', $event.target.value)"></textarea>
    <div v-if="error" class="invalid-feedback">{{ error }}</div>
  </div>
</template>
