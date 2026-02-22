import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { route } from 'ziggy-js'
import { createBootstrap } from 'bootstrap-vue-next'

// Import Bootstrap JS bundle for dropdowns, collapse, tooltips, etc.
import 'bootstrap/dist/js/bootstrap.bundle'

// Import Simplebar for custom scrollbars
import SimpleBar from 'simplebar'
import 'simplebar/dist/simplebar.css'

// =================================================================
// Global Components (Sering dipakai - register di sini)
// =================================================================
// UI Components
import PageHeader from '@/Components/PageHeader.vue'
import DataTable from '@/Components/DataTable.vue'
import TableFilters from '@/Components/TableFilters.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import FlashMessage from '@/Components/FlashMessage.vue'

// Form Components
import Input from '@/Components/Form/Input.vue'
import Select from '@/Components/Form/Select.vue'
import Checkbox from '@/Components/Form/Checkbox.vue'
import Button from '@/Components/Form/Button.vue'
import TextArea from '@/Components/Form/TextArea.vue'

// =================================================================
// Per-file Components (Jarang dipakai - import manual jika perlu)
// =================================================================
// - Modal         → import Modal from '@/Components/Modal.vue'
// - Pagination    → sudah include di DataTable

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// Initialize SimpleBar on elements with data-simplebar attribute
const initSimplebar = () => {
    document.querySelectorAll('[data-simplebar]').forEach((el) => {
        if (!el.SimpleBar) {
            new SimpleBar(el)
        }
    })
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // Support module pages: ModuleName::views/page
        if (name.includes('::')) {
            const [moduleName, pagePath] = name.split('::')
            return resolvePageComponent(
                `../../modules/${moduleName}/resources/${pagePath}.vue`,
                import.meta.glob('../../modules/*/resources/views/*.vue')
            )
        }
        // Default resources/js/Pages
        return resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        )
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia()
        const bootstrapPlugin = createBootstrap()

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(bootstrapPlugin)

        // Register route() as global property for template usage
        app.config.globalProperties.route = route

        // Register Global Components with 'c-' prefix
        app.component('c-link', Link)
        app.component('c-page-header', PageHeader)
        app.component('c-data-table', DataTable)
        app.component('c-table-filters', TableFilters)
        app.component('c-confirm-dialog', ConfirmDialog)
        app.component('c-flash-message', FlashMessage)

        // Register Form Components
        app.component('c-input', Input)
        app.component('c-select', Select)
        app.component('c-checkbox', Checkbox)
        app.component('c-button', Button)
        app.component('c-textarea', TextArea)

        app.mount(el)

        // Initialize simplebar after mount
        setTimeout(initSimplebar, 100)

        return app
    },
    progress: {
        color: '#405189',
    },
})

// Re-init simplebar on Inertia page navigation
document.addEventListener('inertia:finish', () => {
    setTimeout(initSimplebar, 100)
})
