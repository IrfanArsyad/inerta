<script setup>
import { inject } from 'vue'
import Modal from './Modal.vue'

const confirmState = inject('confirmState', null)
const confirmResolve = inject('confirmResolve', null)

const handleConfirm = () => {
  if (confirmResolve?.value) {
    confirmResolve.value(true)
    confirmResolve.value = null
  }
  if (confirmState) {
    confirmState.show = false
  }
}

const handleCancel = () => {
  if (confirmResolve?.value) {
    confirmResolve.value(false)
    confirmResolve.value = null
  }
  if (confirmState) {
    confirmState.show = false
  }
}

const iconClass = {
  danger: 'bg-danger',
  warning: 'bg-warning',
  info: 'bg-info',
  success: 'bg-success',
}

const btnClass = {
  danger: 'btn-danger',
  warning: 'btn-warning',
  info: 'btn-info',
  success: 'btn-success',
}
</script>

<template>
  <Modal
    v-if="confirmState"
    :show="confirmState.show"
    size="sm"
    centered
    @close="handleCancel"
  >
    <div class="text-center">
      <!-- Icon -->
      <div class="mt-2">
        <lord-icon
          v-if="confirmState.variant === 'danger'"
          src="https://cdn.lordicon.com/gsqxdxog.json"
          trigger="loop"
          colors="primary:#f7b84b,secondary:#f06548"
          style="width: 100px; height: 100px;"
        ></lord-icon>
        <lord-icon
          v-else-if="confirmState.variant === 'warning'"
          src="https://cdn.lordicon.com/ygrdtqxm.json"
          trigger="loop"
          colors="primary:#f7b84b,secondary:#f06548"
          style="width: 100px; height: 100px;"
        ></lord-icon>
        <lord-icon
          v-else
          src="https://cdn.lordicon.com/lupuorrc.json"
          trigger="loop"
          colors="primary:#0ab39c,secondary:#405189"
          style="width: 100px; height: 100px;"
        ></lord-icon>
      </div>

      <!-- Title -->
      <h4 class="mt-3 mb-2">{{ confirmState.title || 'Konfirmasi' }}</h4>

      <!-- Message -->
      <p class="text-muted mb-0 fs-14">{{ confirmState.message || 'Apakah Anda yakin?' }}</p>
    </div>

    <template #footer>
      <div class="d-flex justify-content-center gap-2 w-100">
        <button
          type="button"
          class="btn btn-light"
          @click="handleCancel"
        >
          {{ confirmState.cancelText || 'Batal' }}
        </button>
        <button
          type="button"
          class="btn"
          :class="btnClass[confirmState.variant] || 'btn-primary'"
          @click="handleConfirm"
        >
          {{ confirmState.confirmText || 'Ya, Lanjutkan' }}
        </button>
      </div>
    </template>
  </Modal>
</template>
