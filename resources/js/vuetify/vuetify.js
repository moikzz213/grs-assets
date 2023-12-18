// Vuetify
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as directives from "vuetify/directives";
import * as components from "vuetify/components"; // import all
// import components from "./components"; // manual imports

import { aliases, mdi } from "vuetify/iconsets/mdi-svg";
// icon library - https://pictogrammers.com/library/mdi/

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: "light",
        themes: {
            light: {
                colors: {
                    primary: "#000000",
                    secondary: "#ffed00",
                    background: "#fafafa",
                    "background-darken-1": "#F9F9F9",
                    white: "#FFFFFF",
                },
            },
            dark: {
                colors: {
                    primary: "#FFFFFF",
                    secondary: "#ffed00",
                    background: "#3c3c3c",
                    "background-darken-1": "#3c3c3c",
                    white: "#000000",
                },
            },
        },
    },
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
});

export default vuetify;
