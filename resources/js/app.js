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
// import "material-symbols/outlined.css";
import MainLayout from "@/Layout/MainLayout.vue";

// Vuetify
// Vuetify
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: "mdi", // This ensures MDI is the default icon set
    },

    // icons: {
    //     defaultSet: "mdi", // keep mdi as default
    //     sets: {
    //         mdi: {
    //             component: (props) =>
    //                 h(components.VIcon, { ...props, class: "mdi" }),
    //         },
    //         material: {
    //             component: (props) =>
    //                 h(
    //                     "span",
    //                     { class: "material-symbols-outlined", ...props },
    //                     props.icon
    //                 ),
    //         },
    //     },
    // },
});

createInertiaApp({
    // Using async/await is more robust for handling the promise.
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
