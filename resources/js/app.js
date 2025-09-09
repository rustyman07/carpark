import "./bootstrap";
import "../css/app.css";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { ZiggyVue } from "ziggy-js";
import { Ziggy } from "./ziggy";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "@mdi/font/css/materialdesignicons.css";
// Import the VDateInput from the labs directory
import { VDateInput } from "vuetify/labs/VDateInput";

import MainLayout from "@/Layout/MainLayout.vue";

const vuetify = createVuetify({
    components: {
        ...components, // This line includes all standard Vuetify components
        VDateInput, // This line specifically registers the labs component
    },
    directives,
    icons: {
        defaultSet: "mdi",
    },
});

createInertiaApp({
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );
        page.default.layout = page.default.layout || MainLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin).use(ZiggyVue, Ziggy).use(vuetify).mount(el);
        return app;
    },
});
