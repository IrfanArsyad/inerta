<script setup>
import { watch, computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  size: { type: String, default: 'md', validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v) },
  centered: { type: Boolean, default: false },
  scrollable: { type: Boolean, default: false },
  staticBackdrop: { type: Boolean, default: false },
  title: { type: String, default: '' },
  closeable: { type: Boolean, default: true },
  hideHeader: { type: Boolean, default: false },
  hideFooter: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'confirm'])

const close = () => { if (props.closeable) emit('close') }
const handleBackdropClick = () => { if (!props.staticBackdrop) close() }

const modalSizeClass = computed(() => ({ sm: 'modal-sm', md: '', lg: 'modal-lg', xl: 'modal-xl' }[props.size]))

const dialogClass = computed(() => {
  const classes = ['modal-dialog']
  if (modalSizeClass.value) classes.push(modalSizeClass.value)
  if (props.centered) classes.push('modal-dialog-centered')
  if (props.scrollable) classes.push('modal-dialog-scrollable')
  return classes.join(' ')
})

watch(() => props.show, (show) => {
  if (show) {
    document.body.classList.add('modal-open')
    document.body.style.overflow = 'hidden'
  } else {
    document.body.classList.remove('modal-open')
    document.body.style.overflow = ''
  }
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-backdrop">
      <div v-if="show" class="modal-backdrop fade show" @click="handleBackdropClick"></div>
    </Transition>

    <Transition name="modal">
      <div v-if="show" class="modal fade show d-block" tabindex="-1" role="dialog" @click.self="handleBackdropClick">
        <div :class="dialogClass" role="document">
          <div class="modal-content">
            <div v-if="!hideHeader" class="modal-header">
              <slot name="header"><h5 class="modal-title">{{ title }}</h5></slot>
              <button v-if="closeable" type="button" class="btn-close" aria-label="Close" @click="close"></button>
            </div>
            <div class="modal-body"><slot /></div>
            <div v-if="!hideFooter && $slots.footer" class="modal-footer"><slot name="footer" /></div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-backdrop-enter-active, .modal-backdrop-leave-active { transition: opacity 0.15s ease; }
.modal-backdrop-enter-from, .modal-backdrop-leave-to { opacity: 0; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.15s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
