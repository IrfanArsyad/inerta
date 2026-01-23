import { ref } from 'vue'

export function useConfirm() {
  const isOpen = ref(false)
  const config = ref({
    title: 'Konfirmasi',
    message: 'Apakah Anda yakin?',
    confirmText: 'Ya, Lanjutkan',
    cancelText: 'Batal',
    variant: 'danger',
    onConfirm: () => {},
  })

  const confirm = (options = {}) => {
    return new Promise((resolve) => {
      config.value = {
        ...config.value,
        ...options,
        onConfirm: () => {
          isOpen.value = false
          resolve(true)
        },
      }
      isOpen.value = true
    })
  }

  const close = () => {
    isOpen.value = false
  }

  return {
    isOpen,
    config,
    confirm,
    close,
  }
}
