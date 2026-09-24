import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { route } from 'ziggy-js';
import { Ziggy } from './ziggy';

window.Ziggy = Ziggy;
window.route = (name, params, absolute, config = Ziggy) => route(name, params, absolute, config);

createInertiaApp({
    title: (title) => title ? `${title} - CiptaCoding Studio` : 'CiptaCoding Studio & Keuangan',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue, Ziggy);
        app.mount(el);
        return app;
    },
    progress: {
        color: '#0062ff',
        showSpinner: true,
    },
});
