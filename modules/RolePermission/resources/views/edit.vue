<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  role: Object,
  modules: Array,
  can: Object,
  moduleId: Number,
})

const actions = ['read', 'create', 'update', 'delete']
const actionLabels = { read: 'Lihat', create: 'Tambah', update: 'Edit', delete: 'Hapus' }

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

const form = useForm({
  name: props.role.name,
  display_name: props.role.display_name || '',
  description: props.role.description || '',
  active: props.role.active,
  permissions: convertPermissions(props.role.permissions),
})

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
const isSuperadmin = props.role.name === 'superadmin'

const submit = () => form.put(route('roles.update', props.role.id))
</script>

<template>
  <Head :title="`Edit Role - ${role.name}`" />

  <AppLayout>
    <c-page-header :title="`Edit Role: ${role.display_name || role.name}`" :breadcrumbs="[{ label: 'Role & Permission', href: route('roles.index') }, { label: 'Edit Role' }]" />

    <form @submit.prevent="submit">
      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title mb-0">Informasi Role</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <c-input id="name" v-model="form.name" label="Nama Role" placeholder="Masukkan nama role (slug)" :error="form.errors.name" :disabled="isSuperadmin" required />
              <small v-if="isSuperadmin" class="text-muted">Nama role Superadmin tidak dapat diubah</small>
            </div>
            <div class="col-md-6">
              <c-input id="display_name" v-model="form.display_name" label="Display Name" placeholder="Masukkan display name" :error="form.errors.display_name" />
            </div>
            <div class="col-md-12">
              <c-textarea id="description" v-model="form.description" label="Deskripsi" placeholder="Masukkan deskripsi role" :error="form.errors.description" :rows="2" />
            </div>
            <div class="col-md-6">
              <c-checkbox id="active" v-model="form.active" label="Role Aktif" is-switch />
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Hak Akses Module</h5></div>
        <div class="card-body">
          <div v-if="isSuperadmin" class="alert alert-warning mb-3">Role Superadmin memiliki akses penuh ke semua module dan tidak dapat diubah.</div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Module</th>
                  <th class="text-center" style="width: 80px;">Semua</th>
                  <th v-for="action in actions" :key="action" class="text-center" style="width: 80px;">{{ actionLabels[action] }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="module in modules" :key="module.id">
                  <td><div class="fw-medium">{{ module.label }}</div><small class="text-muted">{{ module.name }}</small></td>
                  <td class="text-center"><input type="checkbox" class="form-check-input" :checked="isSuperadmin || isAllCheckedForModule(module.id)" :disabled="isSuperadmin" @change="toggleAllForModule(module.id)"></td>
                  <td v-for="action in actions" :key="action" class="text-center"><input type="checkbox" class="form-check-input" :checked="isSuperadmin || hasPermission(module.id, action)" :disabled="isSuperadmin" @change="togglePermission(module.id, action)"></td>
                </tr>
                <tr v-if="!modules.length"><td colspan="6" class="text-center text-muted py-4">Tidak ada module tersedia</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 justify-content-end mt-3">
        <c-link :href="route('roles.index')" class="btn btn-light">Batal</c-link>
        <c-button v-if="!isSuperadmin" type="submit" variant="primary" :loading="form.processing">Update</c-button>
      </div>
    </form>
  </AppLayout>
</template>
