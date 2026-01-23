<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermission } from '@/Composables/usePermission'

const props = defineProps({
  role: Object,
  modules: Array,
  can: Object,
  moduleId: Number,
})

const { canUpdate } = usePermission(props.moduleId)

const actions = ['read', 'create', 'update', 'delete']
const actionLabels = { read: 'Read', create: 'Create', update: 'Update', delete: 'Delete' }
const isSuperadmin = props.role.name === 'superadmin'

const hasPermission = (moduleId, action) => {
  const permissions = props.role.permissions || {}
  return permissions[action]?.includes(moduleId) || false
}
</script>

<template>
  <Head :title="`Role Detail - ${role.name}`" />

  <AppLayout>
    <c-page-header title="Role Detail" :breadcrumbs="[{ label: 'Role & Permission', href: route('roles.index') }, { label: 'Role Detail' }]">
      <template #actions>
        <c-link :href="route('roles.index')" class="btn btn-light me-2"><i class="ri-arrow-left-line me-1"></i> Back</c-link>
      </template>
    </c-page-header>

    <div class="card mb-3">
      <div class="card-header"><h5 class="card-title mb-0">Role Information</h5></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Role Name</label>
            <p class="fw-semibold mb-0">{{ role.name }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Display Name</label>
            <p class="mb-0">{{ role.display_name || '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Status</label>
            <div><span class="badge" :class="role.active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ role.active ? 'Active' : 'Inactive' }}</span></div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Description</label>
            <p class="mb-0">{{ role.description || '-' }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Created At</label>
            <p class="mb-0">{{ role.created_at }}</p>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted mb-1">Updated At</label>
            <p class="mb-0">{{ role.updated_at }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header"><h5 class="card-title mb-0">Module Permissions</h5></div>
      <div class="card-body">
        <div v-if="isSuperadmin" class="alert alert-success mb-3">Superadmin role has full access to all modules.</div>
        <div class="table-responsive">
          <table class="table table-bordered mb-0">
            <thead class="table-light">
              <tr>
                <th>Module</th>
                <th v-for="action in actions" :key="action" class="text-center" style="width: 80px;">{{ actionLabels[action] }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="module in modules" :key="module.id">
                <td class="fw-medium">{{ module.label }}</td>
                <td v-for="action in actions" :key="action" class="text-center">
                  <i v-if="isSuperadmin || hasPermission(module.id, action)" class="ri-check-line text-success fs-5"></i>
                  <i v-else class="ri-close-line text-muted fs-5"></i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="role.users?.length" class="card">
      <div class="card-header"><h5 class="card-title mb-0">Users with this Role</h5></div>
      <div class="card-body">
        <div class="row g-3">
          <div v-for="user in role.users" :key="user.id" class="col-md-4 col-lg-3">
            <div class="d-flex align-items-center p-2 border rounded">
              <div class="avatar avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <span class="fw-medium">{{ user.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="ms-2">
                <p class="fw-medium mb-0 fs-13">{{ user.name }}</p>
                <small class="text-muted">{{ user.email }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
