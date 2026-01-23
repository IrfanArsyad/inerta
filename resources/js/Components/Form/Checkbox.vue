<script setup>
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: '' },
  error: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  isSwitch: { type: Boolean, default: false },
  id: { type: String, default: () => `checkbox-${Math.random().toString(36).substr(2, 9)}` },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="mb-3">
    <div class="form-check" :class="{ 'form-switch': isSwitch }">
      <input :id="id" type="checkbox" class="form-check-input" :class="{ 'is-invalid': error }" :checked="modelValue" :disabled="disabled" @change="emit('update:modelValue', $event.target.checked)">
      <label v-if="label" :for="id" class="form-check-label">{{ label }}</label>
    </div>
    <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
  </div>
</template>
