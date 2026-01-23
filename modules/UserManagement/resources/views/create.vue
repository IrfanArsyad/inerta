<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  roles: Array,
  can: Object,
  moduleId: Number,
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_active: true,
  role_id: '',
})

const submit = () => form.post(route('users.store'))
</script>

<template>
  <Head title="Tambah User" />

  <AppLayout>
    <c-page-header title="Tambah User" :breadcrumbs="[{ label: 'User Management', href: route('users.index') }, { label: 'Tambah User' }]" />

    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Form User Baru</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row">
            <div class="col-md-6">
              <c-input id="name" v-model="form.name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" :error="form.errors.name" required />
            </div>
            <div class="col-md-6">
              <c-input id="email" v-model="form.email" type="email" label="Email" placeholder="Masukkan email" :error="form.errors.email" required />
            </div>
            <div class="col-md-6">
              <c-input id="password" v-model="form.password" label="Password" placeholder="Masukkan password" :error="form.errors.password" password-toggle required />
            </div>
            <div class="col-md-6">
              <c-input id="password_confirmation" v-model="form.password_confirmation" label="Konfirmasi Password" placeholder="Ulangi password" password-toggle required />
            </div>
            <div class="col-md-6">
              <c-select id="role_id" v-model="form.role_id" label="Role" placeholder="Pilih Role" :options="roles" value-key="id" label-key="display_name" :error="form.errors.role_id" required />
            </div>
            <div class="col-md-6">
              <c-checkbox id="is_active" v-model="form.is_active" label="User Aktif" is-switch />
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-3">
            <c-link :href="route('users.index')" class="btn btn-light">Batal</c-link>
            <c-button type="submit" variant="primary" :loading="form.processing">Simpan</c-button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
