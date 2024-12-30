import { defineStore } from "pinia";
import { useLocalStorage, useStorage } from "@vueuse/core";
import CryptoJS from "crypto-js";
import axios from "axios";
import { axiosToken } from "@/services/axiosToken";
// You can name the return value of `defineStore()` anything you want,
// but it's best to use the name of the store and surround it with `use`
// and `Store` (e.g. `useUserStore`, `useCartStore`, `useProductStore`)
// the first argument is a unique id of the store across your application

const dk = "mel182";

// decrypt data
const decryptData = (data) => {
    return data
        ? JSON.parse(CryptoJS.AES.decrypt(data, dk).toString(CryptoJS.enc.Utf8))
        : null;
};

// encrypt data
const encryptData = (data) => {
    return CryptoJS.AES.encrypt(JSON.stringify(data), dk).toString();
};

const removeCache = () => {
    localStorage.removeItem('current-pg') 
    localStorage.removeItem("authUser");
    localStorage.removeItem("asset-filter-category");
    localStorage.removeItem("transfer-filter-status");
    localStorage.removeItem("asset-filter-ponum");
    localStorage.removeItem("request-filter-company");
    localStorage.removeItem("transfer-filter-company");
    localStorage.removeItem("transfer-filter-from");
    localStorage.removeItem("transfer-filter-location");
    localStorage.removeItem("transfer-filter-row");
    localStorage.removeItem("asset-filter-row");
    localStorage.removeItem("asset-filter-location");
    localStorage.removeItem("request-filter-status");
    localStorage.removeItem("request-filter-location");
    localStorage.removeItem("request-filter-row");
    localStorage.removeItem("request-filter-from");
    localStorage.removeItem("asset-filter-company");
    localStorage.removeItem("asset-filter-status");
    localStorage.removeItem("asset-list-search");
    localStorage.removeItem("request-asset-search");
    localStorage.removeItem("transfer-asset-search");
    localStorage.removeItem("user-search");

    localStorage.removeItem("incident-filter-row");
    localStorage.removeItem("incident-filter-company");
    localStorage.removeItem("incident-filter-location");
    localStorage.removeItem("incident-filter-type");
    localStorage.removeItem("incident-filter-status");
    localStorage.removeItem("incident-search");
   
}

export const useAuthStore = defineStore("authUser", {
    state: () => ({
        auth: useLocalStorage("authUser", {}),
        auth_capabilities: null,
        auth_access: null,
    }),
    getters: {
        user: (state) => {
            return state.auth && state.auth.data
                ? decryptData(state.auth.data).u
                : null;
        },
        token: (state) => {
            return state.auth && state.auth.data
                ? decryptData(state.auth.data).t
                : null;
        },
        authRole: (state) => {
            return state.auth && state.auth.data
                ? decryptData(state.auth.data).r
                : null;
        },
        authIsLoggedIn: (state) => {
            return state.auth && state.auth.data
                ? decryptData(state.auth.data).is_logged_in
                : null;
        },
        access: (state) => {

            return state.auth_access ? state.auth_access : ( state.auth && state.auth.data ? decryptData(state.auth.data).q : null );
        },
        capabilities: (state) => {
            return state.auth_capabilities;
        }
    },
    actions: {
        async setCredentials(res) {
            // save to localstorage
            useStorage(
                "authUser",
                {
                    data: encryptData({
                        u: res.user,
                        t: res.token ? res.token : null,
                        r: res.user.role,
                        is_logged_in: true,
                        q: res.access,
                    }),
                },
                localStorage,
                {
                    mergeDefaults: true,
                }
            );
        }, 

        setCapabilities(path) {
            this.auth_capabilities = null;
            this.auth_capabilities = this.access.filter(
                (o, i) => path == o.slug
            )[0]?.capabilities;
        },

        async checkUser() {
            this.auth_access = null;
            if (this.token) {
                await axiosToken(this.token)
                    .get("/api/checkuser")
                    .then((res) => {

                        // localStorage.removeItem("authUser");
                        if (res.data?.user) {
                            let user = res.data.user.username;
                            let token = res.data.token;

                            axios
                                .get("/api/fetch/log-profile/" + user + "/" + token)
                                .then((q) => {

                                    if (q.data && q.data.id) {

                                        this.auth_access = q.data.access; 

                                        res.data.user.role = q.data.role;
                                        res.data.user.profile = q.data;
                                        res.data.access = q.data.access;
                                        this.setCredentials(res.data);
                                        this.setCapabilities(localStorage.getItem('current-pg'));
                                    }else{
                                        this.logout();
                                        window.location = "/login";
                                    }
                                })
                                .catch((err) => {
                                    this.logout();
                                    window.location = "/login";
                                });
                        }
                    })
                    .catch((err) => {
                        this.logout(); 
                        window.location = "/login";
                    });
            }else{
                this.logout(); 
            }
        },

        async logout() { 
            removeCache(); 
            this.auth = {};
            useLocalStorage("authUser", {}); 
        },
    },
});
