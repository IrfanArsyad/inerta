import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermission(moduleId = null) {
  const page = usePage()

  const permissions = computed(() => page.props.auth?.permissions || {})
  const currentModuleId = computed(() => moduleId ?? page.props.moduleId)

  const hasPermission = (action, modId = null) => {
    const id = modId ?? currentModuleId.value
    const allowed = permissions.value[action] || []

    if (allowed.includes('*')) return true
    return allowed.includes(id)
  }

  const canRead = computed(() => hasPermission('read'))
  const canCreate = computed(() => hasPermission('create'))
  const canUpdate = computed(() => hasPermission('update'))
  const canDelete = computed(() => hasPermission('delete'))

  return {
    permissions,
    hasPermission,
    canRead,
    canCreate,
    canUpdate,
    canDelete,
  }
}
