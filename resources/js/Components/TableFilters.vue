<script setup>
import { computed } from 'vue'

const props = defineProps({
  search: {
    type: String,
    default: '',
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...',
  },
  showSearch: {
    type: Boolean,
    default: true,
  },
  filters: {
    type: Array,
    default: () => [],
    // [{ key: 'status', label: 'Status', type: 'select', options: [...] }]
  },
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  createRoute: {
    type: String,
    default: null,
  },
  createLabel: {
    type: String,
    default: 'Add',
  },
  canCreate: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:search', 'update:modelValue', 'filter', 'create'])

const searchValue = computed({
  get: () => props.search,
  set: (value) => emit('update:search', value),
})

const filterValues = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const updateFilter = (key, value) => {
  const newFilters = { ...filterValues.value, [key]: value }
  emit('update:modelValue', newFilters)
  emit('filter', newFilters)
}
</script>

<template>
  <div class="card mb-2">
    <div class="card-body p-2">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <!-- Search Input -->
        <div v-if="showSearch" class="search-box">
          <input
            v-model="searchValue"
            type="text"
            class="form-control form-control-sm"
            :placeholder="searchPlaceholder"
          >
          <i class="ri-search-line search-icon"></i>
        </div>

        <!-- Dynamic Filters -->
        <template v-for="filter in filters" :key="filter.key">
          <!-- Select Filter -->
          <select
            v-if="filter.type === 'select' || !filter.type"
            class="form-select form-select-sm"
            :value="filterValues[filter.key]"
            @change="updateFilter(filter.key, $event.target.value)"
            :style="{ width: filter.width || 'auto' }"
          >
            <option
              v-for="option in filter.options"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>

          <!-- Date Filter -->
          <input
            v-else-if="filter.type === 'date'"
            type="date"
            class="form-control form-control-sm"
            :value="filterValues[filter.key]"
            @change="updateFilter(filter.key, $event.target.value)"
            style="width: auto;"
          >

          <!-- Custom Filter Slot -->
          <slot v-else :name="`filter-${filter.key}`" :filter="filter" :value="filterValues[filter.key]" />
        </template>

        <!-- Custom Actions Slot -->
        <slot name="actions" />

        <!-- Spacer -->
        <div class="flex-grow-1"></div>

        <!-- Create Button -->
        <c-link v-if="canCreate && createRoute" :href="route(createRoute)" class="btn btn-success btn-sm">
          <i class="ri-add-line align-bottom me-1"></i>{{ createLabel }}
        </c-link>
        <button v-else-if="canCreate" type="button" class="btn btn-success btn-sm" @click="emit('create')">
          <i class="ri-add-line align-bottom me-1"></i>{{ createLabel }}
        </button>

        <!-- Custom Right Actions -->
        <slot name="rightActions" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.search-box {
  position: relative;
}

.search-box input {
  padding-left: 30px;
  min-width: 180px;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--vz-secondary-color);
  font-size: 13px;
}
</style>
