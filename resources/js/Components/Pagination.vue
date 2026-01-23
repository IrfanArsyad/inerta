<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const sizeClass = computed(() => {
  const sizes = {
    sm: 'pagination-sm',
    md: '',
    lg: 'pagination-lg',
  }
  return sizes[props.size]
})

const paginationClass = computed(() => {
  const classes = ['pagination', 'mb-0']
  if (sizeClass.value) classes.push(sizeClass.value)
  return classes.join(' ')
})

// Filter out "..." placeholder links and process labels
const processedLinks = computed(() => {
  return props.links.map(link => ({
    ...link,
    label: cleanLabel(link.label),
  }))
})

const cleanLabel = (label) => {
  // Remove HTML entities and arrows
  return label
    .replace(/&laquo;/g, '')
    .replace(/&raquo;/g, '')
    .replace(/Previous/i, '')
    .replace(/Next/i, '')
    .trim()
}

const isFirst = (index) => index === 0
const isLast = (index) => index === props.links.length - 1
</script>

<template>
  <nav v-if="links.length > 3" aria-label="Page navigation">
    <ul :class="paginationClass">
      <template v-for="(link, index) in links" :key="index">
        <!-- Previous Button -->
        <li v-if="isFirst(index)" class="page-item" :class="{ disabled: !link.url }">
          <Link
            v-if="link.url"
            :href="link.url"
            class="page-link"
            preserve-scroll
          >
            <i class="mdi mdi-chevron-left"></i>
          </Link>
          <span v-else class="page-link">
            <i class="mdi mdi-chevron-left"></i>
          </span>
        </li>

        <!-- Next Button -->
        <li v-else-if="isLast(index)" class="page-item" :class="{ disabled: !link.url }">
          <Link
            v-if="link.url"
            :href="link.url"
            class="page-link"
            preserve-scroll
          >
            <i class="mdi mdi-chevron-right"></i>
          </Link>
          <span v-else class="page-link">
            <i class="mdi mdi-chevron-right"></i>
          </span>
        </li>

        <!-- Page Numbers -->
        <li v-else class="page-item" :class="{ active: link.active }">
          <Link
            v-if="link.url && !link.active"
            :href="link.url"
            class="page-link"
            preserve-scroll
          >
            {{ processedLinks[index].label || link.label }}
          </Link>
          <span v-else class="page-link">
            {{ processedLinks[index].label || link.label }}
          </span>
        </li>
      </template>
    </ul>
  </nav>
</template>
