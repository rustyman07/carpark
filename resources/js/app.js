import "./bootstrap";
import '../css/app.css';
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { ZiggyVue } from "ziggy-js";
import { Ziggy } from "./ziggy"; // the generated routes file

import "@mdi/font/css/materialdesignicons.css";

import MainLayout from "@/Layout/MainLayout.vue";

const vuetify = createVuetify({
    components,
    directives,
});

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });

        let page = pages[`./Pages/${name}.vue`];

        page.default.layout ??= MainLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(vuetify)
            .mount(el);
    },
});
