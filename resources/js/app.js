import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import './bootstrap';

createInertiaApp({
    title: (title) => title ? `${title} | Photography` : 'Photography Portfolio',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
