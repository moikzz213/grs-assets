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

export const useAuthStore = defineStore("authUser", {
    state: () => ({
        auth: useLocalStorage("authUser", {}),
        auth_capabilities: null
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
            return state.auth && state.auth.data
                ? decryptData(state.auth.data).q
                : null;
        },
        capabilities: (state) => {
            return state.auth_capabilities;
        }
    },
    actions: {
        setCapabilities(path) {
            this.auth_capabilities = null;
            this.auth_capabilities = this.access.filter(
                (o, i) => path == o.slug
            )[0]?.capabilities;
        },
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

        async checkUser() {
            if (this.token) {
                await axiosToken(this.token)
                    .get("/api/checkuser")
                    .then((res) => {

                        localStorage.removeItem("authUser");
                        if (res.data?.user) {
                            let user = JSON.stringify(res.data.user);
                            let token = res.data.token;

                            axios
                                .get("/api/fetch/log-profile/" + user + "/" + token)
                                .then((q) => {
                                    if (q.data) {

                                        let user_access = q.data.access.map((a) => {
                                            return a;
                                        });

                                        res.data.access = user_access;
                                        res.data.user.role = q.data.role;
                                        res.data.user.profile = q.data;
                                        this.setCredentials(res.data);
                                    }
                                })
                                .catch((err) => {
                                    console.log("errrr 1", err);
                                });
                        }
                    })
                    .catch((err) => {

                        localStorage.removeItem("authUser");
                        window.location = "/login";
                    });

            }

        },
        async logout() {
            this.auth = {};
        },
    },
});
