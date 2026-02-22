<script setup>
import { ref, watch, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { debounce } from 'lodash-es'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import { usePermission } from '@/Composables/usePermission'
import { useConfirm } from '@/Composables/useConfirm'

const props = defineProps({
  roles: Object,
  modules: Array,
  filters: Object,
  can: Object,
  moduleId: Number,
})

const { canCreate, canUpdate, canDelete } = usePermission(props.moduleId)
const { confirmDelete } = useConfirm()

// Modal State
const showModal = ref(false)
const editingRole = ref(null)
const isEditing = computed(() => editingRole.value !== null)

// Permission Actions
const actions = ['read', 'create', 'update', 'delete']
const actionLabels = { read: 'Read', create: 'Create', update: 'Update', delete: 'Delete' }

const form = useForm({
  name: '',
  display_name: '',
  description: '',
  active: true,
  permissions: {},
})

// Convert role permissions to form format { moduleId: [actions] }
const convertPermissions = (permissions) => {
  const result = {}
  if (!permissions) return result
  for (const action of actions) {
    const moduleIds = permissions[action] || []
    for (const moduleId of moduleIds) {
      if (!result[moduleId]) result[moduleId] = []
      if (!result[moduleId].includes(action)) result[moduleId].push(action)
    }
  }
  return result
}

// Open modal for create
const openCreateModal = () => {
  editingRole.value = null
  form.reset()
  form.clearErrors()
  form.permissions = {}
  showModal.value = true
}

// Open modal for edit
const openEditModal = (role) => {
  editingRole.value = role
  form.name = role.name
  form.display_name = role.display_name || ''
  form.description = role.description || ''
  form.active = role.active
  form.permissions = convertPermissions(role.permissions)
  form.clearErrors()
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  editingRole.value = null
  form.reset()
  form.clearErrors()
}

// Submit form
const submitForm = () => {
  if (isEditing.value) {
    form.put(route('roles.update', editingRole.value.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('roles.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

// Permission helpers
const togglePermission = (moduleId, action) => {
  if (!form.permissions[moduleId]) form.permissions[moduleId] = []
  const index = form.permissions[moduleId].indexOf(action)
  if (index === -1) form.permissions[moduleId].push(action)
  else form.permissions[moduleId].splice(index, 1)
}

const hasPermission = (moduleId, action) => form.permissions[moduleId]?.includes(action) || false

const toggleAllForModule = (moduleId) => {
  if (!form.permissions[moduleId]) form.permissions[moduleId] = []
  form.permissions[moduleId] = form.permissions[moduleId].length === actions.length ? [] : [...actions]
}

const isAllCheckedForModule = (moduleId) => form.permissions[moduleId]?.length === actions.length

// Full Access - all permissions for all modules
const isFullAccess = computed(() => {
  if (!props.modules?.length) return false
  return props.modules.every(m => form.permissions[m.id]?.length === actions.length)
})

const toggleFullAccess = () => {
  if (isFullAccess.value) {
    // Uncheck all
    form.permissions = {}
  } else {
    // Check all
    const newPermissions = {}
    props.modules.forEach(m => {
      newPermissions[m.id] = [...actions]
    })
    form.permissions = newPermissions
  }
}

const isSuperadmin = computed(() => editingRole.value?.name === 'superadmin')

// Filters State
const search = ref(props.filters?.search || '')

// Table Columns
const columns = [
  { key: 'name', label: 'Role Name', sortable: true },
  { key: 'display_name', label: 'Display Name' },
  { key: 'users_count', label: 'Users' },
  { key: 'active', label: 'Status' },
]

// Apply Filters
const applyFilters = debounce(() => {
  router.get(route('roles.index'), {
    search: search.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch(search, applyFilters)

// Delete Role
const deleteRole = async (role) => {
  if (role.name === 'superadmin') return
  const confirmed = await confirmDelete(`Delete role "${role.name}"?`)
  if (confirmed) {
    router.delete(route('roles.destroy', role.id))
  }
}
</script>

<template>
  <Head title="Role Management" />

  <AppLayout>
    <!-- Page Header -->
    <c-page-header
      title="Role Management"
      :breadcrumbs="[
        { label: 'Role & Permission', href: route('roles.index') },
        { label: 'Role List' },
      ]"
    />

    <!-- Filters -->
    <c-table-filters
      v-model:search="search"
      search-placeholder="Search role name..."
      :can-create="canCreate"
      create-label="Add Role"
      @create="openCreateModal"
    />

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Role List</h5>
      </div>
      <div class="card-body">
        <c-data-table
          :columns="columns"
          :data="roles.data"
          :pagination="roles.meta"
          :can-view="true"
          :can-edit="canUpdate"
          :can-delete="canDelete"
          view-route="roles.show"
          @edit="openEditModal"
          @delete="deleteRole"
        >
          <!-- Custom Cell: Name -->
          <template #cell-name="{ item }">
            <h6 class="mb-0 fs-14">{{ item.name }}</h6>
          </template>

          <!-- Custom Cell: Display Name -->
          <template #cell-display_name="{ item }">
            <span class="text-muted">{{ item.display_name || '-' }}</span>
          </template>

          <!-- Custom Cell: Users Count -->
          <template #cell-users_count="{ item }">
            <span class="badge bg-info-subtle text-info">{{ item.users_count }} users</span>
          </template>

          <!-- Custom Cell: Status -->
          <template #cell-active="{ item }">
            <span
              class="badge"
              :class="item.active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
            >
              {{ item.active ? 'Active' : 'Inactive' }}
            </span>
          </template>

          <!-- Custom Delete Action (prevent delete superadmin) -->
          <template #action-delete="{ item }">
            <button
              v-if="canDelete && item.name !== 'superadmin'"
              type="button"
              class="btn btn-sm btn-soft-danger"
              title="Delete"
              @click="deleteRole(item)"
            >
              <i class="ri-delete-bin-fill align-bottom"></i>
            </button>
          </template>

          <!-- Empty State -->
          <template #empty>
            <div class="text-center py-4">
              <lord-icon
                src="https://cdn.lordicon.com/msoeawqm.json"
                trigger="loop"
                colors="primary:#405189,secondary:#0ab39c"
                style="width: 72px; height: 72px;"
              ></lord-icon>
              <h5 class="mt-4">No roles found</h5>
              <p class="text-muted mb-0">No roles registered yet.</p>
            </div>
          </template>
        </c-data-table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal
      :show="showModal"
      :title="isEditing ? 'Edit Role' : 'Add Role'"
      size="xl"
      centered
      scrollable
      @close="closeModal"
    >
      <form @submit.prevent="submitForm">
        <!-- Role Info -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <c-input
              id="name"
              v-model="form.name"
              label="Role Name (slug)"
              placeholder="Enter role name"
              :error="form.errors.name"
              :disabled="isSuperadmin"
              required
            />
            <small v-if="isSuperadmin" class="text-muted">Superadmin role name cannot be changed</small>
          </div>
          <div class="col-md-6">
            <c-input
              id="display_name"
              v-model="form.display_name"
              label="Display Name"
              placeholder="Enter display name"
              :error="form.errors.display_name"
            />
          </div>
          <div class="col-md-12">
            <c-textarea
              id="description"
              v-model="form.description"
              label="Description"
              placeholder="Enter role description"
              :error="form.errors.description"
              :rows="2"
            />
          </div>
          <div class="col-md-6">
            <label class="form-label d-block">Status</label>
            <c-checkbox id="active" v-model="form.active" label="Active" is-switch />
          </div>
        </div>

        <!-- Permission Matrix -->
        <div class="border-top pt-3">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0">Module Permissions</h6>
            <div v-if="!isSuperadmin" class="form-check form-switch">
              <input
                id="fullAccess"
                type="checkbox"
                class="form-check-input"
                role="switch"
                :checked="isFullAccess"
                @change="toggleFullAccess"
              >
              <label class="form-check-label fw-medium text-primary" for="fullAccess">
                Full Access (All Modules)
              </label>
            </div>
          </div>
          <div v-if="isSuperadmin" class="alert alert-warning mb-3">
            Superadmin has full access to all modules and cannot be modified.
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Module</th>
                  <th class="text-center" style="width: 80px;">All</th>
                  <th v-for="action in actions" :key="action" class="text-center" style="width: 80px;">
                    {{ actionLabels[action] }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="module in modules" :key="module.id">
                  <td>
                    <div class="fw-medium">{{ module.label }}</div>
                    <small class="text-muted">{{ module.name }}</small>
                  </td>
                  <td class="text-center">
                    <input
                      type="checkbox"
                      class="form-check-input"
                      :checked="isSuperadmin || isAllCheckedForModule(module.id)"
                      :disabled="isSuperadmin"
                      @change="toggleAllForModule(module.id)"
                    >
                  </td>
                  <td v-for="action in actions" :key="action" class="text-center">
                    <input
                      type="checkbox"
                      class="form-check-input"
                      :checked="isSuperadmin || hasPermission(module.id, action)"
                      :disabled="isSuperadmin"
                      @change="togglePermission(module.id, action)"
                    >
                  </td>
                </tr>
                <tr v-if="!modules.length">
                  <td colspan="6" class="text-center text-muted py-4">No modules available</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4 pt-2 border-top">
          <button type="button" class="btn btn-light" @click="closeModal">Cancel</button>
          <c-button v-if="!isSuperadmin" type="submit" variant="primary" :loading="form.processing">
            {{ isEditing ? 'Update' : 'Save' }}
          </c-button>
        </div>
      </form>
    </Modal>

    <c-confirm-dialog />
  </AppLayout>
</template>
