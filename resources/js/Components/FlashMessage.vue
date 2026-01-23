<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  flash: { type: Object, default: () => ({}) },
})

const visible = ref(false)
const message = ref('')
const type = ref('success')

const show = () => {
  visible.value = true
  setTimeout(() => { visible.value = false }, 5000)
}

const close = () => { visible.value = false }

watch(() => props.flash, (newFlash) => {
  if (newFlash?.success) {
    message.value = newFlash.success
    type.value = 'success'
    show()
  } else if (newFlash?.error) {
    message.value = newFlash.error
    type.value = 'error'
    show()
  }
}, { immediate: true })
</script>

<template>
  <Teleport to="body">
    <Transition name="toast">
      <div v-if="visible && message" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show" :class="type === 'success' ? 'bg-success-subtle' : 'bg-danger-subtle'" role="alert">
          <div class="toast-header" :class="type === 'success' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
            <i :class="type === 'success' ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'" class="me-2 fs-5"></i>
            <strong class="me-auto">{{ type === 'success' ? 'Berhasil' : 'Error' }}</strong>
            <button type="button" class="btn-close" @click="close"></button>
          </div>
          <div class="toast-body">{{ message }}</div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>
