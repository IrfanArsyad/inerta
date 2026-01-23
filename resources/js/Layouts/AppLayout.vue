<script setup>
import { onMounted, onUnmounted } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import Footer from '@/Components/Footer.vue'

const initLayout = () => {
  document.documentElement.setAttribute('data-layout', 'vertical')
  document.documentElement.setAttribute('data-sidebar', 'dark')
  document.documentElement.setAttribute('data-sidebar-size', 'lg')
  document.documentElement.setAttribute('data-topbar', 'light')
  document.documentElement.setAttribute('data-bs-theme', 'light')
  document.documentElement.setAttribute('data-layout-style', 'default')
  document.documentElement.setAttribute('data-layout-position', 'fixed')
  document.documentElement.setAttribute('data-layout-width', 'fluid')
  document.documentElement.setAttribute('data-preloader', 'disable')

  updateSidebarSize()
  window.addEventListener('resize', updateSidebarSize)
}

const updateSidebarSize = () => {
  if (window.innerWidth < 1025 && window.innerWidth >= 768) {
    document.documentElement.setAttribute('data-sidebar-size', 'sm')
  } else if (window.innerWidth < 768) {
    document.documentElement.setAttribute('data-sidebar-size', 'lg')
    document.body.classList.remove('vertical-sidebar-enable')
  } else {
    document.documentElement.setAttribute('data-sidebar-size', 'lg')
  }
}

const handleOverlayClick = () => {
  document.body.classList.remove('vertical-sidebar-enable')
}

onMounted(() => {
  initLayout()
})

onUnmounted(() => {
  window.removeEventListener('resize', updateSidebarSize)
})
</script>

<template>
  <div id="layout-wrapper">
    <Navbar />
    <Sidebar />
    <div class="vertical-overlay" @click="handleOverlayClick"></div>
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <slot />
        </div>
      </div>
      <Footer />
    </div>
  </div>
</template>
