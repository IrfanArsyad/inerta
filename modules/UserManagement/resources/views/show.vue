<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermission } from '@/Composables/usePermission'

const props = defineProps({
  user: Object,
  can: Object,
  moduleId: Number,
})

const { canUpdate } = usePermission(props.moduleId)
</script>

<template>
  <Head :title="`User Detail - ${user.name}`" />

  <AppLayout>
    <c-page-header title="User Detail" :breadcrumbs="[{ label: 'User Management', href: route('users.index') }, { label: 'User Detail' }]">
      <template #actions>
        <c-link :href="route('users.index')" class="btn btn-light me-2"><i class="ri-arrow-left-line me-1"></i> Back</c-link>
      </template>
    </c-page-header>

    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center mb-4">
          <div class="avatar avatar-xl bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <span class="fs-1 fw-medium">{{ user.name.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="ms-3">
            <h4 class="mb-1">{{ user.name }}</h4>
            <p class="text-muted mb-1">{{ user.email }}</p>
            <span class="badge" :class="user.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ user.is_active ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>

        <div class="border-top pt-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">ID</label>
              <p class="mb-0">{{ user.id }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">Email</label>
              <p class="mb-0">{{ user.email }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">Role</label>
              <div>
                <span v-if="user.role" class="badge bg-info-subtle text-info">{{ user.role.display_name || user.role.name }}</span>
                <span v-else class="text-muted">-</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">Last Login</label>
              <p class="mb-0">{{ user.last_login_at || '-' }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">Created At</label>
              <p class="mb-0">{{ user.created_at }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted mb-1">Updated At</label>
              <p class="mb-0">{{ user.updated_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
