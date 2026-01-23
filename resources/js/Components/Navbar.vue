<script setup>
import { computed, onMounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import simplebar from 'simplebar-vue'
import 'simplebar-vue/dist/simplebar.min.css'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const toggleHamburgerMenu = () => {
  const windowSize = document.documentElement.clientWidth

  if (windowSize > 767) {
    document.querySelector('.hamburger-icon')?.classList.toggle('open')
  }

  if (windowSize < 1025 && windowSize > 767) {
    document.body.classList.remove('vertical-sidebar-enable')
    const currentSize = document.documentElement.getAttribute('data-sidebar-size')
    document.documentElement.setAttribute('data-sidebar-size', currentSize === 'sm' ? '' : 'sm')
  } else if (windowSize > 1025) {
    document.body.classList.remove('vertical-sidebar-enable')
    const currentSize = document.documentElement.getAttribute('data-sidebar-size')
    document.documentElement.setAttribute('data-sidebar-size', currentSize === 'lg' ? 'sm' : 'lg')
  } else if (windowSize <= 767) {
    document.body.classList.add('vertical-sidebar-enable')
    document.documentElement.setAttribute('data-sidebar-size', 'lg')
  }
}

const toggleFullscreen = () => {
  document.body.classList.toggle('fullscreen-enable')
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen?.()
  } else {
    document.exitFullscreen?.()
  }
}

const toggleDarkMode = () => {
  const currentMode = document.documentElement.getAttribute('data-bs-theme')
  document.documentElement.setAttribute('data-bs-theme', currentMode === 'dark' ? 'light' : 'dark')
}

const logout = () => {
  router.post(route('logout'))
}

onMounted(() => {
  // Add scroll listener for topbar shadow
  document.addEventListener('scroll', function () {
    const pageTopbar = document.getElementById('page-topbar')
    if (pageTopbar) {
      if (document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50) {
        pageTopbar.classList.add('topbar-shadow')
      } else {
        pageTopbar.classList.remove('topbar-shadow')
      }
    }
  })
})
</script>

<template>
  <header id="page-topbar">
    <div class="layout-width">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box horizontal-logo">
            <Link href="/" class="logo logo-dark">
              <span class="logo-sm">
                <img src="/images/logo-sm.png" alt="" height="22">
              </span>
              <span class="logo-lg">
                <img src="/images/logo-dark.png" alt="" height="17">
              </span>
            </Link>

            <Link href="/" class="logo logo-light">
              <span class="logo-sm">
                <img src="/images/logo-sm.png" alt="" height="22">
              </span>
              <span class="logo-lg">
                <img src="/images/logo-light.png" alt="" height="17">
              </span>
            </Link>
          </div>

          <button
            type="button"
            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
            id="topnav-hamburger-icon"
            @click="toggleHamburgerMenu"
          >
            <span class="hamburger-icon">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </button>

          <!-- App Search-->
          <form class="app-search d-none d-md-block">
            <div class="position-relative">
              <input type="text" class="form-control" placeholder="Search..." autocomplete="off">
              <span class="mdi mdi-magnify search-widget-icon"></span>
            </div>
          </form>
        </div>

        <div class="d-flex align-items-center">
          <!-- Fullscreen -->
          <div class="ms-1 header-item d-none d-sm-flex">
            <button
              type="button"
              class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
              data-toggle="fullscreen"
              @click="toggleFullscreen"
            >
              <i class="bx bx-fullscreen fs-22"></i>
            </button>
          </div>

          <!-- Dark Mode -->
          <div class="ms-1 header-item d-none d-sm-flex">
            <button
              type="button"
              class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none"
              @click="toggleDarkMode"
            >
              <i class="bx bx-moon fs-22"></i>
            </button>
          </div>

          <!-- Notifications Dropdown -->
          <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
            <button
              type="button"
              class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
              id="page-header-notifications-dropdown"
              data-bs-toggle="dropdown"
              data-bs-auto-close="outside"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="bx bx-bell fs-22"></i>
              <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                3<span class="visually-hidden">unread messages</span>
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
              <div class="dropdown-head bg-primary bg-pattern rounded-top">
                <div class="p-3">
                  <div class="row align-items-center">
                    <div class="col">
                      <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                    </div>
                    <div class="col-auto dropdown-tabs">
                      <span class="badge bg-light-subtle text-body fs-13">4 New</span>
                    </div>
                  </div>
                </div>
              </div>
              <simplebar style="max-height: 300px;">
                <div class="text-reset notification-item d-block dropdown-item position-relative">
                  <div class="d-flex">
                    <div class="avatar-xs me-3 flex-shrink-0">
                      <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                        <i class="bx bx-badge-check"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1">
                      <a href="#!" class="stretched-link">
                        <h6 class="mt-0 mb-2 lh-base">
                          Your <b>Elite</b> author Graphic Optimization
                          <span class="text-secondary">reward</span> is ready!
                        </h6>
                      </a>
                      <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                        <span><i class="mdi mdi-clock-outline"></i> Just 30 sec ago</span>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="text-reset notification-item d-block dropdown-item position-relative">
                  <div class="d-flex">
                    <img src="/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs flex-shrink-0" alt="user-pic">
                    <div class="flex-grow-1">
                      <a href="#!" class="stretched-link">
                        <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                      </a>
                      <div class="fs-13 text-muted">
                        <p class="mb-1">Answered to your comment on the cash flow forecast's graph 🔔.</p>
                      </div>
                      <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                        <span><i class="mdi mdi-clock-outline"></i> 48 min ago</span>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="text-reset notification-item d-block dropdown-item position-relative">
                  <div class="d-flex">
                    <div class="avatar-xs me-3 flex-shrink-0">
                      <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                        <i class="bx bx-message-square-dots"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1">
                      <a href="#!" class="stretched-link">
                        <h6 class="mt-0 mb-2 fs-13 lh-base">
                          You have received <b class="text-success">20</b> new messages in the conversation
                        </h6>
                      </a>
                      <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                        <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                      </p>
                    </div>
                  </div>
                </div>
              </simplebar>
              <div class="my-3 text-center">
                <button type="button" class="btn btn-soft-success">
                  View All Notifications <i class="ri-arrow-right-line align-middle"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- User Dropdown -->
          <div class="dropdown ms-sm-3 header-item topbar-user">
            <button
              type="button"
              class="btn shadow-none"
              id="page-header-user-dropdown"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <span class="d-flex align-items-center">
                <img class="rounded-circle header-profile-user" src="/images/users/avatar-1.jpg" alt="Header Avatar">
                <span class="text-start ms-xl-2">
                  <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ user?.name }}</span>
                  <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ user?.role?.display_name }}</span>
                </span>
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="page-header-user-dropdown">
              <h6 class="dropdown-header">Welcome {{ user?.name }}!</h6>
              <a class="dropdown-item" href="#">
                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Profile</span>
              </a>
              <a class="dropdown-item" href="#">
                <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Settings</span>
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" @click.prevent="logout">
                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Logout</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
