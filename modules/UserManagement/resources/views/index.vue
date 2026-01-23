<script setup>
import { ref, watch, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import { usePermission } from '@/Composables/usePermission'
import { useConfirm } from '@/Composables/useConfirm'

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object,
  can: Object,
  moduleId: Number,
})

const { canCreate, canUpdate, canDelete } = usePermission(props.moduleId)
const { confirmDelete } = useConfirm()

// Modal State
const showModal = ref(false)
const editingUser = ref(null)
const isEditing = computed(() => editingUser.value !== null)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_active: true,
  role_id: '',
})

// Open modal for create
const openCreateModal = () => {
  editingUser.value = null
  form.reset()
  form.clearErrors()
  showModal.value = true
}

// Open modal for edit
const openEditModal = (user) => {
  editingUser.value = user
  form.name = user.name
  form.email = user.email
  form.password = ''
  form.password_confirmation = ''
  form.is_active = user.is_active
  form.role_id = user.role_id || ''
  form.clearErrors()
  showModal.value = true
}

// Close modal
const closeModal = () => {
  showModal.value = false
  editingUser.value = null
  form.reset()
  form.clearErrors()
}

// Submit form
const submitForm = () => {
  if (isEditing.value) {
    form.put(route('users.update', editingUser.value.id), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  } else {
    form.post(route('users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    })
  }
}

// Filters State
const search = ref(props.filters?.search || '')
const filterValues = ref({
  is_active: props.filters?.is_active || '',
})

// Table Columns
const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role', label: 'Role' },
  { key: 'is_active', label: 'Status' },
  { key: 'last_login_at', label: 'Last Login' },
]

// Filter Options
const statusFilters = [
  {
    key: 'is_active',
    type: 'select',
    options: [
      { value: '', label: 'All Status' },
      { value: '1', label: 'Active' },
      { value: '0', label: 'Inactive' },
    ],
  },
]

// Apply Filters
const applyFilters = debounce(() => {
  router.get(route('users.index'), {
    search: search.value || undefined,
    is_active: filterValues.value.is_active || undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch([search, filterValues], applyFilters, { deep: true })

// Delete User
const deleteUser = async (user) => {
  const confirmed = await confirmDelete(`Delete user "${user.name}"?`)
  if (confirmed) {
    router.delete(route('users.destroy', user.id))
  }
}
</script>

<template>
  <Head title="User Management" />

  <AppLayout>
    <!-- Page Header -->
    <c-page-header
      title="User Management"
      :breadcrumbs="[
        { label: 'User Management', href: route('users.index') },
        { label: 'User List' },
      ]"
    />

    <!-- Filters -->
    <c-table-filters
      v-model:search="search"
      v-model="filterValues"
      search-placeholder="Search name or email..."
      :filters="statusFilters"
      :can-create="canCreate"
      create-label="Add User"
      @create="openCreateModal"
    />

    <!-- Table Card -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">User List</h5>
      </div>
      <div class="card-body">
        <c-data-table
          :columns="columns"
          :data="users.data"
          :pagination="users.meta"
          :can-view="true"
          :can-edit="canUpdate"
          :can-delete="canDelete"
          view-route="users.show"
          @edit="openEditModal"
          @delete="deleteUser"
        >
          <!-- Custom Cell: Name with Avatar -->
          <template #cell-name="{ item }">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <div class="avatar-xs">
                  <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-13">
                    {{ item.name.charAt(0).toUpperCase() }}
                  </div>
                </div>
              </div>
              <div class="flex-grow-1 ms-2">
                <h6 class="mb-0 fs-14">{{ item.name }}</h6>
              </div>
            </div>
          </template>

          <!-- Custom Cell: Role -->
          <template #cell-role="{ item }">
            <span v-if="item.role" class="badge bg-primary-subtle text-primary">{{ item.role.display_name || item.role.name }}</span>
            <span v-else class="text-muted">-</span>
          </template>

          <!-- Custom Cell: Status -->
          <template #cell-is_active="{ item }">
            <span
              class="badge"
              :class="item.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'"
            >
              {{ item.is_active ? 'Active' : 'Inactive' }}
            </span>
          </template>

          <!-- Custom Cell: Last Login -->
          <template #cell-last_login_at="{ item }">
            <span class="text-muted">{{ item.last_login_at || '-' }}</span>
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
              <h5 class="mt-4">No users found</h5>
              <p class="text-muted mb-0">No users registered yet.</p>
            </div>
          </template>
        </c-data-table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal
      :show="showModal"
      :title="isEditing ? 'Edit User' : 'Add User'"
      size="lg"
      centered
      @close="closeModal"
    >
      <form @submit.prevent="submitForm">
        <div class="row g-3">
          <div class="col-md-6">
            <c-input
              id="name"
              v-model="form.name"
              label="Full Name"
              placeholder="Enter full name"
              :error="form.errors.name"
              required
            />
          </div>
          <div class="col-md-6">
            <c-input
              id="email"
              v-model="form.email"
              type="email"
              label="Email"
              placeholder="Enter email address"
              :error="form.errors.email"
              required
            />
          </div>
          <div class="col-md-6">
            <c-input
              id="password"
              v-model="form.password"
              label="Password"
              :placeholder="isEditing ? 'Leave blank to keep current' : 'Enter password'"
              :error="form.errors.password"
              password-toggle
              :required="!isEditing"
            />
          </div>
          <div class="col-md-6">
            <c-input
              id="password_confirmation"
              v-model="form.password_confirmation"
              label="Confirm Password"
              :placeholder="isEditing ? 'Leave blank to keep current' : 'Repeat password'"
              password-toggle
              :required="!isEditing"
            />
          </div>
          <div class="col-md-6">
            <c-select
              id="role_id"
              v-model="form.role_id"
              label="Role"
              placeholder="Select Role"
              :options="roles"
              value-key="id"
              label-key="display_name"
              :error="form.errors.role_id"
              required
            />
          </div>
          <div class="col-md-6">
            <label class="form-label d-block">Status</label>
            <c-checkbox id="is_active" v-model="form.is_active" label="Active" is-switch />
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4 pt-2 border-top">
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
