/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./bootstrap";
import { createApp } from "vue";
const app = createApp({});

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

/**
 * Pinia JS
 */
import { createPinia } from "pinia";
// import piniaPluginPersistedState from "pinia-plugin-persistedstate";
const pinia = createPinia();
// pinia.use(piniaPluginPersistedState);
app.use(pinia);

/**
 * User Store
 */
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

/**
 * Vue Router
 */
import { createRouter, createWebHistory } from "vue-router";
import { routes } from "./router/routes";
const router = createRouter({
    history: createWebHistory(),
    routes,
});

function returnAccess(data) { 
        let hasAccess = false;
        
        authStore.access.map((o, i) => {
            if (data.title == o.slug) {
                hasAccess = true;
                
                if(data.type && data.type == 'edit' ){
                    if(o.capabilities.includes('edit')){
                        hasAccess = true; 
                    }else{
                        hasAccess = false; 
                    }
                }
                
            }
        });
        return hasAccess;
}

function validateAccess(data) {
    let hasAccess = false;
    
    if (
        authStore?.user?.status.toLowerCase() == "active" &&
        authStore?.authRole == "superadmin"
    ) {
        hasAccess = true;
    } else if (data?.title?.toLowerCase() == 'dashboard' || data?.title?.toLowerCase() == 'account') {
        hasAccess = true;
    } else if (
        authStore?.user?.status.toLowerCase() == "active" && returnAccess(data)
    ) {
        hasAccess = true;
    }

    return hasAccess;
}

router.beforeEach((to, from, next) => { 
    if (to.path == '/' && !to.meta.requiresAuth) { 
        // public route
        if (authStore.authIsLoggedIn) {
            next({ name: 'Dashboard' }); 
        } 
       
        next({ name: 'Login' });
    } else {
        
        // private route
        if (authStore.authIsLoggedIn) { 
            if(to.meta.title?.toLowerCase() == 'unauthorized'){

            }else if (!validateAccess(to.meta)) {
                next({ name: 'Unauthorized' });
            } 
        } else if(to.path != '/login') {
            next({ name: 'Login' });
        }
    }
    next();
});

router.afterEach((to, from) => {
    document.title =
        import.meta.env.VITE_APP_NAME + " - " + to.meta.title ||
        import.meta.env.VITE_APP_NAME;
        localStorage.setItem('current-pg', to.meta.title.toLowerCase());
});
app.use(router);

/**
 * Vuetify
 */
import vuetify from "./vuetify/vuetify";
app.use(vuetify);

/**
 * App Component
 */
import App from "./App.vue";
app.component("App", App);

authStore.checkUser().then(() => { 
    app.mount("#app");
});