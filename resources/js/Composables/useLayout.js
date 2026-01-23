import { ref, onMounted, onUnmounted } from 'vue'

const layoutType = ref('vertical')
const sidebarSize = ref('lg')
const sidebarColor = ref('dark')
const topbarColor = ref('light')
const layoutMode = ref('light')
const layoutPosition = ref('fixed')
const sidebarVisibility = ref('show')

export function useLayout() {
  const setLayoutAttributes = () => {
    document.documentElement.setAttribute('data-layout', layoutType.value)
    document.documentElement.setAttribute('data-sidebar', sidebarColor.value)
    document.documentElement.setAttribute('data-sidebar-size', sidebarSize.value)
    document.documentElement.setAttribute('data-topbar', topbarColor.value)
    document.documentElement.setAttribute('data-layout-mode', layoutMode.value)
    document.documentElement.setAttribute('data-layout-position', layoutPosition.value)
    document.documentElement.setAttribute('data-layout-style', 'default')
    document.documentElement.setAttribute('data-bs-theme', layoutMode.value)
    document.documentElement.setAttribute('data-sidebar-visibility', sidebarVisibility.value)
  }

  const setSidebarSize = (size) => {
    sidebarSize.value = size
    document.documentElement.setAttribute('data-sidebar-size', size)
  }

  const toggleSidebar = () => {
    const windowSize = document.documentElement.clientWidth

    if (windowSize > 767) {
      document.querySelector('.hamburger-icon')?.classList.toggle('open')
    }

    if (sidebarVisibility.value === 'show' && layoutType.value === 'vertical') {
      if (windowSize < 1025 && windowSize > 767) {
        document.body.classList.remove('vertical-sidebar-enable')
        setSidebarSize(sidebarSize.value === 'sm' ? 'lg' : 'sm')
      } else if (windowSize > 1025) {
        document.body.classList.remove('vertical-sidebar-enable')
        setSidebarSize(sidebarSize.value === 'lg' ? 'sm' : 'lg')
      } else if (windowSize <= 767) {
        document.body.classList.add('vertical-sidebar-enable')
        setSidebarSize('lg')
      }
    }
  }

  const toggleSidebarHover = () => {
    if (sidebarSize.value === 'sm-hover') {
      localStorage.setItem('hoverd', 'true')
      setSidebarSize('sm-hover-active')
    } else if (sidebarSize.value === 'sm-hover-active') {
      localStorage.setItem('hoverd', 'false')
      setSidebarSize('sm-hover')
    } else {
      setSidebarSize('sm-hover')
    }
  }

  const toggleDarkMode = () => {
    layoutMode.value = layoutMode.value === 'dark' ? 'light' : 'dark'
    document.documentElement.setAttribute('data-bs-theme', layoutMode.value)
    document.documentElement.setAttribute('data-layout-mode', layoutMode.value)
  }

  const toggleFullscreen = () => {
    document.body.classList.toggle('fullscreen-enable')
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen?.()
    } else {
      document.exitFullscreen?.()
    }
  }

  const handleResize = () => {
    const windowSize = window.innerWidth
    document.body.classList.remove('vertical-sidebar-enable')

    if (windowSize < 1025) {
      setSidebarSize('sm')
    } else {
      const hoverd = localStorage.getItem('hoverd')
      if (hoverd === 'true') {
        setSidebarSize('sm-hover-active')
      } else {
        setSidebarSize('lg')
      }
    }
  }

  const handleOverlayClick = () => {
    document.body.classList.remove('vertical-sidebar-enable')
  }

  const initLayout = () => {
    // Set initial attributes
    setLayoutAttributes()

    // Check localStorage for hover state
    if (localStorage.getItem('hoverd') === 'true') {
      setSidebarSize('sm-hover-active')
    }

    // Set initial size based on window
    if (window.innerWidth < 1025) {
      setSidebarSize('sm')
    }

    // Add event listeners
    window.addEventListener('resize', handleResize)
    document.getElementById('overlay')?.addEventListener('click', handleOverlayClick)
  }

  const destroyLayout = () => {
    window.removeEventListener('resize', handleResize)
  }

  return {
    // State
    layoutType,
    sidebarSize,
    sidebarColor,
    topbarColor,
    layoutMode,
    layoutPosition,
    sidebarVisibility,

    // Methods
    setLayoutAttributes,
    setSidebarSize,
    toggleSidebar,
    toggleSidebarHover,
    toggleDarkMode,
    toggleFullscreen,
    initLayout,
    destroyLayout,
    handleOverlayClick,
  }
}
