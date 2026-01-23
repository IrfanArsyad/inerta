<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Pagination from './Pagination.vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
    // [{ key: 'name', label: 'Name', sortable: true, class: '' }]
  },
  data: {
    type: Array,
    required: true,
  },
  pagination: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  striped: {
    type: Boolean,
    default: false,
  },
  hoverable: {
    type: Boolean,
    default: true,
  },
  emptyText: {
    type: String,
    default: 'No data available.',
  },
  showActions: {
    type: Boolean,
    default: true,
  },
  canView: {
    type: Boolean,
    default: true,
  },
  canEdit: {
    type: Boolean,
    default: false,
  },
  canDelete: {
    type: Boolean,
    default: false,
  },
  viewRoute: {
    type: String,
    default: null,
  },
  editRoute: {
    type: String,
    default: null,
  },
})

const emit = defineEmits(['delete', 'view', 'edit', 'sort'])

const tableClass = computed(() => {
  const classes = ['table', 'align-middle', 'table-nowrap', 'mb-0']
  if (props.striped) classes.push('table-striped')
  if (props.hoverable) classes.push('table-hover')
  return classes.join(' ')
})

const hasActions = computed(() => {
  return props.showActions && (props.canView || props.canEdit || props.canDelete)
})

const getValue = (item, key) => {
  // Support nested keys like 'user.name'
  return key.split('.').reduce((obj, k) => obj?.[k], item)
}
</script>

<template>
  <div class="table-responsive">
    <table :class="tableClass">
      <thead class="table-light">
        <tr>
          <th v-if="$slots.checkbox" style="width: 50px;">
            <slot name="checkbox-header" />
          </th>
          <th
            v-for="column in columns"
            :key="column.key"
            :class="column.class"
            :style="column.width ? `width: ${column.width}` : ''"
            scope="col"
          >
            <span
              v-if="column.sortable"
              class="cursor-pointer d-flex align-items-center"
              @click="$emit('sort', column.key)"
            >
              {{ column.label }}
              <i class="ri-arrow-up-down-line ms-1 text-muted"></i>
            </span>
            <span v-else>{{ column.label }}</span>
          </th>
          <th v-if="hasActions" scope="col" style="width: 120px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loading State -->
        <tr v-if="loading">
          <td :colspan="columns.length + (hasActions ? 1 : 0) + ($slots.checkbox ? 1 : 0)" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </td>
        </tr>

        <!-- Data Rows -->
        <template v-else-if="data.length > 0">
          <tr v-for="(item, index) in data" :key="item.id || index">
            <td v-if="$slots.checkbox">
              <slot name="checkbox" :item="item" :index="index" />
            </td>
            <td v-for="column in columns" :key="column.key" :class="column.cellClass">
              <slot :name="`cell-${column.key}`" :item="item" :value="getValue(item, column.key)">
                {{ getValue(item, column.key) }}
              </slot>
            </td>
            <td v-if="hasActions">
              <div class="d-flex gap-2">
                <!-- View Button -->
                <slot name="action-view" :item="item">
                  <Link
                    v-if="canView && viewRoute"
                    :href="route(viewRoute, item.id)"
                    class="btn btn-sm btn-soft-primary"
                    title="View"
                  >
                    <i class="ri-eye-fill align-bottom"></i>
                  </Link>
                  <button
                    v-else-if="canView"
                    type="button"
                    class="btn btn-sm btn-soft-primary"
                    title="View"
                    @click="$emit('view', item)"
                  >
                    <i class="ri-eye-fill align-bottom"></i>
                  </button>
                </slot>

                <!-- Edit Button -->
                <slot name="action-edit" :item="item">
                  <Link
                    v-if="canEdit && editRoute"
                    :href="route(editRoute, item.id)"
                    class="btn btn-sm btn-soft-secondary"
                    title="Edit"
                  >
                    <i class="ri-pencil-fill align-bottom"></i>
                  </Link>
                  <button
                    v-else-if="canEdit"
                    type="button"
                    class="btn btn-sm btn-soft-secondary"
                    title="Edit"
                    @click="$emit('edit', item)"
                  >
                    <i class="ri-pencil-fill align-bottom"></i>
                  </button>
                </slot>

                <!-- Delete Button -->
                <slot name="action-delete" :item="item">
                  <button
                    v-if="canDelete"
                    type="button"
                    class="btn btn-sm btn-soft-danger"
                    title="Delete"
                    @click="$emit('delete', item)"
                  >
                    <i class="ri-delete-bin-fill align-bottom"></i>
                  </button>
                </slot>

                <!-- Custom Actions -->
                <slot name="actions" :item="item" />
              </div>
            </td>
          </tr>
        </template>

        <!-- Empty State -->
        <tr v-else>
          <td :colspan="columns.length + (hasActions ? 1 : 0) + ($slots.checkbox ? 1 : 0)" class="text-center py-4">
            <slot name="empty">
              <div class="py-4">
                <lord-icon
                  src="https://cdn.lordicon.com/msoeawqm.json"
                  trigger="loop"
                  colors="primary:#405189,secondary:#0ab39c"
                  style="width: 72px; height: 72px;"
                ></lord-icon>
                <h5 class="mt-4">{{ emptyText }}</h5>
              </div>
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div v-if="pagination && pagination.links && data.length > 0" class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} entries
    </div>
    <Pagination :links="pagination.links" />
  </div>
</template>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
