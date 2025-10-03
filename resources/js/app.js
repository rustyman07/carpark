import "./bootstrap";
import "@fontsource/inter";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { ZiggyVue } from "ziggy-js";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "@mdi/font/css/materialdesignicons.css";
import { VDateInput } from "vuetify/labs/VDateInput";
import VueApexCharts from "vue3-apexcharts";

import MainLayout from "@/Layouts/MainLayout.vue";

// --- Initialize Vuetify ---
const vuetify = createVuetify({
    components: {
        ...components,
        VDateInput,
    },
    directives,
    icons: {
        defaultSet: "mdi",
    },
});

// --- Create Inertia app ---
createInertiaApp({
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );

        // Use page layout if defined; otherwise use MainLayout
        page.default.layout = page.default.layout || MainLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // ✅ Use dynamic routes via @routes in Blade, no static import
        app.use(plugin).use(ZiggyVue).use(vuetify).use(VueApexCharts).mount(el);

        return app;
    },
});
