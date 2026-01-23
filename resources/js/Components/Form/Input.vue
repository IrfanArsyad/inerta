<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  type: { type: String, default: 'text' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  passwordToggle: { type: Boolean, default: false },
  id: { type: String, default: () => `input-${Math.random().toString(36).substr(2, 9)}` },
})

const emit = defineEmits(['update:modelValue'])

const showPassword = ref(false)
const inputType = computed(() => props.passwordToggle ? (showPassword.value ? 'text' : 'password') : props.type)
const inputClasses = computed(() => ['form-control', props.error ? 'is-invalid' : ''].filter(Boolean).join(' '))
</script>

<template>
  <div class="mb-3">
    <label v-if="label" :for="id" class="form-label">{{ label }} <span v-if="required" class="text-danger">*</span></label>
    <div v-if="passwordToggle" class="input-group">
      <input :id="id" :type="inputType" :value="modelValue" :placeholder="placeholder" :disabled="disabled" :class="inputClasses" @input="emit('update:modelValue', $event.target.value)">
      <button type="button" class="btn btn-outline-secondary" @click="showPassword = !showPassword"><i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"></i></button>
      <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </div>
    <template v-else>
      <input :id="id" :type="type" :value="modelValue" :placeholder="placeholder" :disabled="disabled" :class="inputClasses" @input="emit('update:modelValue', $event.target.value)">
      <div v-if="error" class="invalid-feedback">{{ error }}</div>
    </template>
  </div>
</template>
