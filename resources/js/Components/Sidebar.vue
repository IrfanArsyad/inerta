<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import simplebar from 'simplebar-vue'
import 'simplebar-vue/dist/simplebar.min.css'

const page = usePage()
const menu = computed(() => page.props.auth?.menu || [])

const expandedMenus = ref({})

const isActive = (routeName) => {
  if (!routeName) return false
  try {
    return route().current(routeName) || route().current(routeName.replace('.index', '.*'))
  } catch {
    return false
  }
}

const hasActiveChild = (item) => {
  if (!item.children) return false
  return item.children.some(child => isActive(child.route))
}

const toggleMenu = (itemId) => {
  expandedMenus.value[itemId] = !expandedMenus.value[itemId]
}

const isExpanded = (itemId) => {
  return expandedMenus.value[itemId] || false
}

const initActiveMenu = () => {
  // Auto-expand menus that have active children
  menu.value.forEach(group => {
    group.items?.forEach(item => {
      if (hasActiveChild(item)) {
        expandedMenus.value[item.id] = true
      }
    })
  })
}

const toggleSidebarHover = () => {
  const currentSize = document.documentElement.getAttribute('data-sidebar-size')
  if (currentSize === 'sm-hover') {
    document.documentElement.setAttribute('data-sidebar-size', 'sm-hover-active')
  } else if (currentSize === 'sm-hover-active') {
    document.documentElement.setAttribute('data-sidebar-size', 'sm-hover')
  } else {
    document.documentElement.setAttribute('data-sidebar-size', 'sm-hover')
  }
}

onMounted(() => {
  initActiveMenu()
})
</script>

<template>
  <div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
      <!-- Dark Logo-->
      <Link href="/" class="logo logo-dark">
        <span class="logo-sm">
          <img src="/images/logo-sm.png" alt="" height="22">
        </span>
        <span class="logo-lg">
          <img src="/images/logo-dark.png" alt="" height="17">
        </span>
      </Link>
      <!-- Light Logo-->
      <Link href="/" class="logo logo-light">
        <span class="logo-sm">
          <img src="/images/logo-sm.png" alt="" height="22">
        </span>
        <span class="logo-lg">
          <img src="/images/logo-light.png" alt="" height="17">
        </span>
      </Link>
      <button
        type="button"
        class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover shadow-none"
        @click="toggleSidebarHover"
      >
        <i class="ri-record-circle-line"></i>
      </button>
    </div>

    <simplebar id="scrollbar" class="h-100">
      <div class="container-fluid">
        <ul class="navbar-nav" id="navbar-nav">
          <template v-for="group in menu" :key="group.id">
            <li class="menu-title">
              <span>{{ group.label }}</span>
            </li>

            <li
              v-for="item in group.items"
              :key="item.id"
              class="nav-item"
            >
              <!-- Has Children -->
              <template v-if="item.children && item.children.length">
                <a
                  class="nav-link menu-link"
                  :class="{ 'active': hasActiveChild(item), 'collapsed': !isExpanded(item.id) }"
                  :href="'#sidebar' + item.id"
                  data-bs-toggle="collapse"
                  role="button"
                  :aria-expanded="isExpanded(item.id)"
                  @click.prevent="toggleMenu(item.id)"
                >
                  <i :class="item.icon"></i>
                  <span>{{ item.label }}</span>
                </a>
                <div
                  class="collapse menu-dropdown"
                  :class="{ 'show': isExpanded(item.id) }"
                  :id="'sidebar' + item.id"
                >
                  <ul class="nav nav-sm flex-column">
                    <li
                      v-for="child in item.children"
                      :key="child.id"
                      class="nav-item"
                    >
                      <Link
                        :href="child.route ? route(child.route) : (child.url || '#')"
                        class="nav-link"
                        :class="{ 'active': isActive(child.route) }"
                        :target="child.external ? '_blank' : null"
                      >
                        {{ child.label }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </template>

              <!-- No Children -->
              <template v-else>
                <Link
                  :href="item.route ? route(item.route) : (item.url || '#')"
                  class="nav-link menu-link"
                  :class="{ 'active': isActive(item.route) }"
                  :target="item.external ? '_blank' : null"
                >
                  <i :class="item.icon"></i>
                  <span>{{ item.label }}</span>
                  <span v-if="item.badge_source" class="badge badge-pill bg-success ms-auto">New</span>
                </Link>
              </template>
            </li>
          </template>
        </ul>
      </div>
    </simplebar>
    <div class="sidebar-background"></div>
  </div>
</template>
