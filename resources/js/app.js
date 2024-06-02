import './bootstrap';
import '../css/app.css';

import { createApp, h, ref, watch } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { createI18n } from 'vue-i18n';
import en from '../lang/en/messages.json?v=6';
import ar from '../lang/ar/messages.json?v=6';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const savedLocale = localStorage.getItem('locale') || 'en';

const messages = {
    en,
    ar
};

const i18n = createI18n({
    locale: savedLocale,
    fallbackLocale: 'en',
    messages,
    legacy: false, // Ensure legacy mode is disabled
    globalInjection: true // Enable global injection for useI18n
});

const direction = ref(savedLocale === 'ar' ? 'rtl' : 'ltr');

watch(
    () => i18n.global.locale,
    (newLocale) => {
        direction.value = newLocale === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.setAttribute('dir', direction.value);
    }
);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n);

        app.provide('direction', direction);

        app.mount(el);

        // Set initial direction
        document.documentElement.setAttribute('dir', direction.value);
    },
    progress: {
        color: '#4B5563',
    },
});
