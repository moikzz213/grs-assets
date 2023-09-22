<template>
    <div v-if="isLoaded">
        <div v-if="authStore.authIsLoggedIn == true">
            <LoggedInLayout>
                <router-view></router-view>
            </LoggedInLayout>
        </div>
        <div v-else>
            <router-view></router-view>
        </div>
    </div>
    <div v-else>
        <v-dialog v-model="dialogLoader" fullscreen>
            <v-card class="rounded-0">
                <v-card-text
                    class="h-100 w-100 d-flex flex-column align-center justify-center"
                >
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        class="mb-3"
                    ></v-progress-circular>
                    <div>Please wait....</div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { axiosToken } from "@/services/axiosToken";
import LoggedInLayout from "@/layouts/LoggedInLayout.vue";
import { useTheme } from "vuetify";
import axios from "axios";
const theme = useTheme();
const authStore = useAuthStore();
let isLoaded = ref(false);
let dialogLoader = ref(true);
// check user
const checkUser = async () => {
    await axiosToken(authStore.token)
        .get("/api/checkuser")
        .then((res) => {
            localStorage.removeItem("authUser");
            if (res.data?.user) {
                let user = JSON.stringify(res.data.user);
                let token = res.data.token;

                axios
                    .get("/api/fetch/log-profile/" + user + "/" + token)
                    .then((q) => {
                   
                        let user_access = q.data.access.map((a) => {
                            return a;
                        });
                        res.data.token = token;
                        res.data.access = user_access;
                        res.data.user.role = q.data.role;
                        
                        authStore.setCredentials(res.data).then(() => {
                            isLoaded.value = true;
                            dialogLoader.value = false;
                        });
                    })
                    .catch((err) => {
                        console.log("errrr 1", err);
                    });
            }
        })
        .catch((err) => {
            isLoaded.value = true;
            dialogLoader = false;
            localStorage.removeItem("authUser");
        });
};
if (
    authStore ||
    authStore.authIsLoggedIn ||
    authStore.authIsLoggedIn == false
) {
    checkUser();
}

// set dark mode on load
const gagDarkTheme = localStorage.getItem("gag_dark_theme");
if (gagDarkTheme) {
    theme.global.name.value = gagDarkTheme;
} else {
    theme.global.name.value = "light";
    localStorage.setItem("gag_dark_theme", theme.global.name.value);
}
</script>
