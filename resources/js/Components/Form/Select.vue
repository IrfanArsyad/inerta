<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number, null], default: '' },
  label: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Pilih...' },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  valueKey: { type: String, default: 'value' },
  labelKey: { type: String, default: 'label' },
  id: { type: String, default: () => `select-${Math.random().toString(36).substr(2, 9)}` },
})

const emit = defineEmits(['update:modelValue'])

const selectClasses = computed(() => ['form-select', props.error ? 'is-invalid' : ''].filter(Boolean).join(' '))
</script>

<template>
  <div class="mb-3">
    <label v-if="label" :for="id" class="form-label">{{ label }} <span v-if="required" class="text-danger">*</span></label>
    <select :id="id" :value="modelValue" :disabled="disabled" :class="selectClasses" @change="emit('update:modelValue', $event.target.value)">
      <option value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option[valueKey] ?? option" :value="option[valueKey] ?? option">{{ option[labelKey] ?? option }}</option>
    </select>
    <div v-if="error" class="invalid-feedback">{{ error }}</div>
  </div>
</template>
