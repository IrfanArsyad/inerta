<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  module: Object,
  groups: Array,
  parents: Array,
  can: Object,
  moduleId: Number,
})

const form = useForm({
  name: props.module.name || '',
  alias: props.module.alias || '',
  label: props.module.label || '',
  icon: props.module.icon || '',
  url: props.module.url || '',
  route_name: props.module.route_name || '',
  permission: props.module.permission || '',
  badge_source: props.module.badge_source || '',
  parent_id: props.module.parent_id || '',
  module_group_id: props.module.module_group_id || '',
  order: props.module.order || 0,
  active: props.module.active ?? true,
  external: props.module.external ?? false,
})

const submit = () => form.put(route('modules.update', props.module.id))
</script>

<template>
  <Head :title="`Edit Module - ${module.label}`" />

  <AppLayout>
    <c-page-header title="Edit Module" :breadcrumbs="[{ label: 'Settings', href: route('modules.index') }, { label: 'Module Management', href: route('modules.index') }, { label: 'Edit Module' }]" />

    <form @submit.prevent="submit">
      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <c-input id="name" v-model="form.name" label="Module Name" placeholder="e.g. UserManagement" :error="form.errors.name" required />
            </div>
            <div class="col-md-6">
              <c-input id="label" v-model="form.label" label="Display Label" placeholder="e.g. User Management" :error="form.errors.label" required />
            </div>
            <div class="col-md-6">
              <c-input id="alias" v-model="form.alias" label="Alias" placeholder="e.g. usermanagement" :error="form.errors.alias" />
            </div>
            <div class="col-md-6">
              <c-input id="icon" v-model="form.icon" label="Icon Class" placeholder="e.g. ri-user-line" :error="form.errors.icon" />
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title mb-0">Routing & Permission</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <c-input id="route_name" v-model="form.route_name" label="Route Name" placeholder="e.g. users.index" :error="form.errors.route_name" />
            </div>
            <div class="col-md-6">
              <c-input id="url" v-model="form.url" label="URL (for external links)" placeholder="e.g. https://example.com" :error="form.errors.url" />
            </div>
            <div class="col-md-6">
              <c-input id="permission" v-model="form.permission" label="Permission Key" placeholder="e.g. user-management" :error="form.errors.permission" />
            </div>
            <div class="col-md-6">
              <c-input id="badge_source" v-model="form.badge_source" label="Badge Source" placeholder="e.g. unread_notifications" :error="form.errors.badge_source" />
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title mb-0">Hierarchy & Order</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <c-select id="module_group_id" v-model="form.module_group_id" label="Module Group" placeholder="Select Group" :options="groups" value-key="id" label-key="label" :error="form.errors.module_group_id" />
            </div>
            <div class="col-md-4">
              <c-select id="parent_id" v-model="form.parent_id" label="Parent Module" placeholder="No parent" :options="parents" value-key="id" label-key="label" :error="form.errors.parent_id" />
            </div>
            <div class="col-md-4">
              <c-input id="order" v-model="form.order" type="number" label="Display Order" placeholder="0" :error="form.errors.order" />
            </div>
            <div class="col-md-6">
              <c-checkbox id="active" v-model="form.active" label="Module Active" is-switch />
            </div>
            <div class="col-md-6">
              <c-checkbox id="external" v-model="form.external" label="External Link" is-switch />
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 justify-content-end">
        <c-link :href="route('modules.index')" class="btn btn-light">Cancel</c-link>
        <c-button type="submit" variant="primary" :loading="form.processing">Update</c-button>
      </div>
    </form>
  </AppLayout>
</template>
