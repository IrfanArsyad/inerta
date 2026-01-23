import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useFlash() {
  const page = usePage()

  const flash = computed(() => page.props.flash || {})
  const success = computed(() => flash.value.success)
  const error = computed(() => flash.value.error)
  const hasMessage = computed(() => !!success.value || !!error.value)

  return {
    flash,
    success,
    error,
    hasMessage,
  }
}
