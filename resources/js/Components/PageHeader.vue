<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: { type: String, required: true },
  breadcrumbs: { type: Array, default: () => [] },
})
</script>

<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0">{{ title }}</h4>
        <div class="d-flex align-items-center gap-2">
          <slot name="actions" />
          <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><Link href="/">Dashboard</Link></li>
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item" :class="{ active: index === breadcrumbs.length - 1 }">
              <Link v-if="crumb.href && index !== breadcrumbs.length - 1" :href="crumb.href">{{ crumb.label }}</Link>
              <span v-else>{{ crumb.label }}</span>
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</template>
