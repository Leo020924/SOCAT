// resources/js/main.js
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { resolvePageComponent } from 'vite-plugin-vue-layouts'
import './bootstrap';
import ElementPlus from 'element-plus'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import { InertiaProgress } from '@inertiajs/progress'
import { ZiggyVue } from './ziggy';
import { router as myRouter } from './router'; // Importa el router



InertiaProgress.init()

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
       const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
         app.use(ElementPlus)
          app.use(ZiggyVue)
           app.use(myRouter); // Usa el router
        for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
          app.component(key, component)
        }
         app.mount(el)
    },
})