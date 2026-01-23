<script setup>
import { ref, watch, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import { usePermission } from '@/Composables/usePermission'
import { useConfirm } from '@/Composables/useConfirm'

const props = defineProps({
  modules: Object,
  filters: Object,
  groups: Array,
  parents: Array,
  can: Object,
  moduleId: Number,
})

const { canCreate, canUpdate, canDelete } = usePermission(props.moduleId)
const { confirmDelete } = useConfirm()

// Modal State
const showModal = ref(false)
const editingModule = ref(null)
const isEditing = computed(() => editingModule.value !== null)

const form = useForm({
  name: '',
  alias: '',
  label: '',
  icon: '',
  url: '',
  route_name: '',
  permission: '',
  badge_source: '',
  parent_id: '',
  module_group_id: '',
  order: 0,
  active: true,
  external: false,
})

// Open modal for create
const openCreateModal = () => {
  editingModule.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

// Open modal for edit
const openEditModal = (module) => {
  editingModule.value = module
  form.name = module.name || ''
  form.alias = module.alias || ''
  form.label = module.label || ''
  form.icon = module.icon || ''
  form.url = module.url || ''
  form.route_name = module.route_name || ''
  form.permission = module.permission || ''
  form.badge_source = module.badge_source || ''
  form.parent_id = module.parent_id || ''
  form.module_group_id = module.module_group_id || ''
  form.order = module.order || 0
  form.active = module.active ?? true
  form.external = module.external ?? false
  form.clearErrors()
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  editingModule.value = null
  form.reset()
  form.clearErrors()
}

// Submit form
const submitForm = () => {
  if (isEditing.value) {
    form.put(route('modules.update', editingModule.value.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('modules.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

// Filters State
const search = ref(props.filters?.search || '')
const filterValues = ref({
  active: props.filters?.active || '',
  group_id: props.filters?.group_id || '',
})

// Table Columns
const columns = [
  { key: 'name', label: 'Module Name', sortable: true },
  { key: 'label', label: 'Label' },
  { key: 'group', label: 'Group' },
  { key: 'route_name', label: 'Route' },
  { key: 'order', label: 'Order' },
  { key: 'active', label: 'Status' },
]

// Group options for filter
const groupOptions = computed(() => [
  { value: '', label: 'All Groups' },
  ...(props.groups || []).map(g => ({ value: String(g.id), label: g.label || g.name })),
])

// Filter Options
const statusFilters = [
  {
    key: 'active',
    type: 'select',
    options: [
      { value: '', label: 'All Status' },
      { value: '1', label: 'Active' },
      { value: '0', label: 'Inactive' },
    ],
  },
  {
    key: 'group_id',
    type: 'select',
    options: groupOptions.value,
  },
]

// Apply Filters
const applyFilters = debounce(() => {
  router.get(route('modules.index'), {
    search: search.value || undefined,
    active: filterValues.value.active || undefined,
    group_id: filterValues.value.group_id || undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch([search, filterValues], applyFilters, { deep: true })

// Delete Module
const deleteModule = async (module) => {
  const confirmed = await confirmDelete(`Delete module "${module.label}"?`)
  if (confirmed) {
    router.delete(route('modules.destroy', module.id))
  }
}
</script>

<template>
  <Head title="Module Management" />

  <AppLayout>
    <!-- Page Header -->
    <c-page-header
      title="Module Management"
      :breadcrumbs="[
        { label: 'Settings', href: route('modules.index') },
        { label: 'Module Management' },
      ]"
    />

    <!-- Filters -->
    <c-table-filters
      v-model:search="search"
      v-model="filterValues"
      search-placeholder="Search module name or label..."
      :filters="statusFilters"
      :can-create="canCreate"
      create-label="Add Module"
      @create="openCreateModal"
    />

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Module List</h5>
      </div>
      <div class="card-body">
        <c-data-table
          :columns="columns"
          :data="modules.data"
          :pagination="modules.meta"
          :can-view="true"
          :can-edit="canUpdate"
          :can-delete="canDelete"
          view-route="modules.show"
          @edit="openEditModal"
          @delete="deleteModule"
        >
          <!-- Custom Cell: Name with Icon -->
          <template #cell-name="{ item }">
            <div class="d-flex align-items-center">
              <i v-if="item.icon" :class="item.icon" class="me-2 fs-5 text-primary"></i>
              <div class="flex-shrink-0" v-else>
                <div class="avatar-xs">
                  <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-13">
                    <i class="ri-apps-line"></i>
                  </div>
                </div>
              </div>
              <div class="flex-grow-1 ms-2">
                <h6 class="mb-0 fs-14">{{ item.name }}</h6>
                <small v-if="item.alias" class="text-muted">{{ item.alias }}</small>
              </div>
            </div>
          </template>

          <!-- Custom Cell: Group -->
          <template #cell-group="{ item }">
            <span v-if="item.group" class="badge bg-info-subtle text-info">
              {{ item.group.label || item.group.name }}
            </span>
            <span v-else class="text-muted">-</span>
          </template>

          <!-- Custom Cell: Route -->
          <template #cell-route_name="{ item }">
            <code v-if="item.route_name" class="fs-12">{{ item.route_name }}</code>
            <span v-else class="text-muted">-</span>
          </template>

          <!-- Custom Cell: Order -->
          <template #cell-order="{ item }">
            <span class="badge bg-secondary-subtle text-secondary">{{ item.order }}</span>
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

          <!-- Empty State -->
          <template #empty>
            <div class="text-center py-4">
              <lord-icon
                src="https://cdn.lordicon.com/msoeawqm.json"
                trigger="loop"
                colors="primary:#405189,secondary:#0ab39c"
                style="width: 72px; height: 72px;"
              ></lord-icon>
              <h5 class="mt-4">No modules found</h5>
              <p class="text-muted mb-0">No modules registered yet.</p>
            </div>
          </template>
        </c-data-table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal
      :show="showModal"
      :title="isEditing ? 'Edit Module' : 'Add Module'"
      size="xl"
      centered
      @close="closeModal"
    >
      <form @submit.prevent="submitForm">
        <!-- Basic Information -->
        <div class="mb-4">
          <h6 class="text-muted mb-3">Basic Information</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <c-input
                id="name"
                v-model="form.name"
                label="Module Name"
                placeholder="e.g. UserManagement"
                :error="form.errors.name"
                required
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="label"
                v-model="form.label"
                label="Display Label"
                placeholder="e.g. User Management"
                :error="form.errors.label"
                required
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="alias"
                v-model="form.alias"
                label="Alias"
                placeholder="e.g. usermanagement"
                :error="form.errors.alias"
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="icon"
                v-model="form.icon"
                label="Icon Class"
                placeholder="e.g. ri-user-line"
                :error="form.errors.icon"
              />
            </div>
          </div>
        </div>

        <!-- Routing & Permission -->
        <div class="mb-4">
          <h6 class="text-muted mb-3">Routing & Permission</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <c-input
                id="route_name"
                v-model="form.route_name"
                label="Route Name"
                placeholder="e.g. users.index"
                :error="form.errors.route_name"
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="url"
                v-model="form.url"
                label="URL (for external links)"
                placeholder="e.g. https://example.com"
                :error="form.errors.url"
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="permission"
                v-model="form.permission"
                label="Permission Key"
                placeholder="e.g. user-management"
                :error="form.errors.permission"
              />
            </div>
            <div class="col-md-6">
              <c-input
                id="badge_source"
                v-model="form.badge_source"
                label="Badge Source"
                placeholder="e.g. unread_notifications"
                :error="form.errors.badge_source"
              />
            </div>
          </div>
        </div>

        <!-- Hierarchy & Order -->
        <div class="mb-4">
          <h6 class="text-muted mb-3">Hierarchy & Order</h6>
          <div class="row g-3">
            <div class="col-md-4">
              <c-select
                id="module_group_id"
                v-model="form.module_group_id"
                label="Module Group"
                placeholder="Select Group"
                :options="groups"
                value-key="id"
                label-key="label"
                :error="form.errors.module_group_id"
              />
            </div>
            <div class="col-md-4">
              <c-select
                id="parent_id"
                v-model="form.parent_id"
                label="Parent Module"
                placeholder="No parent"
                :options="parents"
                value-key="id"
                label-key="label"
                :error="form.errors.parent_id"
              />
            </div>
            <div class="col-md-4">
              <c-input
                id="order"
                v-model="form.order"
                type="number"
                label="Display Order"
                placeholder="0"
                :error="form.errors.order"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label d-block">Status</label>
              <c-checkbox id="active" v-model="form.active" label="Module Active" is-switch />
            </div>
            <div class="col-md-6">
              <label class="form-label d-block">Link Type</label>
              <c-checkbox id="external" v-model="form.external" label="External Link" is-switch />
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-end pt-2 border-top">
          <button type="button" class="btn btn-light" @click="closeModal">Cancel</button>
          <c-button type="submit" variant="primary" :loading="form.processing">
            {{ isEditing ? 'Update' : 'Save' }}
          </c-button>
        </div>
      </form>
    </Modal>

    <c-confirm-dialog />
  </AppLayout>
</template>
