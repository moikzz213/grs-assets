<template>
    <GuestLayout>
        <div
            class="mx-auto px-3 text-center"
            style="z-index: 1; max-width: 400px; width: 100%; margin-top: 100px"
        >
            <WhiteLogo width="100%" />
            <div class="text-subtitle-1 text-white">{{ appName }}</div>
        </div>

        <v-card
            class="mt-8 pa-3 rounded-lg elevation-3"
            width="90%"
            max-width="450"
        >
            <v-card-title class="px-5 pb-0 primary--text">Login</v-card-title>
            <v-card-text class="py-4">
                <v-form autocomplete="off" ref="form" @keydown.enter="login">
                    <v-text-field
                        v-model="credentials.login"
                        variant="outlined"
                        required
                        class="border-radius"
                        autocomplete="off"
                        label="Enter Employee Code"
                    >
                    </v-text-field>
                    <v-text-field
                        v-model="credentials.password"
                        variant="outlined"
                        required
                        autocomplete="off"
                        label="Enter Password"
                        type="password"
                    >
                    </v-text-field>
                    <v-btn
                        @click="login"
                        width="100%"
                        color="primary"
                        height="55"
                        large
                        :loading="loadingLogin"
                        >Login</v-btn
                    >
                    <v-btn variant="text" class="mt-3" @click="resetPassword"
                        >Reset Password</v-btn
                    >
                    <div class="text-error mt-2">
                        {{ hasError == true ? message : "" }}
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </GuestLayout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { authApi } from "@/services/axiosToken";
import GuestLayout from "../layouts/GuestLayout.vue";
import WhiteLogo from "@/Components/logo/WhiteLogo.vue";

// router
const router = useRouter();

const appName = ref(import.meta.env.VITE_APP_NAME);
const key = ref(import.meta.env.VITE_APP_KEY);
// authStore
const authStore = useAuthStore();
if (authStore.authIsLoggedIn == true) {
    router.push({ path: "/dashboard" });
}

// login
const loadingLogin = ref(false);
const credentials = ref({
    login: "",
    password: "",
    url: key.value,
});

const hasError = ref(false);
const message = ref("");

const login = async () => {
    loadingLogin.value = true;
    authLogin()
        .then((res) => {
            if (!res) {
                return;
            }

            let redirectPath = "/dashboard";
            let user = JSON.stringify(res.data.user);
            let token = res.data.token;

            axios
                .get("/api/fetch/log-profile/" + user + "/" + token)
                .then((q) => {
                    let user_access = q.data.access.map((a) => {
                        return a;
                    });

                    res.data.access = user_access;
                    res.data.user.role = q.data.role;
                    res.data.user.profile = q.data;

                    authStore.setCredentials(res.data).then(() => {
                        window.location = redirectPath;
                    });
                })
                .catch((err) => {
                    console.log("errrr 1", err);
                });
        })
        .catch((err) => {
            console.log(err);
            hasError.value = true;
            message.value = "Enter Username and Password...";
            loadingLogin.value = false;
        });
};

const resetPassword = () => {
    router.push({ path: "reset-password" });
};

// auth login to sanctum
const authLogin = async () => {
    message.value = "";
    let data = {
        username: credentials.value.login,
        password: credentials.value.password,
        url: credentials.value.url,
    };

    const response = await authApi.post("/api/sanctumlogin", data);

    if (response.data.status == false) {
        hasError.value = true;
        message.value = response.data.message;
        loadingLogin.value = false;
        return false;
    }
    return response;
};
</script>
