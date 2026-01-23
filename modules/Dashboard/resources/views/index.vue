<script setup>
import { computed } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_users: 0,
      active_users: 0,
      total_roles: 0,
      active_roles: 0,
      total_modules: 0,
      active_modules: 0,
      total_permissions: 0,
    })
  },
  systemInfo: {
    type: Object,
    default: () => ({
      app_version: '1.0.0',
      php_version: '8.2',
      laravel_version: '12.x',
      vue_version: '3.x',
    })
  }
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <!-- Page Title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0">Dashboard</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Welcome Message -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card overflow-hidden">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body h-100">
                <h5>Welcome Back, {{ user?.name }}!</h5>
                <p class="text-muted mb-0">
                  You are logged in as
                  <span class="text-primary fw-semibold">{{ user?.role?.display_name }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1 overflow-hidden">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Users</p>
              </div>
              <div class="flex-shrink-0">
                <span class="badge bg-primary-subtle text-primary">{{ stats.active_users }} active</span>
              </div>
            </div>
            <div class="d-flex align-items-end justify-content-between mt-4">
              <div>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                  <span class="counter-value">{{ stats.total_users }}</span>
                </h4>
              </div>
              <div class="avatar-sm flex-shrink-0">
                <span class="avatar-title bg-primary-subtle rounded fs-3">
                  <i class="bx bx-user-circle text-primary"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1 overflow-hidden">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Roles</p>
              </div>
              <div class="flex-shrink-0">
                <span class="badge bg-success-subtle text-success">{{ stats.active_roles }} active</span>
              </div>
            </div>
            <div class="d-flex align-items-end justify-content-between mt-4">
              <div>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                  <span class="counter-value">{{ stats.total_roles }}</span>
                </h4>
              </div>
              <div class="avatar-sm flex-shrink-0">
                <span class="avatar-title bg-success-subtle rounded fs-3">
                  <i class="bx bx-shield-quarter text-success"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1 overflow-hidden">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Modules</p>
              </div>
              <div class="flex-shrink-0">
                <span class="badge bg-warning-subtle text-warning">{{ stats.active_modules }} active</span>
              </div>
            </div>
            <div class="d-flex align-items-end justify-content-between mt-4">
              <div>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                  <span class="counter-value">{{ stats.total_modules }}</span>
                </h4>
              </div>
              <div class="avatar-sm flex-shrink-0">
                <span class="avatar-title bg-warning-subtle rounded fs-3">
                  <i class="bx bx-grid-alt text-warning"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1 overflow-hidden">
                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Permissions</p>
              </div>
              <div class="flex-shrink-0">
                <span class="badge bg-danger-subtle text-danger">assigned</span>
              </div>
            </div>
            <div class="d-flex align-items-end justify-content-between mt-4">
              <div>
                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                  <span class="counter-value">{{ stats.total_permissions }}</span>
                </h4>
              </div>
              <div class="avatar-sm flex-shrink-0">
                <span class="avatar-title bg-danger-subtle rounded fs-3">
                  <i class="bx bx-key text-danger"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Info -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">System Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light text-primary rounded">
                      <i class="ri-code-s-slash-line"></i>
                    </span>
                  </div>
                  <div class="ms-3">
                    <p class="text-muted mb-0">App Version</p>
                    <h5 class="fs-14 mb-0">{{ systemInfo.app_version }}</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light text-success rounded">
                      <i class="mdi mdi-language-php"></i>
                    </span>
                  </div>
                  <div class="ms-3">
                    <p class="text-muted mb-0">PHP Version</p>
                    <h5 class="fs-14 mb-0">{{ systemInfo.php_version }}</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light text-danger rounded">
                      <i class="mdi mdi-laravel"></i>
                    </span>
                  </div>
                  <div class="ms-3">
                    <p class="text-muted mb-0">Laravel Version</p>
                    <h5 class="fs-14 mb-0">{{ systemInfo.laravel_version }}</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="d-flex align-items-center mb-3">
                  <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light text-info rounded">
                      <i class="mdi mdi-vuejs"></i>
                    </span>
                  </div>
                  <div class="ms-3">
                    <p class="text-muted mb-0">Vue.js Version</p>
                    <h5 class="fs-14 mb-0">{{ systemInfo.vue_version }}</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
