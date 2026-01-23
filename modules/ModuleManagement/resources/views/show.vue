<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermission } from '@/Composables/usePermission'

const props = defineProps({
  module: Object,
  can: Object,
  moduleId: Number,
})

const { canUpdate } = usePermission(props.moduleId)
</script>

<template>
  <Head :title="`Module Detail - ${module.label}`" />

  <AppLayout>
    <c-page-header title="Module Detail" :breadcrumbs="[{ label: 'Settings', href: route('modules.index') }, { label: 'Module Management', href: route('modules.index') }, { label: 'Detail' }]">
      <template #actions>
        <c-link :href="route('modules.index')" class="btn btn-light me-2">
          <i class="ri-arrow-left-line me-1"></i> Back
        </c-link>
        <c-link v-if="canUpdate" :href="route('modules.edit', module.id)" class="btn btn-primary">
          <i class="ri-edit-line me-1"></i> Edit Module
        </c-link>
      </template>
    </c-page-header>

    <div class="row">
      <div class="col-lg-8">
        <div class="card mb-3">
          <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Module Name</label>
                <p class="fw-semibold mb-0">{{ module.name }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Label</label>
                <p class="mb-0">{{ module.label }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Alias</label>
                <p class="mb-0">{{ module.alias || '-' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Icon</label>
                <div v-if="module.icon"><i :class="module.icon" class="me-2 fs-5 text-primary"></i><code>{{ module.icon }}</code></div>
                <p v-else class="mb-0 text-muted">-</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header"><h5 class="card-title mb-0">Routing & Permission</h5></div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Route Name</label>
                <p class="mb-0"><code v-if="module.route_name">{{ module.route_name }}</code><span v-else class="text-muted">-</span></p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">URL</label>
                <p class="mb-0">{{ module.url || '-' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Permission Key</label>
                <p class="mb-0"><code v-if="module.permission">{{ module.permission }}</code><span v-else class="text-muted">-</span></p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted mb-1">Badge Source</label>
                <p class="mb-0">{{ module.badge_source || '-' }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="module.children?.length" class="card">
          <div class="card-header"><h5 class="card-title mb-0">Sub Modules</h5></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Label</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="child in module.children" :key="child.id">
                    <td class="fw-medium">{{ child.name }}</td>
                    <td>{{ child.label }}</td>
                    <td><span class="badge" :class="child.active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ child.active ? 'Active' : 'Inactive' }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-header"><h5 class="card-title mb-0">Status & Hierarchy</h5></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label text-muted mb-1">Status</label>
              <div><span class="badge" :class="module.active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ module.active ? 'Active' : 'Inactive' }}</span></div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted mb-1">External Link</label>
              <div><span class="badge" :class="module.external ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary'">{{ module.external ? 'Yes' : 'No' }}</span></div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted mb-1">Display Order</label>
              <p class="mb-0"><span class="badge bg-secondary-subtle text-secondary">{{ module.order }}</span></p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted mb-1">Module Group</label>
              <p class="mb-0"><span v-if="module.group" class="badge bg-info-subtle text-info">{{ module.group.label || module.group.name }}</span><span v-else class="text-muted">-</span></p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted mb-1">Parent Module</label>
              <p class="mb-0"><span v-if="module.parent" class="badge bg-primary-subtle text-primary">{{ module.parent.label || module.parent.name }}</span><span v-else class="text-muted">Root Module</span></p>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h5 class="card-title mb-0">Timestamps</h5></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label text-muted mb-1">Created At</label>
              <p class="mb-0">{{ module.created_at }}</p>
            </div>
            <div>
              <label class="form-label text-muted mb-1">Updated At</label>
              <p class="mb-0">{{ module.updated_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
